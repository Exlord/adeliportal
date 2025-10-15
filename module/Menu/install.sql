CREATE TABLE IF NOT EXISTS `tbl_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuTitle` varchar(400) DEFAULT NULL,
  `menuName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menuName` (`menuName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `tbl_menu` (`id`, `menuTitle`, `menuName`) VALUES
(1, 'منوی بالا', 'top_menu'),
(2, 'منوی پایین', 'bottom_menu');




CREATE TABLE IF NOT EXISTS `tbl_menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuId` int(11) NOT NULL,
  `parentId` int(11) DEFAULT '0',
  `itemTitle` varchar(400) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
  `itemName` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
  `itemUrlType` varchar(100) DEFAULT NULL,
  `itemUrlTypeParams` text,
  `itemOrder` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `level` int(11) NOT NULL DEFAULT '0',
  `config` text,
  `image` text,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `menuId` (`menuId`),
  KEY `itemOrder` (`itemOrder`),
  KEY `itemTitle` (`itemTitle`(255)),
  KEY `menuId_2` (`menuId`,`itemTitle`(255),`itemOrder`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `tbl_menu_item` (`id`, `menuId`, `parentId`, `itemTitle`, `itemName`, `itemUrlType`, `itemUrlTypeParams`, `itemOrder`, `status`, `level`, `config`) VALUES
(1, 1, 0, '', 'صفحه اصلی', 'frontPage', 'a:1:{s:9:"frontPage";a:1:{s:6:"params";a:1:{s:5:"route";s:14:"app/front-page";}}}', 0, 0, 0, 'a:1:{s:8:"megaMenu";a:2:{s:6:"isMega";s:1:"0";s:7:"columns";s:0:"";}}');