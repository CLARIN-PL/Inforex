SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `tokens_tags_ctags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ctag` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE  `tokens_tags_optimized` ADD  `ctag_id` INT NOT NULL ;
ALTER TABLE  `tokens_tags_optimized` ADD FOREIGN KEY (  `ctag_id` ) REFERENCES  `inforex`.`tokens_tags_ctags` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO `tokens_tags_ctags`(`ctag`) SELECT DISTINCT ctag FROM `tokens_tags_optimized`;
UPDATE `tokens_tags_optimized` tto JOIN `tokens_tags_ctags` ttc USING(ctag) SET tto.ctag_id = ttc.id;

ALTER TABLE `tokens_tags_optimized`  DROP `base`,  DROP `ctag`;
ALTER TABLE  `tokens_tags_optimized` ADD  `pos` VARCHAR( 20 ) NOT NULL ;

UPDATE `tokens_tags_optimized` tto JOIN `tokens_tags_ctags` ttc USING(ctag_id) SET tto.pos = SUBSTRING_INDEX( SUBSTRING_INDEX(ttc.ctag,  ':', 1 ) ,  ':', -1 ) 
