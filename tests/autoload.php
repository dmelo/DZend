<?php

require_once 'vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

echo APPLICATION_PATH . '/configs/application.ini' . PHP_EOL;
$application = new Zend_Application(
    'development',
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
