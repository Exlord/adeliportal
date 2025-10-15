<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/8/14
 * Time: 10:09 AM
 */

namespace File;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_123()
    {
        $q = "ALTER TABLE `tbl_file` ADD INDEX ( `entityId` ) ;";
        $q .= "ALTER TABLE `tbl_file` ADD INDEX ( `entityType`) ;";
        $q .= "ALTER TABLE `tbl_file` ADD INDEX ( `fPath`) ;";
        $q .= "ALTER TABLE `tbl_file` ADD INDEX ( `entityType` , `entityId` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_1211()
    {
        $q = "ALTER TABLE `tbl_file` ADD `fileType` varchar(300) CHARACTER SET utf8 NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
    }

    public function update_13()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_file_private` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `title` varchar(255) NOT NULL,
              `downloadAs` varchar(255) NOT NULL,
              `path` varchar(255) NOT NULL,
              `accessibility` text,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='a list of private files' AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();
    }

    public function update_131()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_file_private_usage` (
              `fileId` int(11) NOT NULL,
              `entityType` varchar(255) NOT NULL,
              `entityId` int(11) NOT NULL,
              UNIQUE KEY `fileId` (`fileId`,`entityType`,`entityId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();
    }
} 