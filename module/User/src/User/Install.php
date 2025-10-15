<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 12:34 PM
 */

namespace User;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function initialize()
    {
//        parent::initialize();

        $perm = array(
            'perms' => array(
                '1' => array(
                    'route:admin' => 2,
                    'route:app' => 1,
                ),
                '2' => array(
                    'route:admin' => 2,
                    'route:app' => 1,
                ),
                '4' => array(
                    'route:admin' => 1,
                    'route:app' => 1,
                ),
                '3' => array(
                    'route:admin' => 1,
                    'route:app' => 1,
                )
            )
        );

        $q = "INSERT INTO `tbl_users` (`username`, `password`, `accountStatus`, `displayName`) VALUES
                ('serverAdmin',?,1,'serverAdmin'),
                ('admin',?,1,'admin');";
        $this->db->query($q, array($this->params['admin-password'], $this->params['user-password']));

        $q = "INSERT INTO `tbl_config` (`varName`, `varDisplayName`, `varValue`, `varGroup`) VALUES
                ('permissions', NULL, ?, NULL);";
        $this->db->query($q)->execute(array(serialize($perm)));
    }

    public function update_254(){

        $q = "ALTER TABLE `tbl_roles` ADD INDEX ( `parentId` ) ;";
        $q .= "ALTER TABLE `tbl_roles` ADD INDEX ( `level` ) ;";
        $q .= "ALTER TABLE `tbl_roles` ADD INDEX ( `parentId` , `level` ) ;";
        $q .= "ALTER TABLE `tbl_roles` ADD INDEX ( `roleName` ) ;";
        $q .= "ALTER TABLE `tbl_user_profile` ADD INDEX ( `userId` ) ;";
        $q .= "ALTER TABLE `tbl_users_roles` ADD INDEX (`roleId` ) ;";
        $q .= "ALTER TABLE `tbl_users_roles` ADD INDEX ( `userId` ) ;";
        $q .= "ALTER TABLE `tbl_users_roles` ADD INDEX ( `userId` , `roleId` ) ;";
        $this->db->query($q)->execute();

        $q .= "ALTER TABLE `tbl_users` ADD INDEX ( `password` ) ;";
        $q .= "ALTER TABLE `tbl_users` ADD INDEX ( `username` , `password` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_310(){
        $q = "ALTER TABLE `tbl_user_profile` ADD `birthDate` INT( 11 ) NOT NULL AFTER `lastName`;";
        $this->db->query($q)->execute();
    }

    public function update_320(){
        $q = "ALTER TABLE `tbl_users` ADD `data` TEXT";
        $this->db->query($q)->execute();
    }

    public function update_330(){
        $q = "CREATE TABLE IF NOT EXISTS `tbl_user_flood` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `ip` varchar(15) NOT NULL,
                  `timestamp` int(11) NOT NULL,
                  `username` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `ip` (`ip`,`timestamp`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='failed login attempts' ;";
        $this->db->query($q)->execute();
    }
}