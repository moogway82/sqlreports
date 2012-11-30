-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 30, 2012 at 08:44 AM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `database`
--

INSERT INTO `database` VALUES(1, 'datasheets1', 'root:root@localhost', 'datasheets1');
INSERT INTO `database` VALUES(2, 'Info Schema', 'root:root@localhost', 'information_schema');
INSERT INTO `database` VALUES(3, 'retweetstats', 'root:root@localhost', 'retweetstats');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `database` int(11) NOT NULL COMMENT 'The database that this SQL to to be run against',
  `name` varchar(255) NOT NULL COMMENT 'name of the report',
  `sql` text NOT NULL COMMENT 'The actual SQL of the query',
  `slug` varchar(255) NOT NULL,
  `notes` text NOT NULL COMMENT 'Notes for the report',
  `settings` text NOT NULL COMMENT 'Display settings go here?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `report`
--

INSERT INTO `report` VALUES(1, 1, 'firstreport', 'SELECT *\r\nFROM user;', 'firstreport', 'This is my first report', '');
INSERT INTO `report` VALUES(3, 2, 'partitions', 'SELECT * \r\nFROM PARTITIONS\r\nLIMIT 0, 1000', 'partitions', 'Info Schema PARTITIONS table.  Limited to 1000 rows.', '');
INSERT INTO `report` VALUES(4, 3, 'retweetshour', 'SELECT\r\n\r\n  HOUR(tweet.created_date),\r\n\r\n  COUNT(DISTINCT tweet.id) AS Tweets,\r\n\r\n  COUNT(retweet.id) AS Retweets,\r\n\r\n  (COUNT(retweet.id) / COUNT(DISTINCT tweet.id)) AS Ratio\r\n\r\n \r\n\r\nFROM tweet\r\n\r\n  INNER JOIN retweet ON (tweet.id = retweet.tweet)\r\n\r\n \r\n\r\nGROUP BY HOUR(tweet.created_date)\r\n\r\n \r\n\r\nORDER BY HOUR(tweet.created_date)', 'retweetshour', 'This shows the best time to get retweets.', '');
INSERT INTO `report` VALUES(5, 3, 'retweetspertweetalltime', 'SELECT tweet.id, tweet.text, tweet.created_date, COUNT(retweet.id)\r\nFROM tweet\r\n  INNER JOIN retweet ON (tweet.id = retweet.tweet)\r\nGROUP BY tweet.id', 'retweetspertweetalltime', 'Retweets per tweet all time.', '');
INSERT INTO `report` VALUES(6, 3, 'rt-testvars', 'SELECT retweet.id, retweet.text, retweet.created_date\r\nFROM retweet\r\nWHERE retweet.created_date BETWEEN "{{datefrom|2011-09-20 09:00}}" AND "{{dateto|2011-09-20 12:00}}"\r\n', 'rt-testvars', 'Testing my new variable placeholder syntax.', '');
INSERT INTO `report` VALUES(7, 3, 'rt-tweet-by-text', 'SELECT tweet.id, tweet.created_date, tweet.text\r\nFROM tweet\r\nWHERE tweet.text LIKE ''%{{tweettext|CBE}}%''', 'rt-tweet-by-text', '', '');
INSERT INTO `report` VALUES(8, 3, 'rt-retweets-per-tweet', 'SELECT tweet.id, tweet.text, count(retweet.id)\r\nFROM tweet\r\n  INNER JOIN retweet ON (tweet.id = retweet.tweet)\r\nWHERE retweet.created_date BETWEEN "{{from|2011-09-20 00:00}}" AND "{{to|2011-09-20 23:59}}"\r\nGROUP BY tweet.id', 'rt-retweets-per-tweet', '', '');
INSERT INTO `report` VALUES(9, 3, 'rt-test-sort', 'SELECT tweet.id, tweet.created_date, tweet.text\r\nFROM tweet\r\nLIMIT 0,10', 'rt-test-sort', '', '');
INSERT INTO `report` VALUES(10, 3, 'rt-two-vars', 'SELECT {{field|tweet.text}}\r\nFROM tweet\r\nWHERE {{field|text}} LIKE "%{{value|cbe}}%"\r\nORDER BY {{field|text}}', 'rt-two-vars', '', '');
INSERT INTO `report` VALUES(11, 3, 'Keyword Report', 'SELECT ''{{keyword1|getcbe}}'' AS "text", ROUND(AVG (a.rt_count),2) AS "Average RTs over last 100 days"\r\nFROM\r\n (SELECT COUNT(retweet.id) AS rt_count\r\n FROM tweet LEFT OUTER JOIN retweet ON (tweet.id = retweet.tweet)\r\n WHERE tweet.text LIKE ''%{{keyword1|getcbe}}%''\r\n AND tweet.created_date >= (CURDATE() - INTERVAL {{days|100}} DAY)\r\n GROUP BY tweet.id\r\n ) AS a\r\nUNION\r\nSELECT ''{{keyword2|beecause}}'' AS "text", ROUND(AVG (a.rt_count),2) AS "Average RTs over last 100 days"\r\nFROM\r\n (SELECT COUNT(retweet.id) AS rt_count\r\n FROM tweet LEFT OUTER JOIN retweet ON (tweet.id = retweet.tweet)\r\n WHERE tweet.text LIKE ''%{{keyword2|beecause}}%''\r\n AND tweet.created_date >= (CURDATE() - INTERVAL {{days|100}} DAY)\r\n GROUP BY tweet.id\r\n ) AS a \r\nUNION\r\nSELECT ''{{keyword3|green tip}}'' AS "text", ROUND(AVG (a.rt_count),2) AS "Average RTs over last 100 days"\r\nFROM\r\n (SELECT COUNT(retweet.id) AS rt_count\r\n FROM tweet LEFT OUTER JOIN retweet ON (tweet.id = retweet.tweet)\r\n WHERE tweet.text LIKE ''%{{keyword3|green tip}}%''\r\n AND tweet.created_date >= (CURDATE() - INTERVAL {{days|100}} DAY)\r\n GROUP BY tweet.id\r\n ) AS a \r\nUNION\r\n\r\nSELECT ''{{keyword4|makeitbetter}}'' AS "text", ROUND(AVG (a.rt_count),2) AS "Average RTs over last 100 days"\r\nFROM\r\n (SELECT COUNT(retweet.id) AS rt_count\r\n FROM tweet LEFT OUTER JOIN retweet ON (tweet.id = retweet.tweet)\r\n WHERE tweet.text LIKE ''%{{keyword4|makeitbetter}}%''\r\n AND tweet.created_date >= (CURDATE() - INTERVAL {{days|100}} DAY)\r\n GROUP BY tweet.id\r\n ) AS a \r\nUNION\r\nSELECT ''{{keyword5|Take action}}'' AS "text", ROUND(AVG (a.rt_count),2) AS "Average RTs over last 100 days"\r\nFROM\r\n (SELECT COUNT(retweet.id) AS rt_count\r\n FROM tweet LEFT OUTER JOIN retweet ON (tweet.id = retweet.tweet)\r\n WHERE tweet.text LIKE ''%{{keyword5|Take action}}%''\r\n AND tweet.created_date >= (CURDATE() - INTERVAL {{days|100}} DAY)\r\n GROUP BY tweet.id\r\n ) AS a \r\nUNION\r\nSELECT ''{{keyword6|Blog:}}'' AS "text", ROUND(AVG (a.rt_count),2) AS "Average RTs over last 100 days"\r\nFROM\r\n (SELECT COUNT(retweet.id) AS rt_count\r\n FROM tweet LEFT OUTER JOIN retweet ON (tweet.id = retweet.tweet)\r\n WHERE tweet.text LIKE ''%{{keyword6|Blog:}}%''\r\n AND tweet.created_date >= (CURDATE() - INTERVAL {{days|100}} DAY)\r\n GROUP BY tweet.id\r\n ) AS a ', 'rt-keyword', '', '');

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

