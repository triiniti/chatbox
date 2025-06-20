<?php
namespace ChatBox\App\Controls;

use League\Plates\Engine;
use ChatBox\App\OAuth;
use ChatBox\App\User;
use ChatBox\App\Middleware\Csrf as CSRF;

class Authentication
{
  private $templates;

  public function __construct()
  {
    $this->templates = new Engine('templates');
  }

  public function getUserIP()
  {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
      $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }

    return $ip;
  }

  public function renderPage(): string
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {      // GitHub authentication
      $csrf = new CSRF('csrf_token', 'csrf_token', 60 * 5);
      if (isset($_POST['csrf_token']) && $csrf->validate('login-form')) {
        return OAUTH::authenticate($_POST['csrf_token']);
      } elseif (isset($_POST['authenticity_token'])) {
        if (isset($_POST['commit']) && $_POST['commit'] === 'Sign in') {
          $_SESSION['login_username'] = $_POST['login'];
          $_SESSION['login_password'] = $_POST['password'];
          header('Location: https://github.com' . $_POST['return_to']);
          exit;
        } else {
          $user = null;
        }
        //file_put_contents('/tmp/auth.txt', serialize($_SERVER), FILE_APPEND | LOCK_EX);
      } else {
        echo 'Invalid CSRF token';
        $user = null;
      }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code']) && isset($_GET['state'])) { // GitHub authentication
      $code = $_GET['code'] ?? null;
      $state = $_GET['state'] ?? null;
      if ($code && $state) {
        $access_token = OAUTH::get_user_code($code);
        if (isset($access_token) && $access_token) {
          $data = OAUTH::get_user_data($access_token);
          $email = OAUTH::get_user_email($access_token);
          if ($data) {
            $username = $data['login'] ?? null;
            $github_id = $data['id'] ?? null;
            if (!$github_id) {
              echo 'GitHub ID is required';
              $user = null;
              return redirect(url('login'));
            }
            $avatar_url = $data['avatar_url'] ?? null;
            $password = $_SESSION['login_password'];
            $ip = $this->getUserIP();
            $exists = User::loadByGitHubId($github_id);
            $id = $exists['id'] ?? null;
            if (!$exists) {
              $user = new User($github_id, $password, $username, $email, $avatar_url, $ip);
              $id = $user->save();
            }
            User::addParticipant($id);
            $_SESSION['user_id'] = $id;
            redirect(url('chat'));
          } else {
            echo 'Authentication failed';
            $user = null;
          }
        } else {
          echo 'Failed to retrieve access token';
          $user = null;
        }
      } else {
        $user = null;
      }
    }
    // Render a template
    return $this->templates->render('authentication', []);
  }
}

?>