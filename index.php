<?php

/* Only show warnings and errors */
// ini_set('error_reporting', 'E_ERROR | E_WARNING');
// ini_set('display_errors', '1');

require_once 'system/Loader.php';

use \Milkshake\Core\Loader;
use \Milkshake\Core\Router;
use \Milkshake\Settings;
use \Milkshake\Controller;

/* Load dependencies */
Loader::init();

/* Set timezone */
date_default_timezone_set(Settings::get('TIMEZONE'));

/* Get request */
$request = $_SERVER['REQUEST_URI'];

/* Route matchiung */
$target = (new Router)->load($request);

/* Prepare method call */
$class = 'Milkshake\Controller\\'.$target['controller'];
$method = $target['function'];

/* Return result */
if (isset($target['data'])) {
	echo (new $class())->$method($target['data']);
} else {
	echo (new $class())->$method();
}

?>
