<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/12/14
 * Time: 11:00 AM
 */
namespace Contact;

use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_168()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_contact_type` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(200) DEFAULT NULL,
              `contactUserId` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $q .= "CREATE TABLE IF NOT EXISTS `tbl_contact_user` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(300) DEFAULT NULL,
              `email` varchar(300) DEFAULT NULL,
              `mobile` varchar(50) DEFAULT NULL,
              `phone` varchar(50) DEFAULT NULL,
              `address` varchar(500) DEFAULT NULL,
              `catId` int(11) DEFAULT NULL,
              `role` varchar(300) DEFAULT NULL,
              `google` varchar(100) DEFAULT NULL,
              `status` int(11) DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `status` (`status`),
              KEY `catId` (`catId`),
              KEY `name` (`name`(255)),
              KEY `catId_2` (`catId`,`status`),
              KEY `name_2` (`name`(255),`status`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $q .= "ALTER TABLE `tbl_contact_user` ADD INDEX ( `name`) ;";
        $q .= "ALTER TABLE `tbl_contact_user` ADD INDEX (`status`) ;";
        $q .= "ALTER TABLE `tbl_contact_user` ADD INDEX ( `catId` ) ;";
        $q .= "ALTER TABLE `tbl_contact_user` ADD INDEX ( `name` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_contact_user` ADD INDEX ( `catId` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_contact_type` ADD INDEX ( `contactUserId` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_170()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_contact` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `sendId` int(11) DEFAULT '0',
              `name` varchar(200) DEFAULT NULL,
              `email` varchar(300) DEFAULT NULL,
              `mobile` varchar(50) DEFAULT NULL,
              `description` text,
              `typeContact` int(11) DEFAULT '0',
              `date` int(11) DEFAULT NULL,
              `status` tinyint(4) DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();
    }

    public function update_177()
    {
        $q = "ALTER TABLE `tbl_contact_user` ADD `fax` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `phone` ;";
        $this->db->query($q)->execute();
    }

    public function update_179()
    {
        $q = "ALTER TABLE `tbl_contact_user` ADD `viewStatus` TINYINT NULL DEFAULT '0',ADD `description` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  ;";
        $this->db->query($q)->execute();
    }

    public function update_181()
    {
        $q = "ALTER TABLE `tbl_contact_user` CHANGE `viewStatus` `type` TINYINT( 4 ) NULL DEFAULT '0';";
        $this->db->query($q)->execute();
    }

    public function update_185()
    {
        $q = "ALTER TABLE `tbl_contact_user` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
    }

    public function update_189()
    {
        $q = "ALTER TABLE `tbl_contact_user` ADD `showEmail` TINYINT NULL DEFAULT '0';";
        $this->db->query($q)->execute();
    }

 public function update_1915()
    {
        $q = "ALTER TABLE `tbl_contact_user` ADD `smsNumber` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `mobile` ;";
        $this->db->query($q)->execute();
    }

}