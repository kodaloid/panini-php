<?php



if (!defined('__ROOT__')) die('Access denied');



/**
 * The app/app.php script is your app's main entry point after everything is
 * loaded. Here is a good place to setup a database connection, include files
 * that contain custom code you've written etc...
 */



$db = $this->load_module(
    'database',
    'MySQL_Database',
    [ DB_HOST, DB_NAME, DB_USER, DB_PASS ]
);