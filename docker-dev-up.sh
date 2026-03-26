#!/usr/bin/env bash

composer update

AUTOLOAD=vendor/autoload.php

if [ -f $AUTOLOAD ]; then
    docker-compose build
    docker-compose up -d
else
    echo -e "[\e[31mERROR\e[0m] $AUTOLOAD not found"
    echo -e "Make sure that '\e[32mcomposer\e[0m' is installed in order to run '\e[32mcomposer update\e[0m' and generate $AUTOLOAD"
fi
