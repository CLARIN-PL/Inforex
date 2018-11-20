ALTER TABLE  `reports` CHANGE  `date`  `date` DATE NULL DEFAULT NULL ,
CHANGE  `title`  `title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `source`  `source` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `author`  `author` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `content`  `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL