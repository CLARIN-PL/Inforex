ALTER TABLE `annotation_types`
	ADD COLUMN `annotation_type_id` INT NOT NULL AUTO_INCREMENT FIRST,
	ADD PRIMARY KEY (`annotation_type_id`);
	
ALTER TABLE `reports_annotations`
	ADD COLUMN `type_id` INT(11) NULL DEFAULT NULL AFTER `type`;
	
ALTER TABLE `reports_annotations`
	DROP FOREIGN KEY `reports_annotations_ibfk_1`;
	
UPDATE reports_annotations 
SET type_id = (SELECT annotation_type_id FROM annotation_types WHERE annotation_types.name=reports_annotations.type COLLATE utf8_bin);

ALTER TABLE `reports_annotations`
	DROP COLUMN `type`;

ALTER TABLE `reports_annotations`
	ADD CONSTRAINT `FK_annotations_types` FOREIGN KEY (`type_id`) REFERENCES `annotation_types` (`annotation_type_id`) ON UPDATE CASCADE ON DELETE CASCADE;

RENAME TABLE `reports_annotations` TO `reports_annotations_optimized`;

CREATE ALGORITHM = UNDEFINED VIEW `reports_annotations` AS 
	SELECT ra.id, report_id, type_id, at.name AS type, `from`, `to`, text, user_id, creation_time, stage, source
	FROM reports_annotations_optimized AS ra
	LEFT JOIN annotation_types AS at ON at.annotation_type_id=ra.type_id 
    