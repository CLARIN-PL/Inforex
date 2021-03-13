<?php
Config::Config()->put_path_engine('/home/inforex/engine');
Config::Config()->put_path_www('/home/inforex/public_html');
Config::Config()->put_path_secured_data('/home/inforex/secured_data');
Config::Config()->put_dsn(array(
    'phptype'  => 'mysqli',
    'username' => 'inforex', 'port' => '3306', 'password' => 'password',
    'hostspec' => 'db',
    'database' => 'inforex',
));
