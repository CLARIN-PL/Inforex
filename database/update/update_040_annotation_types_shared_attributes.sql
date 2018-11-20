-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2015 at 06:22 PM
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
-- Table structure for table `annotation_types_shared_attributes`
--

CREATE TABLE IF NOT EXISTS `annotation_types_shared_attributes` (
  `annotation_type_id` int(11) NOT NULL,
  `shared_attribute_id` int(11) NOT NULL,
  KEY `annotation_type_id` (`annotation_type_id`),
  KEY `shared_attribute_id` (`shared_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annotation_types_shared_attributes`
--
ALTER TABLE `annotation_types_shared_attributes`
  ADD CONSTRAINT `annotation_types_shared_attributes_ibfk_2` FOREIGN KEY (`annotation_type_id`) REFERENCES `annotation_types` (`annotation_type_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `annotation_types_shared_attributes_ibfk_1` FOREIGN KEY (`shared_attribute_id`) REFERENCES `shared_attributes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
