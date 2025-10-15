<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/7/14
 * Time: 10:04 AM
 */

namespace Page;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_13()
    {
        $q = "alter table `tbl_page` add column `domainVisibility` tinyint(1) NOT NULL DEFAULT '0'";
        $this->db->query($q)->execute();
        $q = "alter table `tbl_page` add column `domains` text";
        $this->db->query($q)->execute();
    }

    public function update_179()
    {
        $q = "ALTER TABLE `tbl_page` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_page` ADD INDEX (`isStaticPage` ) ;";
        $q .= "ALTER TABLE `tbl_page` ADD INDEX (`published` ) ;";
        $q .= "ALTER TABLE `tbl_page` ADD INDEX (`pageTitle` ) ;";
        $q .= "ALTER TABLE `tbl_page` ADD INDEX ( `status` , `isStaticPage` , `pageTitle` ) ;";
        $q .= "ALTER TABLE `tbl_page` ADD INDEX ( `status` , `isStaticPage` , `published` ) ;";
        $q .= "ALTER TABLE `tbl_tags_page` ADD INDEX ( `tagsId` ) ;";
        $q .= "ALTER TABLE `tbl_tags_page` ADD INDEX ( `pageId`) ;";
        $q .= "ALTER TABLE `tbl_tags_page` ADD INDEX ( `pageId` , `tagsId` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_1109()
    {
        $q = "ALTER TABLE `tbl_page` ADD `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
    }

    public function update_121()
    {
        $q = "ALTER TABLE `tbl_page` ADD `order` INT NULL DEFAULT '0';";
        $this->db->query($q)->execute();
    }

    public function update_218()
    {
        $q = "ALTER TABLE `tbl_page` ADD `refGallery` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'refrence gallery to page';";
        $this->db->query($q)->execute();
    }

    public function update_235()
    {
        $q = "update `tbl_page` set `status`=1 where `status`=0;";
        $this->db->query($q)->execute();
    }
}