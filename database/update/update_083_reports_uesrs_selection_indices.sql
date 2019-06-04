ALTER TABLE `reports_users_selection` ADD INDEX(`user_id`);

ALTER TABLE `reports_users_selection` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD INDEX(`report_id`);

ALTER TABLE `reports_users_selection` ADD  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD UNIQUE( `user_id`, `report_id`);