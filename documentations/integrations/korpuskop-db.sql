-- Table for Korpuskop run history inside Inforex
CREATE TABLE IF NOT EXISTS `korpuskop_runs` (
  `run_id` BIGINT NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT NULL,
  `corpus_id` INT NOT NULL,
  `user_id` INT NULL,
  `input_path` TEXT NOT NULL,
  `input_kind` VARCHAR(32) NOT NULL,
  `output_path` TEXT NOT NULL,
  `config_json_path` TEXT NULL,
  `progress_file` TEXT NULL,
  `status` VARCHAR(32) NOT NULL,
  `exit_code` INT NULL,
  `message` MEDIUMTEXT NULL,
  `file_size` BIGINT NULL,
  `created_at` DATETIME NOT NULL,
  `finished_at` DATETIME NULL,
  PRIMARY KEY (`run_id`),
  INDEX `korpuskop_runs_task_idx` (`task_id`),
  INDEX `korpuskop_runs_corpus_finished_idx` (`corpus_id`, `finished_at`, `run_id`),
  INDEX `korpuskop_runs_user_finished_idx` (`user_id`, `finished_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Safe upgrade for installations that already created an older version of the table.
SET @has_task_id := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'korpuskop_runs'
    AND COLUMN_NAME = 'task_id'
);
SET @sql := IF(
  @has_task_id = 0,
  'ALTER TABLE `korpuskop_runs` ADD COLUMN `task_id` BIGINT NULL AFTER `run_id`, ADD INDEX `korpuskop_runs_task_idx` (`task_id`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
