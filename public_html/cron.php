<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/23/13
 * Time: 10:34 AM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (isset($argv[1])){
    define('ACTIVE_SITE', $argv[1]);
}
include 'init.php';
chdir(ROOT);
include 'init_autoloader.php';
Zend\Mvc\Application::init(include 'config/application.config.php')->run();