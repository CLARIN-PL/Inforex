ALTER TABLE `relations` DROP FOREIGN KEY `relations_ibfk_2`;
ALTER TABLE `relations` ADD CONSTRAINT `relations_ibfk_2` FOREIGN KEY (`source_id`) REFERENCES `reports_annotations` (`id`) ON DELETE CASCADE;
ALTER TABLE `relations` DROP FOREIGN KEY `relations_ibfk_3`;
ALTER TABLE `relations` ADD CONSTRAINT `relations_ibfk_3` FOREIGN KEY (`target_id`) REFERENCES `reports_annotations` (`id`) ON DELETE CASCADE;