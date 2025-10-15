<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/21/14
 * Time: 2:47 PM
 */

namespace Gallery;


use System\DB\BaseInstall;

class Install extends BaseInstall
{

    public function initialize()
    {
        $api = new \OnlineOrder\API\OnlineOrder();
        $globalConfigs = $api->getGlobalRealEstateConfig();
        if (@$globalConfigs[@$this->params['groupId']]['changeBanner'])
            $locked = 0;
        else
            $locked = 1;

        $select = getSM('banner_table')->getBannerWithSiteNameBlock();
        if ($select->count()) {
            $placeholder = array_fill(0, 10, '?');
            $placeholder = '(' . implode(',', $placeholder) . ')';

            $placeholder = implode(',', array_fill(0, $select->count(), $placeholder));
            $q = "INSERT INTO `tbl_blocks` (`id`, `title`,`description`,`type`,`position`,`visibility`,`pages`,`enabled`,`data`,`locked`) VALUES " . $placeholder . ";";
            $data = array();
            foreach ($select as $row) {
                $data[] = $row->id;
                $data[] = $row->title;
                $data[] = $row->description;
                $data[] = $row->type;
                $data[] = $row->position;
                $data[] = $row->visibility;
                $data[] = $row->pages;
                $data[] = $row->enabled;
                $data[] = $row->data;
                $data[] = $locked;
            }

            $this->db->query($q)->execute($data);
        }
    }

    public function update_12()
    {
        $q = "ALTER TABLE `tbl_banner_queue` ADD INDEX ( `position` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX ( `groupId` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX ( `title` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX ( `type` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX (`groupId` , `title` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX ( `groupId` , `status` , `type` ) ;";
        $q .= "ALTER TABLE `tbl_gallery_item` ADD INDEX ( `id` , `groupId` , `status` , `type` ) ;";
        $q .= "ALTER TABLE `tbl_gallery` ADD INDEX ( `publishDown`) ;";
        $q .= "ALTER TABLE `tbl_gallery` ADD INDEX (`status`) ;";
        $q .= "ALTER TABLE `tbl_gallery` ADD INDEX (`type` ) ;";
        $q .= "ALTER TABLE `tbl_gallery` ADD INDEX ( `type` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_gallery` ADD INDEX ( `groupName` ) ;";
        $q .= "ALTER TABLE `tbl_gallery` ADD INDEX ( `id` , `publishDown` , `status` ) ;";
        $q .= "ALTER TABLE `tbl_order_banner` ADD INDEX ( `status` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_121()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_banner_queue` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `groupId` int(11) NOT NULL,
              `blockId` int(11) NOT NULL,
              `position` varchar(200) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `position` (`position`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();
    }

    public function update_136()
    {
        $q = "ALTER TABLE `tbl_gallery_item` ADD `appHits` INT NULL DEFAULT '0' AFTER `hits` ;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_banner` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `groupId` int(11) DEFAULT NULL,
              `mobile` varchar(50) DEFAULT NULL,
              `email` varchar(300) DEFAULT NULL,
              `position` varchar(300) DEFAULT NULL,
              `created` int(11) DEFAULT NULL,
              `expire` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `mobile` (`mobile`,`email`(255),`expire`),
              KEY `groupId` (`groupId`,`position`(255),`expire`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();

        $q = "DROP TABLE `tbl_banner_queue` ;";
        $this->db->query($q)->execute();

        $q = "ALTER TABLE `tbl_order_banner` DROP `blockId` ;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_banner_size` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `position` varchar(300) DEFAULT NULL,
              `width` int(11) DEFAULT '0',
              `height` int(11) DEFAULT '0',
              `price` varchar(200) DEFAULT '0',
              `addPrice` varchar(200) DEFAULT '0',
              `status` tinyint(4) DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `position` (`position`(255)),
              KEY `status` (`status`),
              KEY `position_2` (`position`(255),`width`,`height`,`price`,`status`),
              KEY `width` (`width`),
              KEY `height` (`height`),
              KEY `id` (`id`,`position`(255),`width`,`height`,`status`),
              KEY `addPrice` (`addPrice`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
        $this->db->query($q)->execute();
    }

    public function update_141()
    {
        $q = "ALTER TABLE `tbl_gallery` ADD `position` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($q)->execute();
        $q = "ALTER TABLE `tbl_gallery` ADD INDEX ( `position` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_145()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_order_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(100) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `images` text,
  `status` tinyint(4) DEFAULT '0',
  `payerCode` varchar(50) DEFAULT NULL,
  `countMonth` int(11) DEFAULT '1',
  `price` varchar(100) DEFAULT '0',
  `email` varchar(300) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `description` text,
  `date` int(11) DEFAULT '0',
  `userId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->db->query($q)->execute();
    }

    public function update_1520()
    {
        $q = "ALTER TABLE `tbl_gallery` ADD `showType` TINYINT NOT NULL DEFAULT '1' ;";
        $this->db->query($q)->execute();
    }



}