<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define(
        'APPLICATION_PATH', realpath(dirname(__FILE__) . '/../Application')
);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', 'testing');

set_include_path(get_include_path() . PATH_SEPARATOR . '../../');


require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    array(
        'config' => APPLICATION_PATH . '/../tests/application.ini',
        'bootstrap' => array(
            'path' => 'DZend/Application/Bootstrap/Bootstrap.php',
            'class' => 'DZend_Application_Bootstrap_Bootstrap'
        )
    )
);
$application->bootstrap();
