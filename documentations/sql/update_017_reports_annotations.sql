ALTER TABLE `reports_annotations` ADD `source` ENUM( 'user', 'bootstrapping' ) NOT NULL DEFAULT 'user';
UPDATE `reports_annotations` SET `source` = "user" WHERE `stage` = "user";
UPDATE `reports_annotations` SET `source` = "bootstrapping" WHERE `stage` = "candidate";
ALTER TABLE `reports_annotations` CHANGE `stage` `stage` ENUM( 'new', 'final', 'discarded' ) NOT NULL DEFAULT 'final';
UPDATE `reports_annotations` SET `stage` = "final" WHERE `source` = "user";
UPDATE `reports_annotations` SET `stage` = "new" WHERE `source` = "bootstrapping";
UPDATE `report_perspectives` SET `title` = 'Annotation Bootstrapping' WHERE `report_perspectives`.`id` = 'autoextension';