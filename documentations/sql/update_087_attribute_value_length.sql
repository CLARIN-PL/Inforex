ALTER TABLE `reports_annotations_shared_attributes` CHANGE `value` `value` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `shared_attributes_enum` CHANGE `value` `value` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `shared_attributes_enum` CHANGE `description` `description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;