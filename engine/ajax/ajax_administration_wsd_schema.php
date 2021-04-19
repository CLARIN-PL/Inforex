<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_administration_wsd_schema extends CPageAdministration {

    function execute(){

	// parameters from ajax request are send by POST
	$draw = isset($_POST['draw']) ? $_POST['draw'] : 0;
	$start = isset($_POST['start']) ? $_POST['start'] : 0;
	$length = isset($_POST['length']) ? $_POST['length'] : 1;
	$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : null;

	$total = DbSens::getSensListCount($searchValue);
        $logFilename = __DIR__."/../templates_c/ajax.wsd.log";
        $logMessage = "Ajax_administration_wsd_schema->execute() works...\n".
                      " draw = ".$draw."\n".
                      " start = ".$start."\n".
                      " length = ".$length."\n".
                      " searchValue = ".$searchValue."\n".
                      " total = ".$total."\n".
                      "\n";
        file_put_contents($logFilename,$logMessage);

	$dataChunk = DbSens::getSensList("id, at.name AS 'annotation_name'",$length,$start,$searchValue);

	$indexKey = $start;
	$numeratedList = array();
	foreach($dataChunk as $dataRow) {
		$indexKey++;
		$numeratedList[] = array( 
			"DT_RowId" => $dataRow['id'],
			"DT_RowClass" => "sensName",
			"index"=>$indexKey, 
			"name"=>substr($dataRow['annotation_name'],4) // obcinanie "wsd_" 
		);
	}

	$totalFiltered = $total;

	$result = array(
		'draw' => $draw,
		'recordsTotal' => $total,
		'recordsFiltered' => $totalFiltered,
		'data' => $numeratedList
	);

	return $result;

    } // execute()

} // Ajax_administration_wsd_schema class

?>
