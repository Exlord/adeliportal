<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/3/14
 * Time: 10:51 AM
 */
// Disable buffering
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('output_buffering', 'Off');
@ini_set('implicit_flush', 1);
// Flush buffers
ob_implicit_flush(1);
for ($i = 0, $level = ob_get_level(); $i < $level; $i++) ob_end_flush();

echo str_repeat(" ", 1024), "\n";
ob_start();

set_time_limit(0);
define('ROOT', str_replace('\\', '/', dirname(__DIR__)));
chdir(ROOT);
include ROOT . '/init_autoloader.php';

$routes = array(
    0 => '/fa/admin/configs',
    1 => '/fa/admin/configs/captcha',
    2 => '/fa/admin/configs/system',
    3 => '/fa/admin/configs/widgets',
    4 => '/fa/admin/users/config',
    5 => '/fa/admin/users/config/more',
    6 => '/fa/admin/page-config',
    7 => '/fa/admin/real-estate/config',
    8 => '/fa/admin/real-estate/config/more',
    9 => '/fa/admin/sms/config',
    10 => '/fa/admin/banner/configs',
    11 => '/fa/admin/gallery/configs',
    12 => '/fa/admin/comment/config',
    13 => '/fa/admin/configs/forms',
    14 => '/fa/admin/configs/mail',
    15 => '/fa/admin/news-letter/config',
    16 => '/fa/admin/notifications/config',
    17 => '/fa/admin/notifications/config/advance',
    18 => '/fa/admin/contact/config',
    19 => '/fa/admin/configs/analyzer',
    20 => '/fa/admin/content-sharing/config',
    21 => '/fa/admin/simple-order/config',
    22 => '/fa/admin',
    23 => '/fa/admin',
    24 => '/fa/admin/updates',
    25 => '/fa/admin/optimization',
    26 => '/fa/admin/cache',
    27 => '/fa/admin/backup',
    28 => '/fa/admin/backup/db',
    29 => '/fa/admin/template',
    30 => '/fa/admin/template/new',
    31 => '/fa/admin/themes',
    32 => '/fa/admin/themes/config',
    33 => '/fa/admin/contents',
    34 => '/fa/admin/links',
    35 => '/fa/admin/page',
    36 => '/fa/admin/content',
    37 => '/fa/admin/geographical-areas',
    38 => '/fa/admin/geographical-areas/country',
    39 => '/fa/admin/geographical-areas/state',
    40 => '/fa/admin/geographical-areas/city',
    41 => '/fa/admin/geographical-areas/area',
    42 => '/fa/admin/file-types/public',
    43 => '/fa/admin/phone-book',
    44 => '/fa/admin/banner',
    45 => '/fa/admin/banner/groups',
    46 => '/fa/admin/banner/item',
    47 => '/fa/admin/slider',
    48 => '/fa/admin/slider/groups',
    49 => '/fa/admin/slider/item',
    50 => '/fa/admin/gallery',
    51 => '/fa/admin/gallery/groups',
    52 => '/fa/admin/gallery/item',
    53 => '/fa/admin/imageBox',
    54 => '/fa/admin/imageBox/groups',
    55 => '/fa/admin/imageBox/item',
    56 => '/fa/admin/comment',
    57 => '/fa/admin/contact',
    58 => '/fa/admin/contact/user',
    59 => '/fa/admin/contact/contacts',
    60 => '/fa/admin/modules',
    61 => '/fa/admin/alias',
    62 => '/fa/admin/alias/new',
    63 => '/fa/admin/translation',
    64 => '/fa/admin/sms/send-sms',
    65 => '/fa/admin/rss-reader',
    66 => '/fa/admin/rss-reader/new',
    67 => '/fa/admin/mail',
    68 => '/fa/admin/mail/archive',
    69 => '/fa/admin/structure',
    70 => '/fa/admin/category',
    71 => '/fa/admin/category/new',
    72 => '/fa/admin/block',
    73 => '/fa/admin/block/new',
    74 => '/fa/admin/fields',
    75 => '/fa/admin/languages',
    76 => '/fa/admin/menu',
    77 => '/fa/admin/menu/new',
    78 => '/fa/admin/payment/bank-info',
    79 => '/fa/admin/forms',
    80 => '/fa/admin/forms/new',
    81 => '/fa/admin/news-letter',
    82 => '/fa/admin/news-letter/send',
    83 => '/fa/admin/domain',
    84 => '/fa/admin/domain/new',
    85 => '/fa/admin/orders',
    86 => '/fa/admin/banner/list',
    87 => '/fa/admin/simple-order',
    88 => '/fa/admin/reports',
    89 => '/fa/admin/reports/logs',
    90 => '/fa/admin/reports/logs?grid_filter_priority=4',
    91 => '/fa/admin/reports/logs?grid_filter_priority=6',
    92 => '/fa/admin/payment/my-payments',
    93 => '/fa/admin/reports/analyzer',
    94 => '/fa/admin/help',
    95 => '/fa/admin/users',
    96 => '/fa/admin/users',
    97 => '/fa/admin/users/new',
    98 => '/fa/admin/users/role',
    99 => '/fa/admin/users/role/new',
    100 => '/fa/admin/users/permission',
    101 => '/fa/admin/users/config',
    102 => '/fa/admin/users/config/more',
    103 => '/fa/admin/real-estate',
    104 => '/fa/admin/real-estate',
    105 => '/fa/admin/real-estate?grid_filter_status=1',
    106 => '/fa/admin/real-estate?grid_filter_status=0',
    107 => '/fa/admin/real-estate?grid_filter_isRequest=1',
    108 => '/fa/admin/real-estate?grid_filter_expire=0',
    109 => '/fa/admin/real-estate?grid_filter_isSpecial=1',
    110 => '/fa/admin/real-estate?grid_filter_status=3',
    111 => '/fa/admin/real-estate?grid_filter_status=4',
    112 => '/fa/admin/real-estate?grid_filter_status=2',
    113 => '/fa/admin/real-estate?grid_filter_status=5',
    114 => '/fa/admin/real-estate/new-transfer',
    115 => '/fa/admin/real-estate/new-request',
    116 => '/fa/admin/users?grid_filter_roleId=11',
    117 => '/fa/admin/real-estate/agent-area',
    118 => '/fa/admin/real-estate/config',
    119 => '/fa/admin/online-order/orders',
    120 => '/fa/admin/online-order/orders',
    121 => '/fa/admin/online-order/sub-domains',
    122 => '/fa/admin/online-order/config',
);
$site = 'http://iptcms';

$request = new \Zend\Http\Request();
$request->setUri($site . '/fa/user/login');
$request->setMethod('POST');
$request->getPost()->set('username', 'developer');
$request->getPost()->set('password', '123456');

$client = new \Zend\Http\Client();
$client->setEncType('application/x-www-form-urlencoded');

$response = false;
try {
    /* @var $response \Zend\Http\Response */
    $response = $client->dispatch($request);
    echo('Logged in' . "<br/>");
} catch (Exception $e) {
    var_dump($e->getMessage());
    if ($e->getPrevious())
        var_dump($e->getPrevious()->getMessage());
}

$start = isset($_GET['start']) ? $_GET['start'] : 0;
for ($i = $start; $i < $start + count($routes) - $start; $i++) {
    $request->setUri($site . $routes[$i] . '?EDPSUPERLUMINAL_CACHE');
    $request->setMethod('GET');

    $client->dispatch($request);
    echo($routes[$i] . "<br/>");
    ob_flush();
    flush();
}