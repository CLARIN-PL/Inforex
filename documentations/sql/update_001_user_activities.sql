CREATE TABLE `gpw`.`user_activities` (
`user_id` INT NOT NULL ,
`started` DATETIME NOT NULL ,
`ended` DATETIME NOT NULL ,
`counter` INT NOT NULL ,
INDEX ( `user_id` , `ended` )
) ENGINE = MYISAM ;

ALTER TABLE `user_activities` ADD `login` BOOLEAN NOT NULL ;
ALTER TABLE `user_activities` ADD `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
