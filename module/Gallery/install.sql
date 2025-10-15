CREATE TABLE IF NOT EXISTS `tbl_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(100) DEFAULT NULL,
  `groupText` text,
  `publishUp` int(11) DEFAULT '0',
  `publishDown` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `reloadType` int(11) DEFAULT '0',
  `type` varchar(100) DEFAULT NULL,
  `width` int(11) DEFAULT '0',
  `height` int(11) DEFAULT '0',
  `image` text,
  `alt` varchar(300) DEFAULT NULL,
  `title` varchar(300) DEFAULT NULL,
  `fileType` varchar(300) DEFAULT NULL,
  `config` text,
  `position` varchar(300) DEFAULT NULL,
  `showType` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_gallery_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `url` varchar(500) DEFAULT NULL,
  `image` text,
  `alt` varchar(500) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `type` varchar(100) DEFAULT NULL COMMENT 'Groups TYpe',
  `fileType` varchar(300) DEFAULT NULL COMMENT 'File Type',
  `width` int(11) DEFAULT '0',
  `height` int(11) DEFAULT '0',
  `appHits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_order_banner` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_banner` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_banner_size` (
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
