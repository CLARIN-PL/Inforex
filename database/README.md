### Database Initialization
Before you start first time database docker move inforex-v1.0.sql to init/ folder

File removed form init/ folder to prevent accidental initialization.

Current Docker bootstrap uses `database/init/001-bootstrap-inforex.sh`.

- Fresh database: the script imports `database/inforex-v1.0.sql`.
- Existing database: the script skips import when the `users` table already exists.
- Schema changes for already created databases must be added to `database/inforex-v1.0-changelog.sql`.
