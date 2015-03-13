<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
$engine = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
include($engine . DIRECTORY_SEPARATOR . "config.php");
include($engine . DIRECTORY_SEPARATOR . "config.local.php");
include($engine . DIRECTORY_SEPARATOR . "include.php");
include($engine . DIRECTORY_SEPARATOR . "cliopt.php");

mb_internal_encoding("UTF-8");

$opt = new Cliopt();
$opt->addExecute("php set-flags.php -c <CORPUS> -U user:pass@host:port/dbname -f Names=3,4 --flag-to-set \"Name Rel\" --init", "Inicjalizuje flagę Name Rel dla dokumentów oznaczonych jako gotowe i sprawdzone dla flagi Name:");
$opt->addParameter(new ClioptParameter("db-uri1", "U1", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-uri2", "U2", "URI", "connection URI: user:pass@host:ip/name"));
$config = null;

try {
	$opt->parseCli($argv);

	$config->dsn1 = parse_database_uri($opt->getRequired("db-uri1"));
	$config->dsn2 = parse_database_uri($opt->getRequired("db-uri2"));
	$config->dns1['phptype'] = 'mysql';
	$config->dns2['phptype'] = 'mysql';
	
	$config->sql = "SELECT r.id, a.from, a.to, a.type_id, a.text
 FROM `reports_annotations_optimized` a
 JOIN reports r ON (a.report_id = r.id) 
 JOIN reports_flags f ON (r.id = f.report_id AND f.corpora_flag_id = 78 AND f.flag_id = 2)
 WHERE r.corpora = 7 AND a.type_id IN (380,381,382)";
	
	main($config);	
	echo "done ■\n";		
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
	
/******************** main function       *********************************************/
function main ($config){
	$ans1 = array();
	$ans2 = array();

	$db1 = new Database($config->dsn1);
	$ans1 = $db1->fetch_rows($config->sql);

	$db2 = new Database($config->dsn2);
	$ans2 = $db2->fetch_rows($config->sql);
		
	echo sprintf("Liczba anotacji w DB1: %d\n", count($ans1));
	echo sprintf("Liczba anotacji w DB2: %d\n", count($ans2));

	$data = array();
	$data[] = compare("Granice i kategorie", $ans1, $ans2, "row_key_full");
	$data[] = compare("Granice", $ans1, $ans2, "row_key_no_type");
	
	$name_max = 0;
	foreach ($data as $d){
		$name_max = max($name_max, strlen($d['name']));
	}
	
	echo "\n\n";
	echo sprintf("%-{$name_max}s %4s %4s %4s\n\n", "Type", "A&B", "A", "B");
	foreach ($data as $d){
		echo sprintf("%-{$name_max}s %4d %4d %4d\n", $d['name'], $d['ab'], $d['a'], $d['b']);
	}
	
	//echo sprintf("A and B : %d\n", count($both));
	//echo sprintf("PCS     : %5.2f\n", pcs(count($both), count($only1), count($only2)));
} 


/******************** aux function        *********************************************/

function row_key_full($row){
	return implode(array_values($row), "_");
}

function row_key_no_type($row){
	$row['type_id'] = "";
	return implode(array_values($row), "_");
}


function save($only1, $only2, $both, $filename){
	$f = fopen($filename, "w");
	
}

function pcs($both, $only1, $only2){
	return $both*200.0/(2.0*$both+$only1+$only2);
}
	
function compare($name, $ans1, $ans2, $key_generator){
	foreach ($ans1 as $as){
		$key = $key_generator($as);
		if ( isset($ans1[$key]) ){
			echo "Warning: duplicated annotation in DB1 $key with $key_generator\n";
		}
		else{
			$ans1[$key] = $as;
		}
	}

	foreach ($ans2 as $as){
		$key = $key_generator($as);
		if ( isset($ans2[$key]) ){
			echo "Warning: duplicated annotation in DB2 $key with $key_generator\n";
		}
		else{
			$ans2[$key] = $as;
		}
	}
	$only1 = array_diff_key($ans1, $ans2);
	$only2 = array_diff_key($ans2, $ans1);
	$both = array_intersect_key($ans1, $ans2);
	
	return array("name"=>$name, "a"=>count($only1), "b"=>count($only2), "ab"=>count($both));	
}
	
?>
