CREATE TABLE IF NOT EXISTS `tbl_newsletter_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text CHARACTER SET utf8,
  `desc` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_news_letter_sign_up` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `config` text,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `email` (`email`),
  KEY `status_2` (`status`),
  KEY `email_2` (`email`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

