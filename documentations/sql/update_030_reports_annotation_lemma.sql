INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`) VALUES ('annotation_lemma', 'Annotation Lemmas', '', '15');

-- phpMyAdmin SQL Dump
-- version 4.0.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 06, 2013 at 02:36 PM
-- Server version: 5.5.32-0ubuntu3
-- PHP Version: 5.5.3-1ubuntu1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `inforex`
--

-- --------------------------------------------------------

--
-- Table structure for table `reports_annotations_lemma`
--

CREATE TABLE IF NOT EXISTS `reports_annotations_lemma` (
  `report_annotation_id` bigint(20) NOT NULL,
  `lemma` mediumtext COLLATE utf8_polish_ci NOT NULL,
  UNIQUE KEY `report_annotation_id` (`report_annotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports_annotations_lemma`
--
ALTER TABLE `reports_annotations_lemma`
  ADD CONSTRAINT `reports_annotations_lemma_ibfk_1` FOREIGN KEY (`report_annotation_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

