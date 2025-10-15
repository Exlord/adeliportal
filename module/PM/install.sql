
CREATE TABLE IF NOT EXISTS `tbl_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `msg` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `to` (`to`,`status`),
  KEY `to_2` (`to`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;