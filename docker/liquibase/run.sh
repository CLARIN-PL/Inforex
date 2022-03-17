#!/bin/bash

# Wait or database server is up
# exit after one hour anyway
export TIMEOUT=3600
# DB server name and port
export HOST="db"
export PORT=3306
timeout $TIMEOUT bash -c 'until printf "" 2>>/dev/null >>/dev/tcp/$0/$1; do echo "waiting for db .."; sleep 1; done' $HOST $PORT

java -jar liquibase.jar --changeLogFile database/inforex-v1.0-changelog.sql update
