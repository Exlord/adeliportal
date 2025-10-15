<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/1/14
 * Time: 12:58 PM
 */

namespace Analyzer;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_113()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_visits` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `date` int(11) NOT NULL DEFAULT '0',
                  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=uniqe,0=repeated',
                  PRIMARY KEY (`id`),
                  KEY `date` (`date`),
                  KEY `date_2` (`date`,`type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
                ";
        $this->db->query($q)->execute();
        $q = "CREATE TABLE IF NOT EXISTS `tbl_visits_archive` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `date` int(11) NOT NULL,
                  `count` int(11) NOT NULL DEFAULT '0',
                  `uniqueCount` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `date` (`date`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";
        $this->db->query($q)->execute();
    }
} 