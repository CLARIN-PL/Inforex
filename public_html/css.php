<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$PATH_CONFIG = "../engine";
$PATH_CONFIG_LOCAL = "../config";

require_once("$PATH_CONFIG/config.php");
if ( file_exists("$PATH_CONFIG_LOCAL/config.local.php") ) {
    require_once("$PATH_CONFIG_LOCAL/config.local.php");
}
require_once("MDB2.php");
require_once($config->get_path_engine() . '/include.php');
$sql_log = false;
require_once('../engine/include/database/Database.php');

/********************************************************************8
 * Połączenie z bazą danych (stary sposób, tylko na potrzeby web)
 */

ob_start();
$options = array('debug' => 2, 'result_buffering' => false );
$mdb2 =& MDB2::singleton($config->dsn, $options);
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}

db_execute("SET CHARACTER SET 'utf8'");
db_execute("SET NAMES 'utf8'");
ob_clean();
/********************************************************************/

ob_start();
header("Content-type: text/css");

function getAnnotationStyles($annotationSetIds, $ignoreAnnotationSetName){
    if ( count($annotationSetIds) == 0 ){
        return "";
    }
    $ids = implode(",", $annotationSetIds);
    $sql = "SELECT group_id AS annotation_set_id, name, css FROM annotation_types WHERE group_id IN ($ids)";
    $annotation_types = db_fetch_rows($sql);

    $css = array();
    foreach($annotation_types as $annotation_type){
        if ($ignoreAnnotationSetName) {
            $css[] = ".annotations span.".$annotation_type['name']."{".$annotation_type['css']."}";
        } else {
            $css[] = "span.annotation_set_" . $annotation_type['annotation_set_id'] . "." . $annotation_type['name'] . "{" .
                $annotation_type['css'] . "}";
        }
    }

    return implode("\n", $css);
}

function getCorpusStyles($corpusIds){
    if ( count($corpusIds) == 0 ){
        return "";
    }
    $ids = implode(",", $corpusIds);
    $sql = "SELECT css FROM corpora WHERE id IN ($ids)";
    $corpora = db_fetch_rows($sql);
    $css = array();
    foreach ($corpora as $c){
        $css[] = $c['css'];
    }
    return implode("\n", $css);
}

function parseIds($idsString){
    $ids = explode(",", $idsString);
    return array_map("intval", $ids);
}

$css = array();
$css[] = getAnnotationStyles(
    parseIds($_GET['annotation_set_ids']), boolval($_GET['ignore_annotation_set_ids']));
$css[] = getCorpusStyles(parseIds($_GET['corpora_ids']));

echo implode("\n", $css);