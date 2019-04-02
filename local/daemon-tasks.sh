#!/bin/bash

echo "Running daemon-tasks.php ..."

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd $DIR

while true; do
  php daemon-tasks.php -v
done
