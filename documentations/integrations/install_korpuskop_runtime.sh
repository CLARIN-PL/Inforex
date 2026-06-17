#!/usr/bin/env bash
set -euo pipefail

KOP_ROOT="/opt/korpuskop"
WWW_USER="www-data"
WWW_GROUP="www-data"

install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT"
install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT/bin"
install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT/config"
install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT/dics"
install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT/var"
install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT/var/progress"
install -d -m 2775 -o "$WWW_USER" -g "$WWW_GROUP" "$KOP_ROOT/var/output"

echo "Installed runtime directories under $KOP_ROOT"
echo "Copy the following into place:"
echo "  - target/release/korpuskop -> $KOP_ROOT/bin/korpuskop"
echo "  - dics/* -> $KOP_ROOT/dics/"
echo "  - config/*.json -> $KOP_ROOT/config/"
echo
echo "Required runtime packages on the web server:"
echo "  - zstd"
echo "  - python3"
echo "  - python3-pyarrow (or pyarrow in the active Python environment)"
echo
echo "Recommended ownership: $WWW_USER:$WWW_GROUP"
