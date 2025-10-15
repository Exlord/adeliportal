<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 9:46 AM
 */
return array(
    'mail_db' => array(
        'driver' => 'Pdo_Mysql',
        'database' => 'iptcms_mail',
        'hostname' => 'localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'root',
        'password' => '',
    ),
);