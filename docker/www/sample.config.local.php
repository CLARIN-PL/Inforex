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
