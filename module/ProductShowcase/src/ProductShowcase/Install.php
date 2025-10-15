<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/1/14
 * Time: 12:58 PM
 */

namespace ProductShowcase;


use System\DB\BaseInstall;

class Install extends BaseInstall
{

    public function update_111()
    {
        $q = "ALTER TABLE `tbl_file` ADD `fileTypes` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `fPath`;";
        $q .= "ALTER TABLE `tbl_product_showcase` ADD `order` INT NULL DEFAULT '0';";
        $q .= "ALTER TABLE `tbl_product_showcase` ADD INDEX ( `order` ) ;";
        $this->db->query($q)->execute();
    }


} 