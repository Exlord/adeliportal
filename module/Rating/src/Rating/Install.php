<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/2/14
 * Time: 2:05 PM
 */

namespace Rating;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_122()
    {
        $q = "ALTER TABLE `tbl_rating` ADD INDEX ( `userId`  ) ;";
        $q .= "ALTER TABLE `tbl_rating` ADD INDEX ( `entityId` ) ;";
        $q .= "ALTER TABLE `tbl_rating` ADD INDEX ( `entityType` ) ;";
        $q .= "ALTER TABLE `tbl_rating` ADD INDEX ( `rateScore` ) ;";
        $q .= "ALTER TABLE `tbl_rating` ADD INDEX ( `userId` , `entityId` , `entityType` , `rateScore` ) ;";
        $q .= "ALTER TABLE `tbl_rating` ADD INDEX ( `entityId` , `entityType` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_125()
    {
        $q = "ALTER TABLE `tbl_rating` ADD `entityId2` INT( 11 ) DEFAULT NULL AFTER `entityId` ";
        $this->db->query($q)->execute();
    }
}