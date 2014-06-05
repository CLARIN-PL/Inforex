#!/bin/bash

for dir in $1/*/
do
    dir=${dir%*/}
    cd ${dir} 
    gunzip *.gz
    cd ~/workspace/inforex/local
    php import-tei.php -U root:root@localhost:3306/inforex_ruler -i ${dir}
done
