<?php
Config::Cfg()->put_path_engine('/home/inforex/engine');
Config::Cfg()->put_path_www('/home/inforex/public_html');
Config::Cfg()->put_path_secured_data('/home/inforex/secured_data');
Config::Cfg()->put_dsn(array(
    'phptype'  => 'mysqli',
    'username' => 'inforex', 
    'port' => '3306', 
    'password' => 'password',
    'hostspec' => 'db',
    'database' => 'inforex',
));
Config::Cfg()->put_url('http://localhost:9080/inforex');
Config::Cfg()->put_oidcEnabled(true);
Config::Cfg()->put_oidcIssuerUrl('http://localhost:9081/realms/inforex');
Config::Cfg()->put_oidcInternalBaseUrl('http://keycloak:8080');
Config::Cfg()->put_oidcClientId('inforex-local');
Config::Cfg()->put_oidcClientSecret('inforex-secret');
Config::Cfg()->put_oidcRedirectUri('http://localhost:9080/inforex/index.php?page=oidc_callback');
Config::Cfg()->put_oidcPostLogoutRedirectUri('http://localhost:9080/inforex/index.php');
Config::Cfg()->put_korpuskopBinary('/opt/korpuskop/bin/korpuskop');
Config::Cfg()->put_korpuskopDefaultConfig('/opt/korpuskop/config/document.report.json');
Config::Cfg()->put_korpuskopDocumentConfig('/opt/korpuskop/config/document.report.json');
Config::Cfg()->put_korpuskopDialogConfig('/opt/korpuskop/config/dialog.report.json');
Config::Cfg()->put_korpuskopProgressDir('/opt/korpuskop/var/progress');
Config::Cfg()->put_korpuskopOutputDir('/opt/korpuskop/var/output');
Config::Cfg()->put_korpuskopWorkerUrl('http://korpuskop-worker:8090');
