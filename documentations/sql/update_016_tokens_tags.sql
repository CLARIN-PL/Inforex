CREATE TABLE `tokens_tags` (
	`token_tag_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ,
	`token_id` BIGINT( 20 ) NOT NULL ,
	`base` VARCHAR( 32 ) NOT NULL ,
	`ctag` VARCHAR( 32 ) NOT NULL ,
	`disamb` BOOLEAN NOT NULL ,
	PRIMARY KEY ( `token_tag_id` ) ,
	CONSTRAINT `fk_token_id` FOREIGN KEY  (`token_id`)
    	REFERENCES `tokens` (`token_id`)
    	ON UPDATE CASCADE
    	ON DELETE CASCADE
) ENGINE = InnoDB;

