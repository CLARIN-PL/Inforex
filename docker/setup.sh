if [ ! -d $inforex_location/engine/templates_c  ]; then
    mkdir $inforex_location/engine/templates_c
    chmod g+rwx $inforex_location/engine/templates_c
    chown :www-data $inforex_location/engine/templates_c
fi

# only execute if inital config
if [ ! -f $inforex_location/engine/config.local.php  ]; then
  echo "[OK] loading dafault config"
  echo "<?php \n \$config->dsn = array(\n    'phptype'  => 'mysql',\n    'username' => 'inforex', 'port' => '3306', 'password' => 'password',\n    'hostspec' => db,\n    'database' => 'inforex',\n);" | tee $inforex_location/engine/config.local.php
else
  echo "[SKIPPING] configuration file alread exists"
fi