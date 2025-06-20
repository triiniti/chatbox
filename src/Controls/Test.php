<?php
namespace ChatBox\App\Controls;
use League\Plates\Engine;
use ChatBox\App\Middleware\Csrf as CSRF;
use ChatBox\App\Database;
use ChatBox\App\OAuth;

class Test
{
  private $templates;

  public function __construct()
  {
    $this->templates = new Engine('templates');
  }

  public function renderPage(): string
  {
    $b = OAUTH::get_user_data('');

    $db = new Database();
    $sql = 'SELECT username FROM users';
    $stmt = $db->pdo->prepare($sql);
    $stmt->execute();


    // if ($data = $stmt->fetch()) {
    //   do {
    //     echo $data['username'] . '<br>';
    //   } while ($data = $stmt->fetch());
    // } else {
    //   echo 'Empty Query';
    // }
    $db->pdo;
    $username = 'poljansek';
    $email = '';
    $password = '$2y$10$PcPK5rVO0zaLorNZpXmZO.XPhnoU/6WULAlFfoCtow45wuySpKCFy';
    $sql = "SELECT * FROM users WHERE username = ? AND email = ? AND password = ?";
    $stmt = $db->pdo->prepare($sql);
    $stmt->execute([$username, $email, $password]);
    $user = $stmt->fetch();
    echo $user;
    // Render a template
    return $this->templates->render('test', []);


  }
}

?>