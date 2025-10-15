<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/21/14
 * Time: 2:47 PM
 */

namespace GeographicalAreas;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_127(){
        $q = "CREATE TABLE IF NOT EXISTS `tbl_city_area_list` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `areaTitle` varchar(300) DEFAULT NULL,
                  `itemStatus` tinyint(4) DEFAULT '0',
                  `cityId` int(11) DEFAULT '0',
                  `itemOrder` int(11) DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `itemStatus` (`itemStatus`,`cityId`,`itemOrder`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();
    }

    public function update_128(){
        $q = "ALTER TABLE `tbl_city_list` ADD INDEX ( `itemStatus` ) ;";
        $q .= "ALTER TABLE `tbl_city_list` ADD INDEX ( `stateId` ) ;";
        $q .= "ALTER TABLE `tbl_city_list` ADD INDEX ( `itemOrder` ) ;";
        $q .= "ALTER TABLE `tbl_city_list` ADD INDEX ( `cityTitle` ) ;";
        $q .= "ALTER TABLE `tbl_city_list` ADD INDEX ( `cityTitle` , `itemOrder` ) ;";
        $q .= "ALTER TABLE `tbl_city_list` ADD INDEX ( `itemStatus` , `stateId` ) ;";
        $q .= "ALTER TABLE `tbl_country_list` ADD INDEX ( `itemStatus` ) ;";
        $q .= "ALTER TABLE `tbl_country_list` ADD INDEX ( `countryTitle` ) ;";
        $q .= "ALTER TABLE `tbl_country_list` ADD INDEX ( `countryTitle` , `itemStatus` ) ;";
        $q .= "ALTER TABLE `tbl_state_list` ADD INDEX ( `itemOrder` ) ;";
        $q .= "ALTER TABLE `tbl_state_list` ADD INDEX ( `stateTitle`) ;";
        $q .= "ALTER TABLE `tbl_state_list` ADD INDEX ( `itemStatus`) ;";
        $q .= "ALTER TABLE `tbl_state_list` ADD INDEX ( `countryId`) ;";
        $q .= "ALTER TABLE `tbl_state_list` ADD INDEX ( `stateTitle`, `itemStatus`, `countryId` , `itemOrder`) ;";
        $q .= "ALTER TABLE `tbl_state_list` ADD INDEX ( `stateTitle`,`itemOrder`) ;";
        $this->db->query($q)->execute();
    }


    public function update_137(){
        $q = "ALTER TABLE `tbl_city_area_list` ADD `parentId` INT NULL DEFAULT '0' ;";
        $q .= "ALTER TABLE `tbl_city_area_list` ADD INDEX ( `parentId` ) ;";
        $this->db->query($q)->execute();
    }

}