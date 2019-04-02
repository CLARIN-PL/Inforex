--liquibase formatted sql

--changeset czuk:0 splitStatements:false endDelimiter:#

CREATE PROCEDURE changeFlagStatus(
  IN flag_id INT(11),
  IN flag_status INT(11),
  IN report_id INT(11),
  IN user_id INT(11))
  BEGIN
    -- Previous flag status.
    DECLARE old_status INT(11);

    -- Store the previous flag status into old_status variable.
    SELECT rf.flag_id INTO old_status FROM reports_flags rf
    WHERE (rf.report_id = report_id AND rf.corpora_flag_id = flag_id);

    -- Update the document's flag status.
    REPLACE INTO reports_flags(corpora_flag_id, report_id, flag_id)
    VALUES(flag_id, report_id, flag_status);

    -- Store the change in the flag status history table.
    INSERT INTO flag_status_history (date, report_id, flag_id, user_id, new_status, old_status)
    VALUES (CURRENT_TIMESTAMP, report_id, flag_id, user_id, flag_status, IFNULL(old_status,-1));
  END
#


--changeset czuk:1

ALTER TABLE `reports_users_selection` ADD INDEX(`user_id`);

ALTER TABLE `reports_users_selection` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD INDEX(`report_id`);

ALTER TABLE `reports_users_selection` ADD  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports_users_selection` ADD UNIQUE( `user_id`, `report_id`);


--changeset czuk:2

ALTER TABLE `tasks_reports` CHANGE `message` `message` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;