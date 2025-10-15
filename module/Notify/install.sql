CREATE TABLE IF NOT EXISTS `tbl_notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uId` int(11) NOT NULL,
  `msg` text NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uId`),
  KEY `uId_2` (`uId`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;