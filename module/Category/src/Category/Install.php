<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/12/14
 * Time: 11:00 AM
 */
namespace Category;

use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_13()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_category_item_entity` (
              `itemId` int(11) NOT NULL,
              `entityId` int(11) NOT NULL,
              `entityType` varchar(200) NOT NULL,
              PRIMARY KEY (`itemId`,`entityId`),
              KEY `entityType` (`entityType`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();
    }

    public function update_151(){
        $q = "alter table `tbl_category_item` change itemIcon image VARCHAR(400);";
        $this->db->query($q)->execute();
    }

    public function update_159(){

        $q = "ALTER TABLE `tbl_category_item` ADD INDEX ( `itemName` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `parentId` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `catId` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `itemOrder` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `itemStatus` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `itemStatus` , `catId` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `parentId` , `catId` ) ;";
        $q .= "ALTER TABLE `tbl_category_item` ADD INDEX ( `itemName` , `itemStatus` , `catId` ) ;";
        $q .= "ALTER TABLE `tbl_category` ADD INDEX ( `catMachineName` ) ;";
        $q .= "ALTER TABLE `tbl_category` ADD INDEX ( `catName` ) ;";
        $q .= "ALTER TABLE `tbl_category_item_entity` ADD INDEX ( `entityId` , `entityType` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_178(){

        $q = "ALTER TABLE `tbl_category_item` DROP `image`;";
        $this->db->query($q)->execute();
    }
}