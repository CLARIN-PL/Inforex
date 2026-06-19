#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

source "${SCRIPT_DIR}/mysql-common.sh"

if sql_file="$(resolve_sql_file "database/maintenance/02-prune-activities.sql")"; then
  mysql_run_file "${sql_file}"
else
  mysql_exec <<'SQL'
SET @activities_cutoff := NOW() - INTERVAL 365 DAY;
SET @activities_batch_size := 50000;

DROP PROCEDURE IF EXISTS prune_activities_before;

DELIMITER $$

CREATE PROCEDURE prune_activities_before(IN cutoff_datetime DATETIME, IN batch_size INT)
BEGIN
    DECLARE deleted_rows INT DEFAULT 1;

    WHILE deleted_rows > 0 DO
        DELETE FROM activities
        WHERE datetime < cutoff_datetime
        LIMIT batch_size;

        SET deleted_rows = ROW_COUNT();
    END WHILE;
END$$

DELIMITER ;

CALL prune_activities_before(@activities_cutoff, @activities_batch_size);

DROP PROCEDURE IF EXISTS prune_activities_before;

OPTIMIZE TABLE activities;
SQL
fi
