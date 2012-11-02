-- Server version: 4.1.22

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure for table `Posts`
--

CREATE TABLE IF NOT EXISTS `Posts` (
  `ID` int(15) NOT NULL auto_increment,
  `ThreadID` int(15) NOT NULL default '0',
  `PostID` int(15) NOT NULL default '0',
  `PostTime` int(15) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Threads`
--

CREATE TABLE IF NOT EXISTS `Threads` (
  `ID` int(15) NOT NULL auto_increment,
  `ThreadID` int(15) NOT NULL default '0',
  `Board` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `Chan` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `Description` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `Status` int(1) NOT NULL default '0',
  `LastChecked` int(15) NOT NULL default '0',
  `TimeAdded` int(15) NOT NULL default '0',
  `CheckDelay` int(11) NOT NULL default '30',
  `FirstImageFilename` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

