<?php
/**
 * PaniniPHP - Version: 1.0 (Build 0002) [alpha]
 * A compact, Composer friendly PHP framework for building web apps.
 */



// define site root path.
define('__ROOT__', __DIR__);



// define the app path.
define('__APP__', __DIR__ . '\app');



// includes & autoloaders.
require_once __ROOT__ . '\config.php';
require_once __ROOT__ . '\vendor\autoload.php';
require_once __ROOT__ . '\system\App.php';
require_once __ROOT__ . '\system\Helper.php';
require_once __ROOT__ . '\system\RequestHandler.php';



// Get an app instance to initialize.
$app = App\App::get_instance();



// initialize the app, loading twig etc...
$app->initialize(__APP__ . '\\views');



// ask the app to locate and execute controller.
$app->run();