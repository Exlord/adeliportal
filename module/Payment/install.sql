CREATE TABLE IF NOT EXISTS `tbl_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` bigint(20) NOT NULL,
  `payDate` int(11) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL,
  `refId` varchar(50) DEFAULT '0' COMMENT 'id returned from bank',
  `status` int(11) NOT NULL,
  `comment` text,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `id` (`id`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_payment_bank_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `userName` varchar(200) DEFAULT NULL,
  `passWord` varchar(200) DEFAULT NULL,
  `terminalId` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `className` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `tbl_payment_bank_info` (`id`, `name`, `userName`, `passWord`, `terminalId`, `status`, `className`) VALUES
(1, 'سامان', '[name]', '[username]', '[password]', 1, 'Payment\\API\\Saman'),
(2, 'ملت', '[name]', '[username]', '[password]', 1, 'Payment\\API\\Mellat');

CREATE TABLE IF NOT EXISTS `tbl_payment_transactions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tbl_payment_user_amount` (
  `userId` int(11) NOT NULL,
  `cash` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`),
  KEY `userId` (`userId`),
  KEY `cash` (`cash`),
  KEY `userId_2` (`userId`,`cash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tbl_payment_entity` (
  `paymentId` int(11) DEFAULT NULL,
  `entityId` int(11) DEFAULT NULL,
  `entityType` varchar(300) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  KEY `paymentId` (`paymentId`),
  KEY `entityId` (`entityId`),
  KEY `entityType` (`entityType`(255)),
  KEY `userId` (`userId`),
  KEY `paymentId_2` (`paymentId`,`entityId`,`entityType`(255),`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

