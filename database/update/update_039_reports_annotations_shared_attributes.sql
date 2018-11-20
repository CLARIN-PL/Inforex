-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 05, 2015 at 12:56 PM
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
-- Table structure for table `reports_annotations_shared_attributes`
--

CREATE TABLE IF NOT EXISTS `reports_annotations_shared_attributes` (
  `annotation_id` bigint(20) NOT NULL,
  `shared_attribute_id` int(11) NOT NULL,
  `value` varchar(45) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `unique_annotations_shared_attributes_values` (`annotation_id`,`shared_attribute_id`),
  KEY `annotation_id` (`annotation_id`),
  KEY `shared_attribute_id` (`shared_attribute_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports_annotations_shared_attributes`
--
ALTER TABLE `reports_annotations_shared_attributes`
  ADD CONSTRAINT `reports_annotations_shared_attributes_ibfk_1` FOREIGN KEY (`annotation_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `reports_annotations_shared_attributes_ibfk_2` FOREIGN KEY (`shared_attribute_id`) REFERENCES `shared_attributes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `reports_annotations_shared_attributes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
