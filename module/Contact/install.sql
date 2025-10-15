CREATE TABLE IF NOT EXISTS `tbl_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendId` int(11) DEFAULT '0',
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `description` text,
  `typeContact` int(11) DEFAULT '0',
  `date` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_contact_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `smsNumber` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `catId` int(11) DEFAULT NULL,
  `role` varchar(300) DEFAULT NULL,
  `google` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  `description` text,
  `showEmail` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `catId` (`catId`),
  KEY `name` (`name`(255)),
  KEY `catId_2` (`catId`,`status`),
  KEY `name_2` (`name`(255),`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_contact_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `contactUserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;