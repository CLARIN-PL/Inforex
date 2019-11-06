ALTER TABLE `reports` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `reports_diffs` CHANGE `diff` `diff` BLOB NOT NULL, CHANGE `comment` `comment` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `reports_annotations_lemma` CHANGE `lemma` `lemma` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `reports_annotations_optimized` CHANGE `text` `text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;