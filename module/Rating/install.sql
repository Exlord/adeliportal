CREATE TABLE IF NOT EXISTS `tbl_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `entityId2` int(11)  DEFAULT NULL,
  `entityType` varchar(300) DEFAULT NULL,
  `rateScore` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entityType` (`entityType`(255)),
  KEY `entityId` (`entityId`),
  KEY `userId` (`userId`),
  KEY `rateScore` (`rateScore`),
  KEY `userId_2` (`userId`,`entityId`,`entityType`(255),`rateScore`),
  KEY `entityId_2` (`entityId`,`entityType`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;