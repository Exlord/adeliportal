<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/7/14
 * Time: 10:04 AM
 */

namespace Payment;


use System\DB\BaseInstall;

class Install extends BaseInstall
{

    public function update_127()
    {
        $q = "ALTER TABLE `tbl_payment_bank_info` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_payment` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_payment` ADD INDEX ( `id` , `status` ) ;";
        $this->db->query($q)->execute();
    }

    public function update_140()
    {
        $q = "CREATE TABLE IF NOT EXISTS `tbl_payment_transactions` (
                  `userId` int(11) NOT NULL,
                  `amount` int(11) NOT NULL,
                  `note` text NOT NULL,
                  `date` int(11) NOT NULL,
                  `adminId` int(11) NOT NULL,
                  KEY `userId` (`userId`),
                  KEY `amount` (`amount`),
                  KEY `date` (`date`),
                  KEY `adminId` (`adminId`),
                  KEY `userId_2` (`userId`,`amount`,`date`,`adminId`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_payment_user_amount` (
                  `userId` int(11) NOT NULL,
                  `cash` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`userId`),
                  KEY `userId` (`userId`),
                  KEY `cash` (`cash`),
                  KEY `userId_2` (`userId`,`cash`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();

        $q = "CREATE TABLE IF NOT EXISTS `tbl_payment_entity` (
                  `paymentId` int(11) DEFAULT NULL,
                  `entityId` int(11) DEFAULT NULL,
                  `entityType` varchar(300) DEFAULT NULL,
                  `userId` int(11) DEFAULT NULL,
                  KEY `paymentId` (`paymentId`),
                  KEY `entityId` (`entityId`),
                  KEY `entityType` (`entityType`(255)),
                  KEY `userId` (`userId`),
                  KEY `paymentId_2` (`paymentId`,`entityId`,`entityType`(255),`userId`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($q)->execute();
    }
}