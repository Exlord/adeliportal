CREATE TABLE IF NOT EXISTS `tbl_product_showcase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT NULL,
  `title` varchar(300) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`(255)),
  KEY `status` (`status`),
  KEY `order` (`order`),
  KEY `id` (`id`,`date`,`title`(255),`status`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_product_showcase_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `family` varchar(300) DEFAULT NULL,
  `company` varchar(200) DEFAULT NULL,
  `mail` varchar(300) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `desc` text,
  `createDate` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `refCode` varchar(50) DEFAULT NULL,
  `refText` text,
  `userId` int(11) DEFAULT '0',
  `psData` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `family` (`family`(255)),
  KEY `id` (`id`,`name`,`family`(255),`company`,`mail`(255),`phone`,`fax`,`country`,`state`,`city`,`createDate`,`status`,`refCode`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;