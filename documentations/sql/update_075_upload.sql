ALTER TABLE `reports` ADD `filename` TEXT NULL AFTER `lang`;
ALTER TABLE `reports` CHANGE `tokenization` `tokenization` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NULL;

ALTER TABLE `reports` CHANGE `date` `date` DATE NULL,
CHANGE `title` `title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
CHANGE `source` `source` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
CHANGE `author` `author` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;