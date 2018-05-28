#!/bin/bash

while getopts ":U:A:i:" opt; do
  case $opt in
    U) db_auth="$OPTARG"
    ;;
    A) ann_subset="$OPTARG"
    ;;
    i) path="$OPTARG"
    ;;
    \?) echo "Invalid option -$OPTARG" >&2
    ;;
  esac
done

php import-sherlock.php $db_auth $ann_subset $path


