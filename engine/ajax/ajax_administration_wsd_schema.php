<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_administration_wsd_schema extends CPageAdministration {

    function execute(){
        global $db;
/*
    [draw] => 1
    [columns] => Array
        (
            [0] => Array
                (
                    [data] => 0
                    [name] =>
                    [searchable] => true
                    [orderable] => true
                    [search] => Array
                        (
                            [value] =>
                            [regex] => false
                        )

                )

            [1] => Array
                (
                    [data] => 1
                    [name] =>
                    [searchable] => true
                    [orderable] => true
                    [search] => Array
                        (
                            [value] =>
                            [regex] => false
                        )

                )

        )
    [order] => Array
        (
            [0] => Array
                (
                    [column] => 1
                    [dir] => asc
                )

        )

    [start] => 0
    [length] => 10
    [search] => Array
        (
            [value] =>
            [regex] => false
        )
)
*/


	// parameters from ajax request are send by POST
	$draw = isset($_POST['draw']) ? $_POST['draw'] : 0;
	$start = isset($_POST['start']) ? $_POST['start'] : 0;
	$length = isset($_POST['length']) ? $_POST['length'] : 1;
	$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : "";

	$logFilename = __DIR__."/../templates_c/ajax.wsd.log";
	$logMessage = "Ajax_administration_wsd_schema->execute() works...\n".
		      " draw = ".$draw."\n".
		      " start = ".$start."\n".
		      " length = ".$length."\n".
		      " searchValue = ".$searchValue."\n".
		      "\n";
	file_put_contents($logFilename,$logMessage);



	$total = 1;
	$totalFiltered = $total;

	$rowArray = array( 
		'"0":"'."100".'"',
		'"1":"'."druga kolumna".'"'
		);
	$data = array(
		"{".implode(",",$rowArray)."}" 
		);
	$data = '"data":['.implode(",",$data)."]";

	$result = "{";
	$result .= '"draw":'.strval($draw).',';
	$result .= '"recordsTotal":'.strval($total).',';
	$result .= '"recordsFiltered":'.strval($totalFiltered).',';
	$result .= $data;
	$result .= "}";

	$result = array(
		'draw' => '1',
		'recordsTotal' => 1,
		'recordsFiltered' => '1',
		'data' => array( array( '"0"'=>1, '"1"'=>'a' ) )
	);

        $result = array(
                "draw" => 1,
                "recordsTotal"=>100,
                "recordsFiltered"=>1,
                "data"=>array (
                        [ "1","cośtam2" ]
                )
        );
 
	return $result;

    } // execute()

} // Ajax_administration_wsd_schema class

?>
