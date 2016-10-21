-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 21. Okt 2016 um 23:48
-- Server-Version: 10.1.13-MariaDB
-- PHP-Version: 7.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `quiz`
--

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `unid`, `username`, `password`, `email`, `phone`, `status`, `fname`, `lname`, `tel1`, `tel2`, `tel3`, `sex`, `dborn`, `about`, `avatar`, `created`, `updated`, `lastact`, `reset`) VALUES
(1, 'padwaokf6u', 'admin', 'enter1234', 'support@quizz.de', NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 'avatar.gif', '2016-09-23 23:06:53', '2016-10-21 21:18:06', '2016-10-21 21:18:06', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
