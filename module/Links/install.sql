CREATE TABLE IF NOT EXISTS `tbl_links_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(200) DEFAULT NULL,
  `itemTitle` text,
  `itemLink` text,
  `itemOrder` int(11) DEFAULT NULL,
  `itemStatus` tinyint(1) DEFAULT NULL,
  `catId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `itemOrder` (`itemOrder`),
  KEY `itemStatus` (`itemStatus`),
  KEY `catId` (`catId`),
  KEY `itemOrder_2` (`itemOrder`,`itemStatus`,`catId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;