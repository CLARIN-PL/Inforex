INSERT INTO `roles` (`role`, `description`) VALUES ('create_corpus', 'Prawo do tworzenia nowych korpusów.');

ALTER TABLE `corpora` CHANGE `ext` `ext` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NULL;
