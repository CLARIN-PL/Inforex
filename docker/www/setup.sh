if [ ! -d $inforex_location/engine/templates_c  ]; then
    mkdir $inforex_location/engine/templates_c
    chmod g+rwx $inforex_location/engine/templates_c
    chown :www-data $inforex_location/engine/templates_c
fi

if [ ! -d /opt/korpuskop/var/output ]; then
    mkdir -p /opt/korpuskop/var/output
fi
if [ ! -d /opt/korpuskop/var/progress ]; then
    mkdir -p /opt/korpuskop/var/progress
fi
chown -R www-data:www-data /opt/korpuskop/var || true
chmod -R g+rwx /opt/korpuskop/var || true

# only execute if inital config
if [ ! -f $inforex_location/config/config.local.php  ]; then
  echo "[OK] create a dafault config"
  cp /bin/sample.config.local.php $inforex_location/config/config.local.php
else
  echo "[SKIPPING] configuration file already exists"
fi
