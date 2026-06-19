# Database maintenance wrappers

These helper scripts are intended to be run from the host system, without entering
the Docker container shell.

They are **not** intended to run from the `www` container, because that container
does not need to provide the MySQL client.

By default they connect to the MySQL server exposed by Docker Compose:

* host: `127.0.0.1`
* port: `3333`
* database: `inforex`
* user: `inforex`
* password: `password`

You can override them with environment variables:

* `INFOREX_DB_HOST`
* `INFOREX_DB_PORT`
* `INFOREX_DB_NAME`
* `INFOREX_DB_USER`
* `INFOREX_DB_PASSWORD`

Examples:

```bash
./local/maintenance/analyze-large-tables.sh
./local/maintenance/prune-activities.sh
./local/maintenance/drop-tokens-backup.sh
./local/maintenance/analyze-tokens-tags-optimized-indexes.sh
./local/maintenance/drop-tto-disamb-pos-token-base-idx.sh
./local/maintenance/drop-tto-stage-user-disamb-pos-token-base-idx.sh
```

If you prefer to run the SQL from Docker instead of the host shell, use the `db`
container directly, for example:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/01-analyze-large-tables.sql
```

With custom connection settings:

```bash
INFOREX_DB_HOST=db.example.org \
INFOREX_DB_PORT=3306 \
INFOREX_DB_NAME=inforex \
INFOREX_DB_USER=admin \
INFOREX_DB_PASSWORD=secret \
./local/maintenance/analyze-large-tables.sh
```
