<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

$enginePath = realpath(__DIR__ . "/../engine/");
require_once($enginePath . "/settings.php");
require_once($enginePath . '/include.php');
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");

/********************************************************************8
 * Połączenie z bazą danych (nowy sposób)
 */
$db = new Database(Config::Config()->get_dsn());
$db->set_encoding('utf8');
/********************************************************************/

ob_start();
header("Content-type: text/css");

function getAnnotationStyles($annotationSetIds, $ignoreAnnotationSetName)
{
    global $db;
    if (count($annotationSetIds) == 0) {
        return "";
    }
    $ids = implode(",", $annotationSetIds);
    $sql = "SELECT group_id AS annotation_set_id, name, css FROM annotation_types WHERE group_id IN ($ids) AND css IS NOT NULL";
    $annotation_types = $db->fetch_rows($sql);

    $css = array();
    foreach ($annotation_types as $annotation_type) {
        if ($ignoreAnnotationSetName) {
            $css[] = ".annotations span." . $annotation_type['name'] . "{" . $annotation_type['css'] . "}";
        } else {
            $css[] = "span.annotation_set_" . $annotation_type['annotation_set_id'] . "." . $annotation_type['name'] . "{" .
                $annotation_type['css'] . "}";
        }
    }

    return implode("\n", $css);
}

function getCorpusStyles($corpusIds)
{
    global $db;
    if (count($corpusIds) == 0) {
        return "";
    }
    $ids = implode(",", $corpusIds);
    $sql = "SELECT css FROM corpora WHERE id IN ($ids) AND css IS NOT NULL";
    $corpora = $db->fetch_rows($sql);
    $css = array();
    foreach ($corpora as $c) {
        $css[] = $c['css'];
    }
    return implode("\n", $css);
}

function parseIds($idsString)
{
    $ids = explode(",", $idsString);
    return array_map("intval", $ids);
}

$css = array();
$css[] = getAnnotationStyles(
    parseIds($_GET['annotation_set_ids']), boolval($_GET['ignore_annotation_set_ids']));
$css[] = getCorpusStyles(parseIds($_GET['corpora_ids']));

echo implode("\n", $css);
