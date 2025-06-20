<?php
namespace ChatBox\App\Controls;
use League\Plates\Engine;
use ChatBox\App\Middleware\Csrf as CSRF;
use ChatBox\App\Message as Message;

class Chat
{
    private $templates;

    public function __construct()
    {
        $this->templates = new Engine('templates');
    }

    public function renderPage(): string
    {
        $csrf = new CSRF('csrf_token', 'csrf_token', 60 * 5);
        $token = $csrf->input('chatbox-form');
        $m = new Message();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $csrf->validate('chatbox-form')) {
            $count = $m->count();
            $lastSender = $m->lastSenderId();
            $pos = ($lastSender == $_SESSION['user_id']) ? 'left' : 'right';
            //$pos = ($count % 2) == 0 ? 'left' : 'right';
            $m = new Message($_POST['message'], $_SESSION['user_id'], date('Y-m-d H:i:s'), $pos);
            $m->save();
        }
        $messages = $m->loadAll();
        // Render a template
        return $this->templates->render('chat', ['csrf_token' => $token, 'messages' => $messages]);
    }
}


?>