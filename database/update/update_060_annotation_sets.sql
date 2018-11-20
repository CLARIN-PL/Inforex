ALTER TABLE `annotation_sets` CHANGE `nested` `nested` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `annotation_types` CHANGE `cross_sentence` `cross_sentence` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `annotation_sets` CHANGE `description` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `annotation_sets` ADD `description` TEXT NOT NULL AFTER `name`;

ALTER TABLE `annotation_subsets` CHANGE `description` `name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `annotation_subsets` ADD `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `name`;

ALTER TABLE `activity_types` CHANGE `name` `name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
ALTER TABLE `annotation_types` CHANGE `level` `level` INT(11) NOT NULL DEFAULT '0';