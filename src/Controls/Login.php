<?php
namespace ChatBox\App\Controls;

use League\Plates\Engine;
use ChatBox\App\Middleware\Csrf as CSRF;
use ChatBox\App\Database;
use ChatBox\App\OAuth;

class Login
{
    private $templates;

    public function __construct()
    {
        $this->templates = new Engine('templates');
    }

    public function renderPage(): string
    {
        $csrf = new CSRF('csrf_token', 'csrf_token', 60 * 5);
        $token = $csrf->input('login-form');
        if (isset($_SESSION['user_id'])) {
            redirect(url('chat'));
        }

        // Render the login form
        return $this->templates->render('login', ['csrf_token' => $token]);
    }
}


?>