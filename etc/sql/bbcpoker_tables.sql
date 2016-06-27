-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 27. Juni 2016 um 17:18
-- Server Version: 5.1.73
-- PHP-Version: 5.3.2-1ubuntu4.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `bbcpoker`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `fullname` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  KEY `active` (`active`),
  KEY `last_loginl` (`last_login`),
  KEY `username_2` (`username`,`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `awards`
--

DROP TABLE IF EXISTS `awards`;
CREATE TABLE IF NOT EXISTS `awards` (
  `awards_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) NOT NULL,
  `file` longblob NOT NULL,
  `filename` varchar(64) NOT NULL,
  `mime` varchar(64) NOT NULL,
  PRIMARY KEY (`awards_id`),
  UNIQUE KEY `month_2` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `banlist`
--

DROP TABLE IF EXISTS `banlist`;
CREATE TABLE IF NOT EXISTS `banlist` (
  `banlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `fingerprint` varchar(64) NOT NULL,
  `playername` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `reason` varchar(128) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`banlist_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `configuration`
--

DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(32) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`configuration_id`),
  KEY `group` (`group`),
  KEY `group_2` (`group`,`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `controllerinc`
--

DROP TABLE IF EXISTS `controllerinc`;
CREATE TABLE IF NOT EXISTS `controllerinc` (
  `controllerinc_id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(64) NOT NULL DEFAULT 'default',
  `controller` varchar(64) NOT NULL,
  `type` set('js','css') NOT NULL,
  `filename` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`controllerinc_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Controller Includes' AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gamedates`
--

DROP TABLE IF EXISTS `gamedates`;
CREATE TABLE IF NOT EXISTS `gamedates` (
  `gamedates_id` int(8) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `step` tinyint(4) NOT NULL DEFAULT '1',
  `played` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gamedates_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gameregs`
--

DROP TABLE IF EXISTS `gameregs`;
CREATE TABLE IF NOT EXISTS `gameregs` (
  `gameregs_id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(64) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `fingerprint` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  `step` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gameregs_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `player`
--

DROP TABLE IF EXISTS `player`;
CREATE TABLE IF NOT EXISTS `player` (
  `player_id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(64) NOT NULL,
  `awards` text,
  `avatar` longblob,
  `avatar_mime` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `playername` (`playername`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `results_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` enum('s1','s2','s3','s4','husc') NOT NULL,
  `date` datetime NOT NULL,
  `1st` varchar(32) NOT NULL,
  `2nd` varchar(32) NOT NULL,
  `3rd` varchar(32) NOT NULL,
  `4th` varchar(32) NOT NULL,
  `5th` varchar(32) NOT NULL,
  `6th` varchar(32) NOT NULL,
  `7th` varchar(32) NOT NULL,
  `8th` varchar(32) DEFAULT NULL,
  `9th` varchar(32) DEFAULT NULL,
  `10th` varchar(23) DEFAULT NULL,
  `log` longblob NOT NULL,
  PRIMARY KEY (`results_id`),
  KEY `table_month` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `settings_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shoutbox`
--

DROP TABLE IF EXISTS `shoutbox`;
CREATE TABLE IF NOT EXISTS `shoutbox` (
  `shoutbox_id` int(8) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `playername` varchar(32) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `fingerprint` varchar(64) NOT NULL,
  `msg` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`shoutbox_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=457 ;
