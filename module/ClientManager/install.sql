CREATE TABLE IF NOT EXISTS `tbl_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `key` varchar(256) NOT NULL,
  `startDate` int(11) NOT NULL,
  `expireDate` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;