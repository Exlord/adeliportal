CREATE TABLE IF NOT EXISTS `tbl_hc_doctor` (
  `doctorId` int(11) NOT NULL,
  `sessionCost` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`doctorId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tbl_hc_doctor_ref` (
  `doctorId` int(11) NOT NULL,
  `patientId` int(11) NOT NULL,
  `refId` int(11) NOT NULL DEFAULT '0' COMMENT 'the user that reffered or assined this patient to this doctor',
  UNIQUE KEY `doctorId_2` (`doctorId`,`patientId`,`refId`),
  KEY `doctorId` (`doctorId`),
  KEY `patientId` (`patientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='list of patients that have been reffered to other doctors';

CREATE TABLE IF NOT EXISTS `tbl_hc_doctor_reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeId` int(11) NOT NULL,
  `doctorId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=temp,1=full,2=failed,3=cancel request,4=canceled,5=visited',
  `paymentId` int(11) NOT NULL DEFAULT '0',
  `paymentStatus` tinyint(1) NOT NULL DEFAULT '0',
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

CREATE TABLE IF NOT EXISTS `tbl_hc_doctor_timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctorId` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `start` int(11) NOT NULL COMMENT 'start time',
  `end` int(11) NOT NULL COMMENT 'end time',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=normal,1=canceled',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;