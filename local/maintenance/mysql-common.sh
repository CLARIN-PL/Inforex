#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

INFOREX_DB_HOST="${INFOREX_DB_HOST:-127.0.0.1}"
INFOREX_DB_PORT="${INFOREX_DB_PORT:-3333}"
INFOREX_DB_NAME="${INFOREX_DB_NAME:-inforex}"
INFOREX_DB_USER="${INFOREX_DB_USER:-inforex}"
INFOREX_DB_PASSWORD="${INFOREX_DB_PASSWORD:-password}"

ensure_mysql_client() {
  if ! command -v mysql >/dev/null 2>&1; then
    cat >&2 <<'EOF'
[ERROR] MySQL client is not available in the current environment.

Run these maintenance wrappers:
  - from the host system, or
  - from a container/image that has the mysql client installed.

For Docker Compose environments, the recommended runtime is the host shell
using the database port exposed by Compose.
EOF
    exit 1
  fi
}

mysql_exec() {
  ensure_mysql_client
  mysql \
    -h"${INFOREX_DB_HOST}" \
    -P"${INFOREX_DB_PORT}" \
    -u"${INFOREX_DB_USER}" \
    -p"${INFOREX_DB_PASSWORD}" \
    "${INFOREX_DB_NAME}" \
    "$@"
}

mysql_run_file() {
  local sql_file="$1"
  mysql_exec < "${sql_file}"
}

resolve_sql_file() {
  local relative_path="$1"
  local candidate=""

  for candidate in \
    "${PROJECT_ROOT}/${relative_path}" \
    "${PWD}/${relative_path}" \
    "/workdir/${relative_path}" \
    "/home/inforex/${relative_path}"
  do
    if [ -f "${candidate}" ]; then
      printf '%s\n' "${candidate}"
      return 0
    fi
  done

  return 1
}
