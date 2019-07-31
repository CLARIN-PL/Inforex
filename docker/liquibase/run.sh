#!/bin/bash

while [ "`nc -vz db 3306 2>&1 | grep open -o`" != "open" ]
do
    nc -vz db 3306 2>&1
    echo "waiting for db .."
    sleep 1
done

java -jar liquibase.jar --changeLogFile database/inforex-v1.0-changelog.sql update