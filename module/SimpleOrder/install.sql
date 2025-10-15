CREATE TABLE IF NOT EXISTS `tbl_simple_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `description` text,
  `catItems` text,
  `created` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`,`created`),
  KEY `created` (`created`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;