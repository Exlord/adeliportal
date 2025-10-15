CREATE TABLE IF NOT EXISTS `tbl_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(200) DEFAULT NULL,
  `position` varchar(200) DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  `pages` text,
  `enabled` tinyint(1) DEFAULT '0',
  `data` text NOT NULL,
  `locked` tinyint(4) DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order` (`order`),
  KEY `enabled` (`enabled`),
  KEY `position` (`position`),
  KEY `locked` (`locked`),
  KEY `type` (`type`),
  KEY `type_2` (`type`,`enabled`,`locked`),
  KEY `position_2` (`position`,`enabled`),
  KEY `type_3` (`type`,`enabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_blocks_per_url` (
  `url` varchar(500) NOT NULL,
  `blocks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tbl_blocks_per_url` ADD INDEX `url` ( `url` ( 500 ) );


