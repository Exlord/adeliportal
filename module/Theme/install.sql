CREATE TABLE IF NOT EXISTS `tbl_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1=client,2=admin',
  `default` int(1) NOT NULL DEFAULT '0' COMMENT '0=no,1=yes',
  `locked` int(1) NOT NULL DEFAULT '1' COMMENT '0=no,1=yes',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `type` (`type`),
  KEY `default` (`default`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;