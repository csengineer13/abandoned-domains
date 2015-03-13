--
-- Table structure for table `abd_users`
--

CREATE TABLE IF NOT EXISTS `abd_users` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(200) NOT NULL,
  `LastName` varchar(200) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `IsBanned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;