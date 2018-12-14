<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$PATH_CONFIG = "../config";
chdir($PATH_CONFIG);

require_once("config.php");
require_once("MDB2.php");

if ( file_exists("config.local.php") ) {
    require_once("config.local.php");
}
require_once($config->get_path_engine() . '/include.php');

$sql_log = false;

require_once('../engine/include/database/Database.php');

/********************************************************************8
 * Połączenie z bazą danych (stary sposób, tylko na potrzeby web)
 */

ob_start();
$options = array(
    'debug' => 2,
    'result_buffering' => false,
);

$mdb2 =& MDB2::singleton($config->dsn, $options);

if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->loadModule('Extended');
$mdb2->loadModule('TableBrowser');


db_execute("SET CHARACTER SET 'utf8'");
db_execute("SET NAMES 'utf8'");
ob_clean();
/********************************************************************/

ob_start();

header("Content-type: text/css");
$annotation_css = '';

$annotation_set_ids = $_GET['annotation_set_ids'];
$sql = "SELECT group_id AS annotation_set_id, name, css FROM annotation_types WHERE group_id IN (".$annotation_set_ids.")";
$annotation_types = db_fetch_rows($sql);

if(isset($_GET['ignore_annotation_set_ids'])){
    foreach($annotation_types as $annotation_type){
        $annotation_css .= ".annotations span.".$annotation_type['name']."{".$annotation_type['css']."}\n";
    }
} else{
    foreach($annotation_types as $annotation_type){
        $annotation_css .= "span.annotation_set_".$annotation_type['annotation_set_id'].".".$annotation_type['name']."{".
            $annotation_type['css']."}\n";
    }
}

echo $annotation_css;
