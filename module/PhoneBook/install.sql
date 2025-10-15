CREATE TABLE IF NOT EXISTS `tbl_phone_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameAndFamily` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL,
  `mobile` varchar(300) NOT NULL,
  `phone` varchar(300) NOT NULL,
  `fax` varchar(300) NOT NULL,
  `comment` text NOT NULL,
  `date` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_phonebook_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameAndFamily` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL,
  `mobile` varchar(300) NOT NULL,
  `phone` varchar(300) NOT NULL,
  `fax` varchar(300) NOT NULL,
  `comment` text NOT NULL,
  `date` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;