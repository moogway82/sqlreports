-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2012 at 07:05 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sqlreports`
--
CREATE DATABASE `sqlreports` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sqlreports`;

-- --------------------------------------------------------

--
-- Table structure for table `database`
--

CREATE TABLE `database` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `database` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `database`
--

INSERT INTO `database` VALUES(1, 'datasheets1', 'root:root@localhost', 'datasheets1');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `database` int(11) NOT NULL COMMENT 'The database that this SQL to to be run against',
  `name` varchar(255) NOT NULL COMMENT 'name of the report',
  `sql` text NOT NULL COMMENT 'The actual SQL of the query',
  `notes` text NOT NULL COMMENT 'Notes for the report',
  `settings` text NOT NULL COMMENT 'Display settings go here?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `report`
--

INSERT INTO `report` VALUES(1, 1, 'firstreport', 'SELECT *\r\nFROM incident;', 'This is my first report', '');
INSERT INTO `report` VALUES(3, 0, 'testing', 'SELECT id, name\r\nFROM report', 'This is my <b>second</b> report!<br />\r\nWhoop.<br />\r\nWhoop.', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` int(11) NOT NULL COMMENT '0 = admin, 1 = regular',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user`
--

