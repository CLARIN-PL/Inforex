#!/bin/sh
set -eu

DB_NAME="${MYSQL_DATABASE:-inforex}"

if mysql --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" "${DB_NAME}" -e "SHOW TABLES LIKE 'users';" 2>/dev/null | grep -q '^users$'; then
    echo "Inforex schema already present in '${DB_NAME}', skipping bootstrap import."
    exit 0
fi

echo "Bootstrapping Inforex schema into '${DB_NAME}' from database/inforex-v1.0.sql"
mysql --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" "${DB_NAME}" < /home/inforex-bootstrap/inforex-v1.0.sql
