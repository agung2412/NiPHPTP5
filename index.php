<?php
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
	die('require PHP >= 5.5.0 !');
}

define('APP_DEBUG', false);
define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
define('CONF_PATH', APP_PATH . 'config' . DIRECTORY_SEPARATOR);
define('EXTEND_PATH', APP_PATH . 'extend' . DIRECTORY_SEPARATOR);
require __DIR__ . '/thinkphp/start.php';