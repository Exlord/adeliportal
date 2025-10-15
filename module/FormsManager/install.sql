CREATE TABLE IF NOT EXISTS `tbl_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `formType` tinyint(1) NOT NULL DEFAULT '2',
  `templateFile` varchar(200) DEFAULT 'simple',
  `format` mediumtext,
  `email` varchar(200) DEFAULT NULL,
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `config` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_forms_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formId` int(11) NOT NULL DEFAULT '0',
  `createdTime` int(11) NOT NULL,
  `editTime` int(11) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `formId` (`formId`),
  KEY `userId` (`userId`),
  KEY `formId_2` (`formId`,`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;


