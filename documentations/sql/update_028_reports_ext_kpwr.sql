CREATE TABLE `gpw`.`reports_ext_kpwr` (
`id` BIGINT NOT NULL ,
`keywords` TEXT NOT NULL ,
PRIMARY KEY ( `report_id` )
) ENGINE = InnoDB;

ALTER TABLE `reports_ext_kpwr` ADD FOREIGN KEY ( `id` ) REFERENCES `gpw`.`reports` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
