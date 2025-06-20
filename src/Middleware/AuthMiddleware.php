<?php
namespace ChatBox\App\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use ChatBox\App\User as User;

class AuthMiddleware implements IMiddleware
{

    public function handle(Request $request): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['chat_token'])) {
            if (isset($_SESSION['user_id'])) {
                $u = new User();
                $user = $u->loadById($_SESSION['user_id']);
                if ($user) {
                    $request->user = $user;
                    $_SESSION['chat_token'] = $user['token'];
                } else {
                    $request->user = null;
                }
            } else {
                $request->user = null;
            }

            // If authentication failed, redirect request to user-login page.
            if ($request->user === null) {
                redirect(url('login'));
            }
        }

    }
}
?>