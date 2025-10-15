<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 12:40 PM
 */

namespace Components;


use System\DB\BaseInstall;

class Install extends BaseInstall
{

    public function update_12()
    {
        $q = "alter table `tbl_blocks` add column  `locked` tinyint(1) DEFAULT '0';";
        $this->db->query($q)->execute();
    }

//    public function update_13()
//    {
//        $q = "alter table `tbl_blocks` add column `domainVisibility` tinyint(1) NOT NULL DEFAULT '0'";
//        $this->db->query($q)->execute();
//        $q = "alter table `tbl_blocks` add column `domains` text NOT NULL";
//        $this->db->query($q)->execute();
//    }

//    public function update_14()
//    {
//        $q = "alter table `tbl_blocks` MODIFY column `domains` text";
//        $this->db->query($q)->execute();
//    }

    public function update_16()
    {
        //`order` int(11) NOT NULL DEFAULT '0',
        $q = "alter table `tbl_blocks` add column `order` int(11) NOT NULL DEFAULT '0'";
        $this->db->query($q)->execute();
    }


    public function update_169()
    {
        $q = "ALTER TABLE `tbl_blocks` ADD INDEX ( `enabled` ) ;";
        $q .= "ALTER TABLE `tbl_blocks` ADD INDEX ( `position` ) ;";
        $q .= "ALTER TABLE `tbl_blocks` ADD INDEX ( `locked` ) ;";
        $q .= "ALTER TABLE `tbl_blocks` ADD INDEX ( `type` ) ;";
        $q .= "ALTER TABLE `tbl_blocks` ADD INDEX ( `type` ,  `enabled` , `locked`) ;";
        $q .= "ALTER TABLE `tbl_blocks` ADD INDEX ( `type` ,  `enabled`) ;";
        $q .= "ALTER TABLE `tbl_blocks` ADD INDEX ( `position` ,  `enabled`) ;";
        $this->db->query($q)->execute();
    }

    public function update_176()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_blocks_per_url` (
                  `url` varchar(255) NOT NULL,
                  `blocks` text NOT NULL,
                  PRIMARY KEY (`url`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();
    }

    public function update_179(){
        $q = "ALTER TABLE `tbl_blocks_per_url` DROP PRIMARY KEY";
        $this->db->query($q)->execute();

        $q = "ALTER TABLE `tbl_blocks_per_url` CHANGE `url` `url` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
        $this->db->query($q)->execute();

        $q = "ALTER TABLE `tbl_blocks_per_url` ADD INDEX `url` ( `url` ( 500 ) ) ";
        $this->db->query($q)->execute();
    }

    public function update_180(){
        $q = "ALTER TABLE `tbl_blocks` DROP `expire` ;";
        $this->db->query($q)->execute();
    }

    public function update_182(){
        $q = "ALTER TABLE `tbl_blocks` DROP `domains` ;";
        $this->db->query($q)->execute();

        $q = "ALTER TABLE `tbl_blocks` DROP `domainVisibility` ;";
        $this->db->query($q)->execute();
    }
}