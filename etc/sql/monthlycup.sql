-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 12. Februar 2016 um 15:50
-- Server Version: 5.1.73
-- PHP-Version: 5.3.2-1ubuntu4.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `monthlycup`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `admin`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `award2016`
--

CREATE TABLE IF NOT EXISTS `award2016` (
  `award2016_id` int(8) NOT NULL AUTO_INCREMENT,
  `month` int(2) NOT NULL,
  `type` varchar(64) NOT NULL,
  `file` longblob NOT NULL,
  `filename` varchar(64) NOT NULL,
  `mime` varchar(64) NOT NULL,
  PRIMARY KEY (`award2016_id`),
  UNIQUE KEY `month_2` (`month`,`type`),
  KEY `month` (`month`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `award2016`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(32) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`configuration_id`),
  KEY `group` (`group`),
  KEY `group_2` (`group`,`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `configuration`
--

INSERT INTO `configuration` (`configuration_id`, `group`, `key`, `value`) VALUES
(1, 'leftnavi', 'Startseite', ''),
(2, 'core', 'title', 'PokerTH Monthly Cup'),
(3, 'head', 'js', 'jquery-1.12.0.min'),
(4, 'head', 'css', 'font-awesome.min'),
(5, 'head', 'js', 'base'),
(6, 'head', 'css', 'base'),
(7, 'head', 'css', 'bootstrap.min'),
(8, 'head', 'css', 'bootstrap-theme.min'),
(9, 'head', 'js', 'bootstrap.min');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `controllerinc`
--

CREATE TABLE IF NOT EXISTS `controllerinc` (
  `controllerinc_id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(64) NOT NULL DEFAULT 'default',
  `controller` varchar(64) NOT NULL,
  `type` set('js','css') NOT NULL,
  `filename` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`controllerinc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Controller Includes' AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `controllerinc`
--

INSERT INTO `controllerinc` (`controllerinc_id`, `template`, `controller`, `type`, `filename`, `active`) VALUES
(1, 'default', 'main_default', 'js', 'login', 1),
(2, 'default', 'main_default', 'js', 'md5', 1),
(3, 'default', 'admin_upload', 'js', 'upload', 1),
(4, 'default', 'admin_award', 'js', 'upload', 1),
(5, 'default', 'admin_award', 'js', 'fileinput.min', 1),
(7, 'default', 'admin_award', 'css', 'fileinput.min', 1),
(8, 'default', 'admin_award', 'js', 'jquery.form.min', 1),
(9, 'default', 'admin_award', 'js', 'award', 1),
(10, 'default', 'admin_signup', 'js', 'signup', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `player2016`
--

CREATE TABLE IF NOT EXISTS `player2016` (
  `player2016_id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(64) NOT NULL,
  `awards` text,
  `avatar` longblob,
  `avatar_mime` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`player2016_id`),
  KEY `playername` (`playername`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `player2016`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `upload2016`
--

CREATE TABLE IF NOT EXISTS `upload2016` (
  `uploads2016_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` enum('firstround','final') NOT NULL,
  `table_` varchar(16) NOT NULL,
  `month` int(2) NOT NULL,
  `playername` varchar(32) NOT NULL,
  `position` int(2) NOT NULL,
  `points` int(2) NOT NULL,
  PRIMARY KEY (`uploads2016_id`),
  KEY `table_month` (`table_`,`month`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `upload2016`
--

--
-- Tabellenstruktur für Tabelle `signup2016`
--

CREATE TABLE IF NOT EXISTS `signup2016` (
  `signup2016_id` int(8) NOT NULL AUTO_INCREMENT,
  `month` int(2) NOT NULL,
  `playername` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`signup2016_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;