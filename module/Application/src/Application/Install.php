<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/21/14
 * Time: 2:47 PM
 */

namespace Application;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_187(){
        $q = "CREATE TABLE IF NOT EXISTS `tbl_template` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `title` VARCHAR(200) NOT NULL,
                  `format` TEXT NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";
        $this->db->query($q)->execute();
    }

    public function update_209(){
        $q = "ALTER TABLE `tbl_modules` ADD UNIQUE (`name`)";
        $this->db->query($q)->execute();
    }

    public function update_213(){
        $q = "CREATE TABLE IF NOT EXISTS `tbl_cache_url` (
              `url` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
              `matchedRoute` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "ALTER TABLE `tbl_cache_url` ADD INDEX `url` ( `url` ( 500 ) );";
        $this->db->query($q)->execute();
    }

    public function update_214(){
        $q = "CREATE TABLE IF NOT EXISTS `tbl_alias_url` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `url` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
              `alias` varchar(500) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();

        $q = "ALTER TABLE `tbl_alias_url` ADD INDEX `alias` ( `alias` ( 500 ) );";
        $this->db->query($q)->execute();
    }

    public function update_3225(){
        $q = "ALTER TABLE `tbl_db_backup` CHANGE `size` `size` VARCHAR( 25 ) NOT NULL DEFAULT '0' COMMENT 'the size of the file in MB'";
        $this->db->query($q)->execute();
    }
}