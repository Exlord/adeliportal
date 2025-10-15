<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/8/14
 * Time: 10:09 AM
 */

namespace Fields;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_125()
    {
        $q = "alter table `tbl_fields` add column `entityType` varchar(200) NOT NULL";
        $this->db->query($q)->execute();
    }

    public function update_131(){
        $q = "ALTER TABLE `tbl_fields` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_fields` ADD INDEX ( `entityType` ) ;";
        $q .= "ALTER TABLE `tbl_fields` ADD INDEX ( `entityType` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_fields` ADD INDEX ( `fieldOrder` ) ;";
        $q .= "ALTER TABLE `fieldOrder_2` ADD INDEX ( `status`,`entityType`,`fieldOrder`) ;";
        $this->db->query($q)->execute();
    }

    public function update_16(){
        $q = "ALTER TABLE `tbl_fields` ADD `collection` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT 'dose this field belongs to a collection'";
        $this->db->query($q)->execute();
    }

    public function update_161(){
        $q = "ALTER TABLE `tbl_fields` ADD INDEX ( `collection` );";
        $this->db->query($q)->execute();
    }
} 