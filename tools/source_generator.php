<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/14/13
 * Time: 9:22 AM
 */
set_time_limit(0);
define('ROOT', str_replace('\\', '/', dirname(__DIR__)));
define("MODULE_DIR", ROOT . '/module');
define("SOURCE_DIR", ROOT . '/source');
define("ARCHIVE_DIR", SOURCE_DIR . '/archive');
define("LANG_DIR", ROOT . '/language');
chdir(ROOT);
include ROOT . '/init_autoloader.php';

include_once MODULE_DIR . '/System/src/System/IO/Directory.php';
include_once MODULE_DIR . '/System/src/System/Filter/Compress/ExZip.php';

if (!is_dir(SOURCE_DIR . '/core'))
    mkdir(SOURCE_DIR . '/core', 0755, true);
if (!is_dir(SOURCE_DIR . '/module'))
    mkdir(SOURCE_DIR . '/module', 0755, true);
if (!is_dir(SOURCE_DIR . '/language'))
    mkdir(SOURCE_DIR . '/language', 0755, true);


$updateInfo = new stdClass();
if (file_exists(SOURCE_DIR . '/update-info'))
    $updateInfo = json_decode(file_get_contents(SOURCE_DIR . '/update-info'));


$modules = \System\IO\Directory::getDirs(MODULE_DIR, false, array('Sample'));
foreach ($modules as $m) {
    $ini = parse_ini_file(MODULE_DIR . '/' . $m . '/module.ini');
    $version = isset($ini['version']) ? $ini['version'] : '1.0';
    $filename = $m . '-' . $version . '-source.zip';
    $path = SOURCE_DIR . '/module/' . $filename;
    $archivePath = SOURCE_DIR . '/archive/module/' . $filename;
    $ignore = null;
    if ($m == 'Theme') {
        $ignore = array(
            'dir' => array(
                MODULE_DIR . '/Theme/public/themes/'
            ),
            'exception' => array(
                MODULE_DIR . '/Theme/public/themes/' => array(
                    'default',
                    'default2',
                    'defaultAdmin',
                    'IptCmsAdmin',
                )
            )
        );
    }
    $md5_file = false;
    if (!file_exists($path) && !file_exists($archivePath)) {
        $filter = new \Zend\Filter\Compress(array(
            'adapter' => 'System\Filter\Compress\ExZip',
            'options' => array(
                'archive' => $path,
                'ignore' => $ignore

            ),
        ));
        $file = $filter->filter(MODULE_DIR . '/' . $m);
        echo($file . "<br/>");
        $md5_file = md5_file($path);
    } else {
        if (file_exists($archivePath))
            $md5_file = md5_file($archivePath);
        else
            $md5_file = md5_file($path);
    }

    $updateInfo->module->{$m} = array('version' => $version, 'hash' => $md5_file);
}

$systemVersion = parse_ini_file(ROOT . '/system.ini', true);
$version = $systemVersion['version'];
$zendVersion = \Zend\Version\Version::VERSION;

//--------------------- CORE ------------------------------
$filename = 'core-' . $version . '-source.zip';
$path = SOURCE_DIR . '/core/' . $filename;
$archivePath = SOURCE_DIR . '/archive/core/' . $filename;
$md5_file = false;
if (!file_exists($path) && !file_exists($archivePath)) {
    $filter = new \Zend\Filter\Compress(array(
        'adapter' => 'System\Filter\Compress\ExZip',
        'options' => array(
            'archive' => $path,
            'ignore' => array(
                'self' => true,
                'dir' => array(
                    ROOT . '/',
                    ROOT . '/config/',
                    ROOT . '/config/autoload/',
                    ROOT . '/data/',
                    ROOT . '/library/',
                    ROOT . '/public_html/'
                ),
                'exception' => array(
                    ROOT . '/' => array(
                        'config',
                        'library',
                        'public_html',
                        'data',
                        'init_autoloader.php',
                        'system.ini'
                    ),
                    ROOT . '/data/' => array(
                        'classes.php.cache'
                    ),
                    ROOT . '/config/' => array(
                        'application.config.php',
                        'autoload'
                    ),
                    ROOT . '/config/autoload/' => array(
                        'ipt24-ir-email.global.php'
                    ),
                    ROOT . '/library/' => array(
                        'functions.php',
                        'Updater.php'
                    ),
                    ROOT . '/public_html/' => array(
                        'fonts',
                        'lib',
                        '.htaccess',
                        '503.php',
                        'cron.php',
                        'index.php',
                        'init.php',
                        'robots.txt',
                        'update.php'
                    ),
                )
            )

        ),
    ));
    $file = $filter->filter(ROOT);
    echo($file . "<br/>");
    $md5_file = md5_file($path);
} else {
    if (file_exists($archivePath))
        $md5_file = md5_file($archivePath);
    else
        $md5_file = md5_file($path);
}

$updateInfo->core->system = array('version' => $version, 'hash' => $md5_file);
//--------------------- CORE ------------------------------

//--------------------- ZEND ------------------------------
$filename = 'zend-' . $zendVersion . '-source.zip';
$path = SOURCE_DIR . '/core/' . $filename;
$archivePath = SOURCE_DIR . '/archive/core/' . $filename;
$md5_file = false;
if (!file_exists($path) && !file_exists($archivePath)) {
    $filter = new \Zend\Filter\Compress(array(
        'adapter' => 'System\Filter\Compress\ExZip',
        'options' => array(
            'archive' => $path,
        ),
    ));
    $file = $filter->filter(ROOT . '/vendor');
    echo($file . "<br/>");
    $md5_file = md5_file($path);
} else {
    if (file_exists($archivePath))
        $md5_file = md5_file($archivePath);
    else
        $md5_file = md5_file($path);
}

$updateInfo->core->zend = array('version' => $zendVersion, 'hash' => $md5_file);
//--------------------- ZEND ------------------------------

//--------------------- LANGS ------------------------------
$langs = \System\IO\Directory::getDirs(LANG_DIR);
foreach ($langs as $l) {
    $ini = parse_ini_file(LANG_DIR . '/' . $l . '/lang.ini');
    $version = $ini['version'];
    $filename = $l . '-' . $version . '-source.zip';
    $path = SOURCE_DIR . '/language/' . $filename;
    $archivePath = SOURCE_DIR . '/archive/language/' . $filename;
    $md5_file = false;
    if (!file_exists($path) && !file_exists($archivePath)) {
        $filter = new \Zend\Filter\Compress(array(
            'adapter' => 'System\Filter\Compress\ExZip',
            'options' => array(
                'archive' => $path,
                'ignore' => array(
                    'file' => array(
                        'miscellaneous.lang'
                    )
                )
            ),
        ));
        $file = $filter->filter(LANG_DIR . '/' . $l);
        echo($file . "<br/>");
        $md5_file = md5_file($path);
    } else {
        if (file_exists($archivePath))
            $md5_file = md5_file($archivePath);
        else
            $md5_file = md5_file($path);
    }

    $updateInfo->language->{$l} = array('version' => $version, 'hash' => $md5_file);
}
//--------------------- LANGS ------------------------------

file_put_contents(SOURCE_DIR . '/update-info', json_encode($updateInfo));