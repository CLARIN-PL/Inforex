# Korpuskop Runtime For Inforex Docker WWW

This directory is copied directly into the Inforex `www` image as `/opt/korpuskop`.

Contents:
- `bin/korpuskop` - release binary
- `config/document.report.json` - default report profile for document corpora
- `config/dialog.report.json` - report profile for dialog corpora
- `dics/` - dictionaries required by the default config
- `var/output/` - generated reports
- `var/progress/` - progress snapshots

Refresh this bundle after changing Korpuskop binary or dictionaries.
