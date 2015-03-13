--
-- Table structure for table `abd_request_log`
--

CREATE TABLE IF NOT EXISTS `abd_request_log` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `IsRead` tinyint(1) NOT NULL DEFAULT '0',
  `From` varchar(250) NOT NULL DEFAULT '',
  `Date` varchar(100) NOT NULL DEFAULT '',
  `Subject` varchar(250) NOT NULL DEFAULT '',
  `Message` varchar(2000) NOT NULL DEFAULT '',
  `F_IsBanned` tinyint(1) NOT NULL DEFAULT '0',
  `F_IsTodayBlocked` tinyint(1) NOT NULL DEFAULT '0',
  `F_IsMonthBlocked` tinyint(1) NOT NULL DEFAULT '0',
  `F_IsValid` tinyint(1) NOT NULL DEFAULT '0',
  `F_IsInvalid` tinyint(1) NOT NULL DEFAULT '0',
  `F_IsDuplicate` tinyint(1) NOT NULL DEFAULT '0',
  `F_IsBannable` tinyint(1) NOT NULL DEFAULT '0',
  `LoggedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;