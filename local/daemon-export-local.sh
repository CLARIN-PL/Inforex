#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

while true; do
  php daemon-export.php -v -U root:krasnal@localhost:3306/inforex
  sleep 2
done
