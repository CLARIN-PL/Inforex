ALTER TABLE `reports` ADD `filename` TEXT NULL AFTER `lang`;
ALTER TABLE `reports` CHANGE `tokenization` `tokenization` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NULL;