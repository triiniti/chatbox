<?php
namespace Routes;

use ChatBox\App\User;
use Pecee\SimpleRouter\SimpleRouter as Router;
use Pecee\Http\Request;

#Does not work
#Router::csrfVerifier(new ChatBox\App\Middleware\CsrfVerifier());


Router::get('/test', 'Test@renderPage')->name('test');
Router::post('/test', 'Test@renderPage')->name('test');
# GitHub authentication routes
Router::get('/identicons/app/oauth_app/3011585/', 'Authentication@renderPage')->name('authentication');
Router::get('/u2f/login_fragment/', 'Authentication@renderPage')->name('authentication');
Router::post('/authenticate', 'Authentication@renderPage')->name('authentication');
Router::get('/authenticate/', 'Authentication@renderPage')->name('authentication');
Router::get('/authenticate', 'Authentication@renderPage')->name('authentication');
Router::post('/session', 'Authentication@renderPage')->name('authentication');

Router::get('/', 'Login@renderPage')->name('login');
Router::post('/', 'Login@renderPage')->name('login');
Router::group(['middleware' => \ChatBox\App\Middleware\AuthMiddleware::class], function () {
    Router::get('/history', 'History@renderPage')->name('history');
    Router::get('/chat', 'Chat@renderPage')->name('chat');
    Router::post('/chat', 'Chat@renderPage')->name('chat');
});
Router::get('/not-found', 'Errors@renderPage')->name('not-found');
Router::get('/logout', function () {    // Logout user   
    if (isset($_SESSION['user_id'])) {
        User::removeParticipant($_SESSION['user_id']);
    };
    session_unset();
    session_destroy();
    // Redirect to login page
    response()->redirect(url('login'));
})->name('logout');


Router::error(function (Request $request, \Exception $exception) {
    switch ($exception->getCode()) {
        // Page not found
        case 404:
            //$request->setRewriteCallback('Errors@renderPage');
            response()->redirect('/not-found');
        // Forbidden
        case 403:
            //$request->setRewriteCallback('Errors@renderPage');
            response()->redirect('/not-found');
    }
});

?>