ALTER TABLE `reports` ADD `parent_report_id` BIGINT(20) NULL DEFAULT NULL AFTER `filename`;
ALTER TABLE reports ADD CONSTRAINT fk_parent_report_id FOREIGN KEY (parent_report_id) REFERENCES reports(id);
INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`) VALUES ('edittranslation', 'Edit translation', 'Edit document translation', '320');
INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`) VALUES ('extendedmetadata', 'Extended metadata', 'Metadata including translations, images and document content.', '325');