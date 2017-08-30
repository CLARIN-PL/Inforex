--create table tagsets

CREATE TABLE IF NOT EXISTS `tagsets` (
  `tagset_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`tagset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


-- add fields to tokens_tags_optimized
--  user default NULL

ALTER TABLE  `tokens_tags_optimized` ADD  `user_id` INT NULL DEFAULT NULL;

-- adding foreign key constraints
ALTER TABLE `tokens_tags_optimized`
ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`);

-- adding tagset_id to tags
ALTER TABLE  `tokens_tags_ctags`
ADD COLUMN tagset_id INT DEFAULT NULL ,
ADD FOREIGN KEY  `fk_tagset` ( tagset_id ) REFERENCES tagsets( tagset_id ) ;

-- delete unique on ctag
ALTER TABLE  `tokens_tags_ctags` DROP INDEX  `ctag_UNIQUE` ;

-- add uniqe on ctag and tagset
ALTER TABLE  `tokens_tags_ctags` ADD CONSTRAINT  `ctag_tagset_UNIQUE` UNIQUE (
`ctag` ,
`tagset_id`
);
