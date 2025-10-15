<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/21/14
 * Time: 2:47 PM
 */

namespace Comment;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_15(){
        $q = "ALTER TABLE `tbl_comment` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_comment` ADD INDEX ( `entityId` ) ;";
        $q .= "ALTER TABLE `tbl_comment` ADD INDEX ( `entityType` ) ;";
        $q .= "ALTER TABLE `tbl_comment` ADD INDEX ( `parentId` ) ;";
        $q .= "ALTER TABLE `tbl_comment` ADD INDEX ( `userId` ) ;";
        $q .= "ALTER TABLE `tbl_comment` ADD INDEX ( `parentId` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_comment` ADD INDEX ( `entityId` , `entityType` , `status` ) ;";
        $this->db->query($q)->execute();
    }
}