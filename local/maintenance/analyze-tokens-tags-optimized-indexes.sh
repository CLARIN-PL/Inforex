#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=/dev/null
source "${SCRIPT_DIR}/mysql-common.sh"

if sql_file="$(resolve_sql_file 'database/maintenance/04-analyze-tokens-tags-optimized-indexes.sql')"; then
  mysql_run_file "${sql_file}"
  exit 0
fi

cat >&2 <<'EOF'
[ERROR] Could not locate database/maintenance/04-analyze-tokens-tags-optimized-indexes.sql
EOF
exit 1
