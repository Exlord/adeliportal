CREATE TABLE IF NOT EXISTS `tbl_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(200) DEFAULT NULL,
  `fieldMachineName` varchar(200) DEFAULT NULL,
  `fieldDefaultValue` text,
  `fieldPostfix` text,
  `fieldDisplayTemplate` text,
  `fieldOrder` int(11) DEFAULT NULL,
  `fieldType` varchar(20) DEFAULT NULL COMMENT 'fk to fieldTypes',
  `fieldConfigData` text,
  `status` tinyint(1) DEFAULT '0',
  `fieldPrefix` varchar(200) DEFAULT NULL,
  `filters` text NOT NULL,
  `validators` text NOT NULL,
  `entityType` varchar(200) NOT NULL,
  `collection` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT 'dose this field belongs to a collection',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `entityType` (`entityType`),
  KEY `status_2` (`status`,`entityType`),
  KEY `fieldOrder` (`fieldOrder`),
  KEY `fieldOrder_2` (`status`,`entityType`,`fieldOrder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

