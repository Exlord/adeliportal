CREATE TABLE IF NOT EXISTS `tbl_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catName` varchar(100) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
  `catText` text,
  `catMachineName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catMachineName` (`catMachineName`),
  KEY `catName` (`catName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_category_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(100) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
  `itemText` text,
  `parentId` int(11) DEFAULT '0',
  `itemStatus` tinyint(1) DEFAULT '0' COMMENT '0=disabled\r\n1=enabled',
  `catId` int(11) DEFAULT NULL,
  `itemOrder` int(11) DEFAULT NULL,
  `image` VARCHAR(400) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `itemName` (`itemName`),
  KEY `parentId` (`parentId`),
  KEY `itemStatus` (`itemStatus`),
  KEY `catId` (`catId`),
  KEY `itemOrder` (`itemOrder`),
  KEY `itemStatus_2` (`itemStatus`,`catId`),
  KEY `parentId_2` (`parentId`,`catId`),
  KEY `itemName_2` (`itemName`,`itemStatus`,`catId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
commit;


CREATE TABLE IF NOT EXISTS `tbl_category_item_entity` (
  `itemId` int(11) NOT NULL,
  `entityId` int(11) NOT NULL,
  `entityType` varchar(200) NOT NULL,
  PRIMARY KEY (`itemId`,`entityId`),
  KEY `entityType` (`entityType`),
  KEY `entityId` (`entityId`,`entityType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tbl_category_item` (`id`, `itemName`, `itemText`, `image`, `parentId`, `itemStatus`, `catId`, `itemOrder`) VALUES
(1, 'مقالات', '', NULL, 0, 1, 1, 0),
(2, 'خبرها', '', NULL, 0, 1, 1, 0);


INSERT INTO `tbl_category` (`id`, `catName`, `catText`, `catMachineName`) VALUES
(1, 'article', '', 'article');
