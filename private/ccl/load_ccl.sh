#!/bin/bash
php upload_ccl.php -U root:alamakota@localhost:3306/inforex_a -c 22 -d
#php upload_ccl.php -U root:alamakota@localhost:3306/inforex_a -c 22 -s 84 -f ccl/15582480.txt.ccl
find ccl -type f -name "*.ccl" -print0 | xargs -0 -I{} php upload_ccl.php -U root:alamakota@localhost:3306/inforex_a -c 22 -s 84 -f {}
find ccl -type f -name "*.ccl" -print0 | xargs -0 -I{} php upload_ccl.php -U root:alamakota@localhost:3306/inforex_a -c 22 -s 85 -f {}