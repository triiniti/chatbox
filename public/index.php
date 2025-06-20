<?php
// Include the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';
require_once '../src/helpers.php';
use Pecee\SimpleRouter\SimpleRouter;
if (!isset($_SESSION)) {
  session_start();
}
/* Load external routes file */
require_once '../libraries/routes.php';

/**
 * The default namespace for route-callbacks, so we don't have to specify it each time.
 * Can be overwritten by using the namespace config option on your routes.
 */

SimpleRouter::setDefaultNamespace('ChatBox\App\Controls');

// Start the routing
SimpleRouter::start();
?>