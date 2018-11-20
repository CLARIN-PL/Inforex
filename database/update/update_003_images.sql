CREATE TABLE `images` (
	`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`corpus_id` INT NOT NULL,
	`original_name` VARCHAR(64) NOT NULL ,
	`hash_name` VARCHAR(32) NOT NULL
) ;

CREATE TABLE `reports_and_images` (
	`report_id` INT NOT NULL ,
	`images_id` INT NOT NULL ,
	`position` INT NOT NULL ,
	PRIMARY KEY ( `report_id` , `images_id` )
) ;