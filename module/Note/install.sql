CREATE TABLE IF NOT EXISTS `tbl_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT '0',
  `note` text,
  `owner` int(11) DEFAULT '0',
  `entityId` int(11) DEFAULT '0',
  `entityType` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `entityId` (`entityId`,`entityType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_note_visibility` (
  `noteId` int(11) NOT NULL,
  `visibility` varchar(100) NOT NULL,
  UNIQUE KEY `noteId` (`noteId`,`visibility`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;