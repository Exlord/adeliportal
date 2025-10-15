CREATE TABLE IF NOT EXISTS `tbl_ec_workshop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT 'disabled=0, enabled=1',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `title` (`title`),
  KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `tbl_ec_workshop_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `classId` int(11) NOT NULL,
  `registerDate` int(11) NOT NULL,
  `paymentStatus` tinyint(1) NOT NULL,
  `paymentId` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL COMMENT '0=temp res,1=full res,2=failed res,3=cancel request,4=canceled',
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `classId` (`classId`),
  KEY `registerDate` (`registerDate`),
  KEY `status` (`status`),
  KEY `userId` (`userId`,`classId`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `tbl_ec_workshop_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workshopId` int(11) NOT NULL,
  `educatorId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `capacity` int(3) NOT NULL,
  `price` varchar(15) NOT NULL,
  `location` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT 'disabled=0, enabled=1, canceled=2,started=3,finished=4',
  PRIMARY KEY (`id`),
  KEY `workshopId` (`workshopId`),
  KEY `educatorId` (`educatorId`),
  KEY `workshopId_2` (`workshopId`,`status`),
  KEY `educatorId_2` (`educatorId`,`status`),
  KEY `title` (`title`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `tbl_ec_workshop_timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classId` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `start` int(11) NOT NULL COMMENT 'start time',
  `end` int(11) NOT NULL COMMENT 'end time',
  `status` tinyint(1) NOT NULL COMMENT '0=normal,1=canceled',
  PRIMARY KEY (`id`),
  KEY `end` (`end`,`status`),
  KEY `start` (`start`,`status`),
  KEY `classId` (`classId`,`status`),
  KEY `classId_2` (`classId`),
  KEY `date` (`date`),
  KEY `start_2` (`start`),
  KEY `end_2` (`end`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;