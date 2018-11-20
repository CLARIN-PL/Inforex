ALTER TABLE `reports` DROP FOREIGN KEY `reports_ibfk_7` ,
ADD FOREIGN KEY ( `subcorpus_id` ) REFERENCES `inforex`.`corpus_subcorpora` (
`subcorpus_id`
) ON DELETE SET NULL ON UPDATE CASCADE ;

ALTER TABLE `reports` DROP FOREIGN KEY `reports_ibfk_8` ,
ADD FOREIGN KEY ( `type` ) REFERENCES `inforex`.`reports_types` (
`id`
) ON DELETE SET NULL ON UPDATE CASCADE ;
