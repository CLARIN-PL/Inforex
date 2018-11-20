ALTER TABLE `reports_annotations_optimized` DROP FOREIGN KEY `FK_reports_annotations_annotation_types` ,
ADD FOREIGN KEY ( `type_id` ) REFERENCES `gpw`.`annotation_types` (
`annotation_type_id`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `relations` DROP FOREIGN KEY `relations_ibfk_5` ,
ADD FOREIGN KEY ( `relation_type_id` ) REFERENCES `gpw`.`relation_types` (
`id`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `relations` DROP FOREIGN KEY `relations_ibfk_7` ,
ADD FOREIGN KEY ( `source_id` ) REFERENCES `gpw`.`reports_annotations_optimized` (
`id`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

ALTER TABLE `relations` DROP FOREIGN KEY `relations_ibfk_8` ,
ADD FOREIGN KEY ( `target_id` ) REFERENCES `gpw`.`reports_annotations_optimized` (
`id`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

