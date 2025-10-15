<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
//$start_time = microtime();
date_default_timezone_set('Asia/Tehran');
include 'init.php';

//if (IS_DEVELOPMENT_SERVER) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
//}
chdir(ROOT);
try {
//    define('ZF_CLASS_CACHE', 'data/classes.php.cache');
//    if (file_exists(ZF_CLASS_CACHE)) require_once ZF_CLASS_CACHE;
// Setup autoloading

    include ROOT . '/init_autoloader.php';
// Run the application!

    Zend\Mvc\Application::init(include 'config/application.config.php')->run();
} catch (Exception $e) {
    if (IS_DEVELOPMENT_SERVER)
        throw $e;
    else {
        echo('Error : ' . $e->getCode() . " " . $e->getMessage());
        throw $e;
    }
}

//$end_time = microtime();
//var_dump($end_time - $start_time);