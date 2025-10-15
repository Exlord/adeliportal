CREATE TABLE IF NOT EXISTS `tbl_domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_domain_content` (
  `domainId` int(11) NOT NULL,
  `entityId` int(11) NOT NULL,
  `entityType` varchar(100) NOT NULL,
  KEY `entityId` (`entityId`,`entityType`),
  KEY `domainId` (`domainId`,`entityId`,`entityType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;