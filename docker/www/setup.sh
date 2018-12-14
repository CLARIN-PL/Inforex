if [ ! -d $inforex_location/engine/templates_c  ]; then
    mkdir $inforex_location/engine/templates_c
    chmod g+rwx $inforex_location/engine/templates_c
    chown :www-data $inforex_location/engine/templates_c
fi

# only execute if inital config
if [ ! -f $inforex_location/config/config.local.php  ]; then
  echo "[OK] create a dafault config"
  cp /bin/sample.config.local.php $inforex_location/config/config.local.php
else
  echo "[SKIPPING] configuration file already exists"
fi
