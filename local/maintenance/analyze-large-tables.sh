#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

source "${SCRIPT_DIR}/mysql-common.sh"

if sql_file="$(resolve_sql_file "database/maintenance/01-analyze-large-tables.sql")"; then
  mysql_run_file "${sql_file}"
else
  mysql_exec <<'SQL'
SELECT
    table_name,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS total_mb,
    ROUND(data_length / 1024 / 1024, 2) AS data_mb,
    ROUND(index_length / 1024 / 1024, 2) AS index_mb,
    table_rows
FROM information_schema.tables
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC
LIMIT 25;

SELECT 'activities_total' AS metric, COUNT(*) AS value FROM activities
UNION ALL
SELECT 'activities_30d', COUNT(*) FROM activities WHERE datetime >= NOW() - INTERVAL 30 DAY
UNION ALL
SELECT 'activities_90d', COUNT(*) FROM activities WHERE datetime >= NOW() - INTERVAL 90 DAY
UNION ALL
SELECT 'activities_180d', COUNT(*) FROM activities WHERE datetime >= NOW() - INTERVAL 180 DAY
UNION ALL
SELECT 'activities_365d', COUNT(*) FROM activities WHERE datetime >= NOW() - INTERVAL 365 DAY
UNION ALL
SELECT 'activities_older_365d', COUNT(*) FROM activities WHERE datetime < NOW() - INTERVAL 365 DAY;

SELECT
    MIN(datetime) AS min_activity_datetime,
    MAX(datetime) AS max_activity_datetime
FROM activities;

SELECT COUNT(*) AS tokens_backup_rows FROM tokens_backup;
SQL
fi
