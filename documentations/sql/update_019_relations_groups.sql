CREATE TABLE `relations_groups` (
  `relation_type_id` int(11) NOT NULL,
  `part` enum('source','target') NOT NULL,
  `annotation_set_id` int(11) DEFAULT NULL,
  `annotation_subset_id` int(11) DEFAULT NULL,
  KEY `relation_type_id` (`relation_type_id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  KEY `annotation_subset_id` (`annotation_subset_id`)  
) ENGINE=InnoDB;

ALTER TABLE `relations_groups` ADD FOREIGN KEY ( `relation_type_id` ) REFERENCES `relation_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `relations_groups` ADD FOREIGN KEY ( `annotation_set_id` ) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `relations_groups` ADD FOREIGN KEY ( `annotation_subset_id` ) REFERENCES `annotation_subsets` (`annotation_subset_id`) ON DELETE CASCADE ON UPDATE CASCADE ;