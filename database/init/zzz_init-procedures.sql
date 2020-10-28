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
    INSERT INTO flag_status_history (date, report_id, flag_id, user_id, new_status, old_status)
    VALUES (CURRENT_TIMESTAMP, report_id, flag_id, user_id, flag_status, IFNULL(old_status,-1));
  END$$

DELIMITER ;
