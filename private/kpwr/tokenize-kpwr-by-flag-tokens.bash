#!/bin/bash
# Tokenizuje dokumenty z korpusu KPWr oznaczone jako gotowe do tokenizacji lub do poprawy
php tokenize.php -U $1 -c 7 -F Tokens=5 -a wcrft
