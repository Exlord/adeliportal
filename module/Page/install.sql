CREATE TABLE IF NOT EXISTS `tbl_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageTitle` varchar(300) DEFAULT NULL,
  `introText` text,
  `fullText` text,
  `status` tinyint(1) DEFAULT '0' COMMENT '0=inactive1=publish2=unpublish3=archive4=recycle',
  `isStaticPage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=>no - 1=>yes',
  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=> dont show to front page - 1=>show to front page',
  `publishUp` int(11) DEFAULT NULL,
  `publishDown` int(11) DEFAULT NULL,
  `hits` int(11) DEFAULT '0',
  `createdBy` varchar(300) DEFAULT NULL,
  `config` text,
  `domainVisibility` tinyint(4) NOT NULL DEFAULT '0',
  `domains` text NOT NULL,
  `image` text,
  `order` int(11) DEFAULT '0',
  `refGallery` text COMMENT 'refrence gallery to page',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `isStaticPage` (`isStaticPage`),
  KEY `pageTitle` (`pageTitle`(255)),
  KEY `pageTitle_2` (`pageTitle`(255),`status`,`isStaticPage`),
  KEY `published` (`published`),
  KEY `status_2` (`status`,`isStaticPage`,`published`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `tbl_tags_page` (
  `pageId` int(11) DEFAULT '0',
  `tagsId` int(11) DEFAULT '0',
  KEY `pageId` (`pageId`),
  KEY `tagsId` (`tagsId`),
  KEY `pageId_2` (`pageId`,`tagsId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
