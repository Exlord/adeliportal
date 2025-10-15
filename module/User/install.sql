CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `displayName` varchar(200) DEFAULT NULL,
  `emailStatus` int(11) DEFAULT '0' COMMENT '0=not validated\r\n1=validation email sended\r\n2=validated',
  `accountStatus` int(11) DEFAULT '0' COMMENT '0=not-approved\r\n1=approved\r\n2=temporery-locked\r\n3=locked\r\n4=banned\r\n5=deleted',
  `loginDate` int(11) DEFAULT NULL,
  `lastLoginDate` int(11) DEFAULT NULL,
  `data` TEXT ,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `username_2` (`username`),
  KEY `password` (`password`),
  KEY `email` (`email`),
  KEY `username_3` (`username`,`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

CREATE TABLE IF NOT EXISTS `tbl_user_flood` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`,`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='failed login attempts' ;

CREATE TABLE IF NOT EXISTS `tbl_users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `roleId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `roleId` (`roleId`),
  KEY `userId_2` (`userId`,`roleId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tbl_user_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `birthDate` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `stateId` int(11) DEFAULT NULL,
  `cityId` int(11) DEFAULT NULL,
  `address` text,
  `aboutMe` text,
  `image` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(200) DEFAULT NULL,
  `parentId` int(11) DEFAULT '0',
  `locked` tinyint(1) DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parentId` (`parentId`),
  KEY `level` (`level`),
  KEY `parentId_2` (`parentId`,`level`),
  KEY `roleName` (`roleName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `tbl_roles` (`id`, `roleName`, `parentId`, `locked`, `level`) VALUES
(1, 'Guest', 0, 1, 0),
(2, 'Member', 0, 1, 1),
(3, 'server Admin', 4, 1, 10000),
(4, 'Admin', 1, 1, 9000);

INSERT INTO `tbl_users_roles` (`userId`, `roleId`) VALUES
                (1,3),
                (2,4);
commit;

INSERT INTO `tbl_config` (`varName`,`varValue`) VALUES ('permissions','a:1:{s:5:"perms";a:3:{i:4;a:16:{s:15:"route:app/print";s:1:"1";s:11:"route:admin";s:1:"1";s:25:"route:app/quick-send-mail";s:1:"1";s:15:"route:app/links";s:1:"1";s:19:"route:app/page-view";s:1:"1";s:17:"route:app/content";s:1:"1";s:24:"route:app/single-content";s:1:"1";s:21:"route:app/real-estate";s:1:"1";s:21:"route:app/delete-file";s:1:"1";s:22:"route:app/online-order";s:1:"1";s:17:"route:app/payment";s:1:"1";s:17:"route:app/comment";s:1:"1";s:17:"route:app/contact";s:1:"1";s:15:"route:app/chart";s:1:"1";s:16:"route:app/rating";s:1:"1";s:32:"route:app/negative-positive-rate";s:1:"1";}i:1;a:10:{s:15:"route:app/links";s:1:"1";s:19:"route:app/page-view";s:1:"1";s:17:"route:app/content";s:1:"1";s:24:"route:app/single-content";s:1:"1";s:21:"route:app/real-estate";s:1:"1";s:22:"route:app/online-order";s:1:"1";s:17:"route:app/payment";s:1:"1";s:17:"route:app/comment";s:1:"0";s:17:"route:app/contact";s:1:"1";s:15:"route:app/chart";s:1:"1";}i:2;a:25:{s:15:"route:app/links";s:1:"1";s:19:"route:app/page-view";s:1:"1";s:17:"route:app/content";s:1:"1";s:24:"route:app/single-content";s:1:"1";s:21:"route:app/real-estate";s:1:"1";s:21:"route:app/delete-file";s:1:"1";s:22:"route:app/online-order";s:1:"1";s:17:"route:app/payment";s:1:"1";s:17:"route:app/comment";s:1:"1";s:26:"route:app/comment/edit:all";s:1:"2";s:28:"route:app/comment/delete:all";s:1:"2";s:17:"route:app/contact";s:1:"1";s:15:"route:app/chart";s:1:"1";s:16:"route:app/rating";s:1:"1";s:32:"route:app/negative-positive-rate";s:1:"1";s:22:"route:admin/users/view";s:1:"1";s:26:"route:admin/users/view:all";s:1:"2";s:22:"route:admin/users/edit";s:1:"1";s:26:"route:admin/users/edit:all";s:1:"2";s:27:"route:admin/users/edit:role";s:1:"2";s:28:"route:admin/users/edit-image";s:1:"1";s:32:"route:admin/users/edit-image:all";s:1:"2";s:24:"route:admin/users/delete";s:1:"1";s:28:"route:admin/users/delete:all";s:1:"2";s:33:"route:admin/users/change-password";s:1:"1";}}}');
commit;