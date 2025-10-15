<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/2/14
 * Time: 2:05 PM
 */

namespace RealEstate;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_21()
    {
        $q = "alter table `tbl_realestate_contactus` add column  `been` int(1) DEFAULT '0';";
        $this->db->query($q)->execute();
    }

    public function update_220()
    {
        $q = "DROP table `tbl_realestate_contactus`;";
        $this->db->query($q)->execute();
    }

    public function update_235()
    {
        $q = "alter table `tbl_realestate` add column  `areaId` int(11) DEFAULT NULL COMMENT 'fk to tbl_city_area_list.id';";
        $this->db->query($q)->execute();
    }

    public function update_237()
    {
        $q = "ALTER TABLE `tbl_realestate` ADD INDEX ( `ownerName` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `ownerPhone` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `ownerMobile` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `ownerEmail` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `addressShort` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `expire` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `isRequest` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `isSpecial` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `cityId` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `areaId` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `estateType` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `stateId` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `userId` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `passForEdit` ) ;";
        $q .= "ALTER TABLE `tbl_realestate` ADD INDEX ( `ownerName` , `ownerPhone` , `ownerMobile` , `ownerEmail` , `addressShort` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_243()
    {
        $q = "ALTER TABLE `tbl_realestate` ADD `dateUpdate` INT NOT NULL ;";
        $this->db->query($q)->execute();
    }


    public function update_250()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_realestate_agent_area` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `agentId` int(11) DEFAULT NULL,
              `areaId` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `agentId` (`agentId`,`areaId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $q .= "ALTER TABLE `tbl_realestate` CHANGE `newArea` `newArea` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
    }

    public function update_263()
    {
        $q = "ALTER TABLE `tbl_realestate` DROP `passForEdit` ,DROP `estateArea` ,DROP `allowEdit` ;";
        $this->db->query($q)->execute();
    }

    public function update_2712()
    {
        $q = "ALTER TABLE `tbl_config` CHANGE `varValue` `varValue` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
    }
    public function update_2715()
    {
        $q = "ALTER TABLE `tbl_realestate` ADD `app` TINYINT NULL DEFAULT '0' COMMENT 'realestate sent by app' ;";
        $this->db->query($q)->execute();
    }
} 