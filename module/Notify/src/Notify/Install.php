<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 12:56 PM
 */

namespace Notify;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_12()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_notify` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `uId` int(11) NOT NULL,
                  `msg` text NOT NULL,
                  `date` int(11) NOT NULL,
                  `status` tinyint(1) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `uid` (`uId`),
                  KEY `uId_2` (`uId`,`status`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";
        $result = $this->db->query($q)->execute();
        if (!$result) {
            $q = "alter table `tbl_notify` MODIFY COLUMN `msg` text NOT NULL";
            $this->db->query($q)->execute();

            $q = "ALTER TABLE `tbl_notify` ADD INDEX (`uId`,`status`) ;";
            $this->db->query($q)->execute();
        }
    }
} 