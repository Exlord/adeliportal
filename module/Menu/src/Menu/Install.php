<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/2/14
 * Time: 2:16 PM
 */

namespace Menu;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_121()
    {
        $q = "alter table `tbl_menu_item` add column `config` text NOT NULL;";
        $this->db->query($q)->execute();
    }

    public function update_141(){
        $q = "ALTER TABLE `tbl_menu_item` ADD INDEX ( `menuId` ) ;";
        $q .= "ALTER TABLE `tbl_menu_item` ADD INDEX ( `itemOrder` ) ;";
        $q .= "ALTER TABLE `tbl_menu_item` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_menu_item` ADD INDEX ( `menuId` , `itemOrder` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_menu` ADD INDEX ( `menuName` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_150(){
        $q = "ALTER TABLE `tbl_menu_item` ADD `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
    }
} 