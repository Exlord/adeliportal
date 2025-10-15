CREATE TABLE IF NOT EXISTS `tbl_mail_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` text NOT NULL,
  `from` text NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `count` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `domain` varchar(250) NOT NULL,
  `entityType` varchar(200) NOT NULL,
  `sendTime` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_mail_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` text NOT NULL,
  `from` text NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `count` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `domain` varchar(250) NOT NULL,
  `entityType` varchar(200) NOT NULL,
  `queued` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_send_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendTime` int(11) NOT NULL,
  `sendCount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
