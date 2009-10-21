<?php
/*
 * Created on 2009-02-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
$conf_global_path = '/home/czuk/nlp/workspace/GPWKorpusWeb/engine';

define('GLOBAL_PATH_SQL_BACKUP', '/home/czuk/nlp/gpwc/sql');
define('GLOBAL_PATH_REPORTS_HTML', '/home/czuk/nlp/gpwc/html');
define('GLOBAL_PATH_REPORTS_HTML', '/home/czuk/nlp/gpwc/txt');

define('PATH_PUBLIC_HTML', "/home/czuk/nlp/workspace/GPWKorpusWeb/public_html");
define('PATH_ENGINE', "/home/czuk/nlp/workspace/GPWKorpusWeb/engine");

$dsn = array(
    'phptype'  => 'mysql',
    'username' => 'root',
    'password' => 'krasnal',
    'hostspec' => 'localhost',
    'database' => 'gpw',
);

define('IS_RELEASE', true);

?>
