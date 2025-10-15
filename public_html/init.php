<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 10/30/13
 * Time: 2:09 PM
 */
define('ROOT', dirname(__DIR__));
define('PUBLIC_PATH', dirname(__FILE__));

defined('DOMAIN') || define('DOMAIN', str_replace('www.', '', $_SERVER['HTTP_HOST']));
if (!defined('ACTIVE_SITE')) {
    $sites = include ROOT . '/config/sites.php';
    $active_site = false;
    if (isset($sites[DOMAIN]))
        $active_site = $sites[DOMAIN];
    elseif (isset($sites['default']))
        $active_site = $sites['default'];
    elseif (count($sites))
        $active_site = current($sites);
    else
        die('Invalid domain.There is no configuration defined for this domain');

    define('ACTIVE_SITE', $active_site);
}
define('PUBLIC_FILE_MANAGER_PATH', '/clients/' . ACTIVE_SITE . '/files/');
define('PUBLIC_FILE', ROOT . '/public_html/clients/' . ACTIVE_SITE . '/files');
define('PUBLIC_FILE_PATH', '/clients/' . ACTIVE_SITE . '/files');
define('PRIVATE_FILE', ROOT . '/data/' . ACTIVE_SITE . '/files');
define('IS_DEVELOPMENT_SERVER', file_exists(ROOT . '/local'));

//include ROOT . '/vendor/geoip/geoip.inc';
//include ROOT . '/vendor/geoip/geoipcity.inc';
include ROOT . '/library/functions.php';