CREATE TABLE IF NOT EXISTS `tbl_org_chart_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `parentId` int(11) NOT NULL,
  `chartId` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `chartId` (`chartId`),
  KEY `parentId` (`parentId`),
  KEY `userId` (`userId`),
  KEY `userId_2` (`userId`,`parentId`,`chartId`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_org_chart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) DEFAULT NULL,
  `description` text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `config` text,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;