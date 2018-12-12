<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Get array value with isset check. If value is not set 
 * then return the $default value. In other case return the value as string.
 * @param $array ­— an array
 * @param $index — element key in the array
 * @param $default — default value if the not found
 * @return value of the key of default value
 */
function array_get_str(&$array, $key, $default){
	return isset($array[$key]) ? strval($array[$key]) : $default;
}

function array_get_int(&$array, $key, $default){
	return isset($array[$key]) ? intval($array[$key]) : $default;
}

function array_map_replace_spaces(&$value){
    $value['name'] = str_replace(" ", "&nbsp;", $value['name']);
}

function array_map_replace_null_id(&$value){
    $value['id'] = ($value['id'] ? $value['id'] : "no_annotation" );
}

function array_walk_highlight(&$value, $key, $phrase){
    $value['title'] = str_replace($phrase, "<em>$phrase</em>", $value['title']);
}

function where_or($column, $values){
    $ors = array();
    foreach ($values as $value)
        $ors[] = "$column = '$value'";
    if (count($ors)>0)
        return "(" . implode(" OR ", $ors) . ")";
    else
        return "";
}

function getBaseAnchor($found_count, $report_id, $base){
    $anchor = '<a href="#" class="ajax_link_get_sentences"  data-report_id="'.$report_id.'" data-search_base="'.$base.'">Get sentences (found '.$found_count.' occurrences).</a>';
    return $anchor;
}

function getDocumentAnchor($corpus_id, $document_id, $title = "<i>no title</i>"){
    $title = trim($title)==="" ? "<i>no title</i>" : trim($title);
    $anchor = '<a href="index.php?page=report&amp;corpus='.$corpus_id.'&amp;id='.$document_id.'" class="tip" title="'.$title.'">'.$title.'</a>';
    return $anchor;
}

function getFlagMarkup($flag_id, $flag_name){
    $markup = '<img src="gfx/flag_'.$flag_id.'.png" title="'.$flag_name.'" style="vertical-align: baseline"/>';
    return $markup;
}

/**
 * Return $val if it is not empty or null in other case.
 * @param $val
 * @return null|string
 */
function strvalOrNull($val){
    $v = strval($val);
    return $v ? $v : null;
}

/**
 * Return $val if it is not empty or null in other case.
 * @param $val
 * @return null|string
 */
function intvalOrNull($val){
    $v = intval($val);
    return $v ? $v : null;
}

/**
 * Creates an associate array of given array of values.
 * @param $arr ['x','y','z']
 * @return {'x':1, 'y':1, 'z':1}
 */
function arrayToAssoc($arr){
    $assoc = array();
    foreach ($arr as $val){
        $assoc[$val] = 1;
    }
    return $assoc;
}

function arrayToMap($arr, $key, $val){
    $map = array();
    foreach ($arr as $item){
        $map[$item[$key]] = $item[$val];
    }
    return $map;
}


/**
 * @param $arr
 * @param $field
 * @return array
 */
function assocToArray($arr, $field){
    $vals = array();
    foreach ($arr as $a){
        $vals[] = $a[$field];
    }
    return $vals;
}