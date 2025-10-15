CREATE TABLE IF NOT EXISTS `tbl_event_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` tinyint(1) DEFAULT '6',
  `message` text,
  `uid` int(11) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `entityType` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entityType` (`entityType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;