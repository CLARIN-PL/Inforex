CREATE TABLE IF NOT EXISTS `annotation_sets_corpora` (
  `annotation_set_id` int(11) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  PRIMARY KEY (`annotation_set_id`,`corpus_id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  KEY `corpus_id` (`corpus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;