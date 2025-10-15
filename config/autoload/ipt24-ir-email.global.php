<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 9:28 AM
 */
if (!IS_DEVELOPMENT_SERVER)
    return array(
        'mail_db' => array(
            'driver' => 'Pdo_Mysql',
            'database' => 'ipt24ir_mail',
            'hostname' => 'localhost',
            'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ),
            'username' => 'ipt24ir_mail',
            'password' => '',
        ),
    );
else
    return array();
