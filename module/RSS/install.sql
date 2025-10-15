CREATE TABLE IF NOT EXISTS `tbl_rss_reader` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `url` varchar(250) NOT NULL,
  `lastRead` int(10) NOT NULL DEFAULT '0' COMMENT 'last time this rss was read from the source',
  `readInterval` tinyint(2) NOT NULL,
  `feedLimit` tinyint(2) NOT NULL DEFAULT '10' COMMENT 'how many feed item to read',
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
