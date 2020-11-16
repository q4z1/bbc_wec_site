-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 27. Juni 2016 um 17:19
-- Server Version: 5.1.73
-- PHP-Version: 5.3.2-1ubuntu4.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `bbcpoker`
--

--
-- Daten für Tabelle `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `fullname`, `email`, `active`) VALUES
(1, 'sp0ck', 'xxxxxxxxxx', '', NULL, 1),
(2, 'boehmi', 'xxxxxxxxxx', '', NULL, 1),
(3, 'Emmeler', 'xxxxxxxxxx', '', NULL, 1),
(4, 'M4N!4C', 'xxxxxxxxxx', '', NULL, 1);

--
-- Daten für Tabelle `configuration`
--

INSERT INTO `configuration` (`configuration_id`, `group`, `key`, `value`) VALUES
(1, 'core', 'title', 'Weekly Cup'),
(2, 'head', 'js', 'jquery-1.12.0.min'),
(3, 'head', 'js', 'base'),
(4, 'head', 'css', 'base'),
(5, 'head', 'css', 'bootstrap.min'),
(6, 'head', 'js', 'bootstrap.min'),
(7, 'head', 'js', 'bootbox.min'),
(8, 'head', 'js', 'jquery.md5');

--
-- Daten für Tabelle `controllerinc`
--

INSERT INTO `controllerinc` (`controllerinc_id`, `template`, `controller`, `type`, `filename`, `active`) VALUES
(1, 'default', 'main_login', 'js', 'login', 1),
(3, 'default', 'admin_upload', 'js', 'upload', 1),
(4, 'default', 'admin_award', 'js', 'upload', 1),
(5, 'default', 'admin_award', 'js', 'fileinput.min', 1),
(6, 'default', 'admin_award', 'css', 'fileinput.min', 1),
(7, 'default', 'admin_award', 'js', 'jquery.form.min', 1),
(8, 'default', 'admin_award', 'js', 'award', 1),
(9, 'default', 'admin_signup', 'js', 'signup', 1),
(10, 'default', 'admin_settings', 'js', 'settings', 1),
(11, 'default', 'main_shoutbox', 'js', 'shoutbox', 1),
(12, 'default', 'main_shoutbox', 'js', 'jquery.bootpag.min', 1),
(13, 'default', 'main_games', 'js', 'games', 1),
(14, 'default', 'main_register', 'js', 'register', 1);

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`settings_id`, `type`, `value`) VALUES
(1, 'footer', '<div class="text-center"><a href="http://www.pokerth.net/live.html" target="_blank"><h3>&gt;&gt; SPECTATE WEC &lt;&lt;</h3></a></div>');
