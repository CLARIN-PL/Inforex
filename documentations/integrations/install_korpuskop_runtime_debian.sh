#!/usr/bin/env bash
set -euo pipefail

export DEBIAN_FRONTEND=noninteractive

apt-get update
apt-get install -y \
  zstd \
  python3 \
  python3-pip \
  php-cli

python3 -m pip install --break-system-packages pyarrow || python3 -m pip install pyarrow

echo "Installed runtime packages for Inforex + Korpuskop: zstd, python3, php-cli, pyarrow"
