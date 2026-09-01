CREATE TABLE `corpus_report_perspective_annotation_sets` (
  `corpus_id` int(11) NOT NULL,
  `perspective_id` varchar(32) CHARACTER SET utf8 NOT NULL,
  `annotation_set_id` int(11) NOT NULL,
  UNIQUE KEY `corpus_perspective_annotation_set_unique` (`corpus_id`,`perspective_id`,`annotation_set_id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  KEY `perspective_id` (`perspective_id`),
  CONSTRAINT `corpus_report_perspective_annotation_sets_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_report_perspective_annotation_sets_ibfk_2` FOREIGN KEY (`perspective_id`) REFERENCES `report_perspectives` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_report_perspective_annotation_sets_ibfk_3` FOREIGN KEY (`annotation_set_id`) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reports_document_annotation_types` (
  `report_id` bigint(20) NOT NULL,
  `annotation_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  UNIQUE KEY `reports_document_annotation_type_unique` (`report_id`,`annotation_type_id`),
  KEY `annotation_type_id` (`annotation_type_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reports_document_annotation_types_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_document_annotation_types_ibfk_2` FOREIGN KEY (`annotation_type_id`) REFERENCES `annotation_types` (`annotation_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_document_annotation_types_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`)
VALUES ('document_annotation_categories', 'Document annotation categories', 'Assign annotation categories to a whole document.', 26);