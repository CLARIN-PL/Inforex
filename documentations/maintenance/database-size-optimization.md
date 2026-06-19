# Database size optimization

This document describes the recommended production cleanup procedure for the largest
database tables currently identified in Inforex:

* `activities`
* `tokens_backup`
* `tokens_tags_optimized`

The goal is to reclaim space safely and predictably on a production system.

## Summary of recommendations

1. Keep recent activity data online and prune old rows from `activities`.
2. Remove `tokens_backup` after making a one-time backup, because it is not referenced
   by the application code and has no foreign-key dependencies.
3. Run `OPTIMIZE TABLE activities` after pruning to reclaim disk space.
4. Review large composite indexes on `tokens_tags_optimized` and remove redundant ones in separate maintenance steps.

## Before you start

Perform the cleanup during a maintenance window.

Important notes:

* `OPTIMIZE TABLE activities` may take time and may lock the table.
* Make a backup before any destructive operation.
* Test the commands on a staging copy if possible.

## 1. Inspect current size

Run:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/01-analyze-large-tables.sql
```

Or use the host-side helper wrapper:

```bash
./local/maintenance/analyze-large-tables.sh
```

These wrapper scripts are intended for the host system, not for the `www` container.
If you want to execute the SQL from Docker, use the `db` container directly.

Docker Compose example:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/01-analyze-large-tables.sql
```

This script shows:

* the largest tables,
* the distribution of data in `activities`,
* the date range of activity data,
* the number of rows in `tokens_backup`.

## 2. Back up the affected data

Create a backup of `activities` and `tokens_backup` before cleanup:

```bash
mysqldump -uUSER -p DATABASE_NAME activities > activities-backup.sql
mysqldump -uUSER -p DATABASE_NAME tokens_backup > tokens_backup-backup.sql
```

If you use Docker Compose:

```bash
docker compose exec -T db mysqldump -uroot -ppassword inforex activities > activities-backup.sql
docker compose exec -T db mysqldump -uroot -ppassword inforex tokens_backup > tokens_backup-backup.sql
```

## 3. Prune old rows from `activities`

The prepared maintenance script keeps the last 365 days of activity data and deletes
older rows in batches of 50,000 rows:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/02-prune-activities.sql
```

Or use:

```bash
./local/maintenance/prune-activities.sh
```

If you want to execute the SQL from Docker instead of the host shell:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/02-prune-activities.sql
```

If you want a different retention period, edit:

```sql
SET @activities_cutoff := NOW() - INTERVAL 365 DAY;
```

For example:

* `180 DAY`
* `90 DAY`

If you want a different batch size, edit:

```sql
SET @activities_batch_size := 50000;
```

## 4. Remove `tokens_backup`

`tokens_backup` appears to be a legacy technical table:

* it is not referenced by application code,
* it has no foreign-key dependencies,
* it can be safely removed after backup.

Run:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/03-drop-tokens-backup.sql
```

Or use:

```bash
./local/maintenance/drop-tokens-backup.sh
```

If you want to execute the SQL from Docker instead of the host shell:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/03-drop-tokens-backup.sql
```

## 5. Verify the result

Run the analysis again:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/01-analyze-large-tables.sql
```

Or use:

```bash
./local/maintenance/analyze-large-tables.sh
```

Then verify:

* reduced size of `activities`,
* removal of `tokens_backup`,
* sufficient free disk space on the database host.

## Production notes

For MySQL 5.7:

* batch deletion is safer than deleting tens of millions of rows in one statement,
* `OPTIMIZE TABLE` is the step that usually returns space to the filesystem,
* plan enough time for large tables.

If you prefer a more conservative approach, replace direct cleanup with:

1. export old `activities` rows to an archive database or dump,
2. verify the archive,
3. prune only after successful verification.


## 6. Analyze `tokens_tags_optimized` indexes

The table `tokens_tags_optimized` is currently one of the largest structures in the database,
and most of its size is taken by indexes.

Run:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/04-analyze-tokens-tags-optimized-indexes.sql
```

Or use:

```bash
./local/maintenance/analyze-tokens-tags-optimized-indexes.sh
```

If you want to execute the SQL from Docker instead of the host shell:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/04-analyze-tokens-tags-optimized-indexes.sql
```

This script shows:

* current size of `tokens_tags_optimized`,
* all indexes on the table,
* row distribution by `stage` and `user_id`,
* representative `EXPLAIN` plans for corpus statistics and agreement/final token-tag queries.

## 7. Drop the first redundant composite index

The first recommended candidate is:

* `tokens_tags_optimized_disamb_pos_token_base_idx`

Run:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/05-drop-tto-disamb-pos-token-base-idx.sql
```

Or use:

```bash
./local/maintenance/drop-tto-disamb-pos-token-base-idx.sh
```

Docker Compose variant:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/05-drop-tto-disamb-pos-token-base-idx.sql
```

### Verification after step 7

Check these application areas before proceeding:

* corpus word-frequency/statistics views,
* report filters using token base form,
* token/tag browsing for report preview,
* any screen using morphological agreement summaries.

Then run the index analysis again:

```bash
./local/maintenance/analyze-tokens-tags-optimized-indexes.sh
```

or:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/04-analyze-tokens-tags-optimized-indexes.sql
```

## 8. Drop the second redundant composite index

If step 7 does not regress performance, the second candidate is:

* `tokens_tags_optimized_stage_user_disamb_pos_token_base_idx`

Run:

```bash
mysql -uUSER -p DATABASE_NAME < database/maintenance/06-drop-tto-stage-user-disamb-pos-token-base-idx.sql
```

Or use:

```bash
./local/maintenance/drop-tto-stage-user-disamb-pos-token-base-idx.sh
```

Docker Compose variant:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/06-drop-tto-stage-user-disamb-pos-token-base-idx.sql
```

### Verification after step 8

Repeat the same application checks as in step 7, with extra attention to:

* agreement-mode token tagging,
* final decision views,
* report-level token/tag loading.

## 9. Final verification

After all cleanup steps, run:

```bash
./local/maintenance/analyze-large-tables.sh
./local/maintenance/analyze-tokens-tags-optimized-indexes.sh
```

Or from Docker:

```bash
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/01-analyze-large-tables.sql
docker compose exec -T db mysql -uinforex -ppassword inforex < database/maintenance/04-analyze-tokens-tags-optimized-indexes.sql
```

Then verify:

* reduced total size of `tokens_tags_optimized`,
* unchanged behavior in corpus statistics and agreement/final tagging views,
* sufficient free disk space on the database host.
