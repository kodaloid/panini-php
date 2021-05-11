<?php



if (!defined('__ROOT__')) die('Access denied');



// Localization.
date_default_timezone_set('Europe/London');



// PHP Settings.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Global Variables.
define('SITE_URL', 'http://localhost/panini-php/');
define('DB_NAME', 'panini');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8_unicode_ci');
define('DB_DATE_FORMAT', 'Y-m-d H:i:s');



// Max search depth for controllers / veiws.
define('MAX_ROOT_DEPTH', 5);