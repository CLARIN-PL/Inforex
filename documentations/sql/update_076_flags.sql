-- Flag status history table.
CREATE TABLE `flag_status_history` (
  `report_id` bigint(20) NOT NULL,
  `flag_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `new_status` int(11) NOT NULL,
  `old_status` int(11) NOT NULL
) ENGINE=InnoDB;

ALTER TABLE `flag_status_history`
  ADD KEY `report_id` (`report_id`),
  ADD KEY `flag_id` (`flag_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `new_status` (`new_status`),
  ADD KEY `old_status` (`old_status`);

ALTER TABLE `flag_status_history`
  ADD CONSTRAINT `flag_status_history_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  ADD CONSTRAINT `flag_status_history_ibfk_2` FOREIGN KEY (`flag_id`) REFERENCES `corpora_flags` (`corpora_flag_id`),
  ADD CONSTRAINT `flag_status_history_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `flag_status_history_ibfk_4` FOREIGN KEY (`new_status`) REFERENCES `flags` (`flag_id`),
  ADD CONSTRAINT `flag_status_history_ibfk_5` FOREIGN KEY (`old_status`) REFERENCES `flags` (`flag_id`);

ALTER TABLE `flag_status_history` ADD `id` BIGINT(22) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `flag_status_history` ADD `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `old_status`;

-- Stored procedure
DROP procedure IF EXISTS `changeFlagStatus`;

DELIMITER $$
CREATE PROCEDURE `changeFlagStatus`(
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
    INSERT INTO flag_status_history (report_id, flag_id, user_id, new_status, old_status)
    VALUES (report_id, flag_id, user_id, flag_status, IFNULL(old_status,-1));
  END$$

DELIMITER ;

-- Adding perspective
INSERT INTO `report_perspectives` (`id`, `title`, `description`, `order`) VALUES ('flag_history', 'Flag history', 'Show the history of flag changes.', '320');

-- Adding corpus role
INSERT INTO `corpus_roles` (`role`, `description`, `description_long`) VALUES ('flag_history', 'Sprawdzanie historii flag', '');
