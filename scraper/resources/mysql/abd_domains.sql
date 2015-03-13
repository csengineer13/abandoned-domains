--
-- Table structure for table `abd_domains`
--

CREATE TABLE IF NOT EXISTS `abd_domains` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `UserId` int(10) NOT NULL,
  `Domain` varchar(200) NOT NULL,
  `TLD` varchar(200) NOT NULL,
  `LastChecked` datetime NOT NULL,
  `CreatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `UserId` (`UserId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;