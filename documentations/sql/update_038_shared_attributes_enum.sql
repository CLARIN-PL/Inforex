-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 05, 2015 at 01:55 PM
-- Server version: 5.5.41-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `inforex`
--

-- --------------------------------------------------------

--
-- Table structure for table `shared_attributes_enum`
--

CREATE TABLE IF NOT EXISTS `shared_attributes_enum` (
  `shared_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(45) CHARACTER SET utf8 NOT NULL,
  `description` varchar(45) CHARACTER SET utf8 NOT NULL,
  KEY `shared_attribute_id` (`shared_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `shared_attributes_enum`
--
ALTER TABLE `shared_attributes_enum`
  ADD CONSTRAINT `shared_attributes_enum_ibfk_1` FOREIGN KEY (`shared_attribute_id`) REFERENCES `shared_attributes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
