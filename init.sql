-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 30, 2015 at 10:19 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `sarah`
--

-- --------------------------------------------------------

--
-- Table structure for table `budget`
--

CREATE TABLE IF NOT EXISTS `budget` (
  `BUDGET_ID` int(11) NOT NULL AUTO_INCREMENT,
  `USER_ID` int(11) NOT NULL,
  `store` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`BUDGET_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=771 ;

-- --------------------------------------------------------

--
-- Table structure for table `budget_items`
--

CREATE TABLE IF NOT EXISTS `budget_items` (
  `BUDGET_ITEM_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BUDGET_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `amount` float NOT NULL,
  `item_name` text NOT NULL,
  `size` float NOT NULL,
  `size_unit` text NOT NULL,
  `brand` text NOT NULL,
  `category` text NOT NULL,
  `tax` float NOT NULL,
  `sale` tinyint(1) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`BUDGET_ITEM_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=832 ;

-- --------------------------------------------------------

--
-- Table structure for table `budget_recurring`
--

CREATE TABLE IF NOT EXISTS `budget_recurring` (
  `RECURRING_ID` int(11) NOT NULL AUTO_INCREMENT,
  `USER_ID` int(11) NOT NULL,
  `amount` float NOT NULL,
  `category` text NOT NULL,
  `store` text NOT NULL,
  `items` text NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  PRIMARY KEY (`RECURRING_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

