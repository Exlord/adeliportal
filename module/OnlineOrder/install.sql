CREATE TABLE IF NOT EXISTS `tbl_online_order_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) DEFAULT '0',
  `others` text,
  `name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `address` varchar(300) DEFAULT NULL,
  `comment` text,
  `subDomain` varchar(300) DEFAULT NULL,
  `domains` text,
  `refCode` varchar(100) DEFAULT NULL,
  `confirmation` tinyint(4) DEFAULT '0',
  `diskSpace` int(11) DEFAULT '0',
  `date` int(11) DEFAULT '0',
  `publishUp` int(11) DEFAULT '0',
  `publishDown` int(11) DEFAULT '0',
  `payerId` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `amount` varchar(100) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_online_order_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domainName` varchar(200) DEFAULT NULL,
  `domainAlias` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domainName` (`domainName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientName` varchar(200) DEFAULT NULL,
  `clientEmail` varchar(200) DEFAULT NULL,
  `clientDomain` varchar(200) DEFAULT NULL,
  `dbName` varchar(100) DEFAULT NULL,
  `dbUser` varchar(100) DEFAULT NULL,
  `dbPass` varchar(100) DEFAULT NULL,
  `diskSpace` int(11) DEFAULT NULL COMMENT 'used disk space in MB',
  `bandwidth` int(11) DEFAULT NULL COMMENT 'used bandwidth over a month in MB',
  `locked` tinyint(1) DEFAULT '0',
  `modules` text,
  `username` varchar(100) DEFAULT NULL COMMENT 'username for login server admin to user cms',
  `password` varchar(50) DEFAULT NULL COMMENT 'password for login server admin to user cms',
  `subDomainUser` varchar(100) DEFAULT NULL COMMENT 'username for login sub domain admin to user cms',
  `subDomainPass` varchar(50) DEFAULT NULL COMMENT 'password for login sub domain admin to user cms',
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientDomain` (`clientDomain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
