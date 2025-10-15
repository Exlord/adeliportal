CREATE TABLE IF NOT EXISTS `tbl_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fName` varchar(400) DEFAULT NULL,
  `fTitle` varchar(400) DEFAULT NULL,
  `fAlt` varchar(400) DEFAULT NULL,
  `fPath` varchar(400) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `entityType` varchar(200) DEFAULT NULL,
  `entityId` int(11) DEFAULT NULL,
  `fileType` varchar(300) CHARACTER SET utf8 NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `entityType` (`entityType`),
  KEY `fPath` (`fPath`(255)),
  KEY `entityType_2` (`entityType`,`entityId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_file_private` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `downloadAs` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `accessibility` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='a list of private files' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_file_private_usage` (
  `fileId` int(11) NOT NULL,
  `entityType` varchar(255) NOT NULL,
  `entityId` int(11) NOT NULL,
  UNIQUE KEY `fileId` (`fileId`,`entityType`,`entityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;