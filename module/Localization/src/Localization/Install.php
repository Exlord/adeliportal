<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/21/14
 * Time: 2:47 PM
 */

namespace Localization;


use System\DB\BaseInstall;
use Zend\Db\TableGateway\TableGateway;

class Install extends BaseInstall
{
    public function update_137()
    {
        $q = "ALTER TABLE `tbl_languages` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_languages` ADD INDEX ( `default` ) ;";
        $q .= "ALTER TABLE `tbl_languages` ADD INDEX ( `status` , `default` ) ;";
        $q .= "ALTER TABLE `tbl_translation` ADD INDEX ( `entityId` ) ;";
        $q .= "ALTER TABLE `tbl_translation` ADD INDEX ( `entityType`) ;";
        $q .= "ALTER TABLE `tbl_translation` ADD INDEX ( `entityType` , `entityId` ) ;";
        $q .= "ALTER TABLE `tbl_translation` ADD INDEX ( `lang` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_162()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_translation_contact` (
              `entityId` int(11) NOT NULL,
              `lang` varchar(4) NOT NULL,
              `name` varchar(300) DEFAULT NULL,
              `address` varchar(500) DEFAULT NULL,
              `role` varchar(300) DEFAULT NULL,
              UNIQUE KEY `entityId` (`entityId`,`lang`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_translation_contact_type` (
              `entityId` int(11) NOT NULL,
              `lang` varchar(4) NOT NULL,
              `title` varchar(200) DEFAULT NULL,
              UNIQUE KEY `entityId` (`entityId`,`lang`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_translation_menu_item` (
              `entityId` int(11) NOT NULL,
              `lang` varchar(4) NOT NULL,
              `itemName` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
              `itemTitle` varchar(400) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
              UNIQUE KEY `entityId` (`entityId`,`lang`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_translation_page` (
              `entityId` int(11) NOT NULL,
              `lang` varchar(4) NOT NULL,
              `pageTitle` varchar(300) DEFAULT NULL,
              `fullText` text,
              `introText` text,
              UNIQUE KEY `entityId` (`entityId`,`lang`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "select * from tbl_translation";
        $result = $this->db->query($q)->execute();
        $data = array();
        foreach ($result as $row) {
            $entityType = $row['entityType'];
            switch ($row['entityType']) {
                case 'page':
                    $entityType = 'page';
                    break;
                case 'content':
                    $entityType = 'page';
                    break;
                case 'menu-item':
                    $entityType = 'menu_item';
                    break;
                case 'contact':
                    $entityType = 'contact';
                    break;
                case 'contact_type':
                    $entityType = 'contact_type';
                    break;
            }
            $data[$entityType][$row['entityId']][$row['lang']][$row['fieldName']] = $row['content'];
        }
        $exceptions = array();
        foreach ($data as $entityType => $data2) {
            $table = new TableGateway('tbl_translation_' . strtolower($entityType), $this->db);
            foreach ($data2 as $entityId => $data3) {
                foreach ($data3 as $lang => $data4) {
                    $row = array('entityId' => $entityId, 'lang' => $lang);
                    foreach ($data4 as $fieldName => $value) {
                        $row[$fieldName] = $value;
                    }
                    try {
                        $table->insert($row);
                    } catch (\Exception $e) {
                        $exceptions[] = $e;
                    }
                }
            }
        }
        if (count($exceptions)) {
            $output = array();
            /* @var $ex \Exception */
            foreach ($exceptions as $ex) {
                $output[] = $ex->getMessage();
                if ($ex->getPrevious())
                    $output[] = $ex->getPrevious()->getMessage();
            }
            throw new \Exception(implode('<br/>\n', $exceptions));
        }
    }

    public function update_17()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_language_content` (
              `langSign` varchar(5) NOT NULL,
              `entityId` int(11) NOT NULL,
              `entityType` varchar(100) NOT NULL,
              KEY `entityId` (`entityId`,`entityType`),
              KEY `domainId` (`langSign`,`entityId`,`entityType`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $this->db->query($q)->execute();
    }
}