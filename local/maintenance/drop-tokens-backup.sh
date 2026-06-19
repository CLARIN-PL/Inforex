#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

source "${SCRIPT_DIR}/mysql-common.sh"

if sql_file="$(resolve_sql_file "database/maintenance/03-drop-tokens-backup.sql")"; then
  mysql_run_file "${sql_file}"
else
  mysql_exec <<'SQL'
DROP TABLE IF EXISTS tokens_backup;
SQL
fi
