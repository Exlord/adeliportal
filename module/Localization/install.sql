CREATE TABLE IF NOT EXISTS `tbl_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `langSign` varchar(20) DEFAULT NULL,
  `langName` varchar(100) DEFAULT NULL,
  `langFlag` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0=>inactive , 1=>active , 2=>customer not Purchased this language',
  `default` tinyint(1) DEFAULT '0' COMMENT '0=>inactive , 1=>active , 2=>customer not Purchased this language',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `default` (`default`),
  KEY `status_2` (`status`,`default`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityType` varchar(100) NOT NULL,
  `entityId` int(11) NOT NULL,
  `fieldName` varchar(200) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entityType` (`entityType`),
  KEY `entityId` (`entityId`),
  KEY `lang` (`lang`),
  KEY `entityType_2` (`entityType`,`entityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_language_content` (
  `langSign` varchar(5) NOT NULL,
  `entityId` int(11) NOT NULL,
  `entityType` varchar(100) NOT NULL,
  KEY `entityId` (`entityId`,`entityType`),
  KEY `domainId` (`langSign`,`entityId`,`entityType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


