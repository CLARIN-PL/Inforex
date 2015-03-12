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

header("Content-Type: text/html; charset=utf-8");

mb_internal_encoding("UTF-8");
echo "<pre>";

try {
	$config->annotation_type = $_GET['type'];
	
	$config->dsn1 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
	$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex2");
	$config->dns1['phptype'] = 'mysql';
	$config->dns2['phptype'] = 'mysql';
	
	$config->sql_reports = "SELECT r.id" .
			" FROM reports r" .
			" JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id)" .
			" JOIN reports_flags rf ON (rf.report_id=r.id)" .
			" JOIN corpora_flags cf ON (cf.corpora_flag_id=rf.corpora_flag_id)" .
			" WHERE cs.name='working_spatial' AND rf.flag_id IN (3,4) AND cf.short = 'Spatial 3'";
	
	$config->sql = "SELECT r.id, a.from, a.to, at.name AS type_id, a.text
 FROM `reports_annotations_optimized` a
 JOIN annotation_types at ON (a.type_id = at.annotation_type_id)
 JOIN reports r ON (a.report_id = r.id)
 JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id) 
 JOIN reports_flags f ON (r.id = f.report_id)
 JOIN corpora_flags cf ON (cf.corpora_flag_id=f.corpora_flag_id)
 WHERE r.corpora = 7 AND f.flag_id IN (3,4) AND cf.short = 'Spatial 3' AND cs.name='working_spatial' AND at.name != 'Motion_Indicator_3' AND at.name != 'Path'";
	
	main($config);	
} 
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}
	
/******************** main function       *********************************************/
function main ($config){
	$reports1 = array();
	$reports2 = array();

	echo "<pre>";
	$db1 = new Database($config->dsn1);
	foreach ($db1->fetch_rows($config->sql_reports) as $r){
		$reports1[] = $r['id'];
	}
	$ans1 = $db1->fetch_rows($config->sql);

	$db2 = new Database($config->dsn2);
	foreach ($db2->fetch_rows($config->sql_reports) as $r){
		$reports2[] = $r['id'];
	}
	$ans2 = $db2->fetch_rows($config->sql);

	$reports = array_intersect($reports1, $reports2);
	echo sprintf("Dokumenty oznakowane przez dwie osoby (%d): %s\n\n", count($reports), implode(", ", $reports));
	
	$reports_set = array();
	foreach ($reports as $r){
		$reports_set[$r] = 1;
	}		
	
	$ans1 = filter_by_report_id($ans1, $reports_set);
	$ans2 = filter_by_report_id($ans2, $reports_set);
	
	$type_ids = array();
	foreach (array_merge($ans1, $ans2) as $a){
		$type_ids[$a['type_id']] = 1;
	}
	$type_ids = array_keys($type_ids);
	
	$data = array();
	$data[] = compare("Granice i kategorie", $ans1, $ans2, "row_key_full");
	$data[] = compare("Granice", $ans1, $ans2, "row_key_no_type", "notype");
	foreach ($type_ids as $type_id){
		$data[] = compare("_Typ=$type_id", 
					filter_by_type($ans1, $type_id), 
					filter_by_type($ans2, $type_id),
					"row_key_no_type",
					$type_id);		
	}
	
	$name_max = 0;
	foreach ($data as $d){
		$name_max = max($name_max, strlen($d['name']));
	}
	
	echo "\n\n";
	echo sprintf("%-{$name_max}s %4s %4s %4s %6s\n\n", "Type", "A&B", "A", "B", "pcs");
	foreach ($data as $d){
		$pcs = pcs($d['ab'], $d['a'], $d['b']);
		echo sprintf("<a href='?type=%s'>%-{$name_max}s</a> %4d %4d %4d %6.2f%%\n", $d['type'], $d['name'], $d['ab'], $d['a'], $d['b'], $pcs);
	}
	echo "</pre>";
	
	print_in_table($ans1, $ans2, $config->annotation_type);
	
} 


/******************** aux function        *********************************************/

function filter_by_type($ans, $type_id){
	$ans_new = array();
	foreach ($ans as $v){
		if ( $v['type_id'] == $type_id){
			$ans_new[] = $v;
		}		
	}
	return $ans_new;	
}

function filter_by_report_id($ans, $reports){
	$ans_new = array();
	foreach ($ans as $v){
		if (isset($reports[$v['id']])){
			$ans_new[] = $v;
		}		
	}
	return $ans_new;
}

function row_key_full($row){
	return implode(array_values($row), "_");
}

function row_key_full_sort($row){
	return sprintf("%06d_%04d_%04d_%s", $row['id'], $row['from'], $row['to'], $row['type_id']);
}

function row_key_no_type($row){
	$row['type_id'] = "";
	return implode(array_values($row), "_");
}

function pcs($both, $only1, $only2){
	return $both*200.0/(2.0*$both+$only1+$only2);
}
	
function compare($name, $ans1, $ans2, $key_generator, $type=""){
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
	
	return array("name"=>$name, "a"=>count($only1), "b"=>count($only2), "ab"=>count($both), "type"=>$type);	
}

/**
 * Drukuje porównanie anotacji w postaci tabeli
 */
function print_in_table($ans1, $ans2, $annotation_type){
	$both = array();
	$in1 = array();
	$in2 = array();
	
	$notype = $annotation_type == "notype";
	if ( $notype ){
		$annotation_type = null;
	}
	
	foreach ( $ans1 as $an){
		if ($annotation_type == null || $annotation_type == $an['type_id']){
			if ( $notype ){
				$an['type_id'] = "";
			}			
			$key = row_key_full_sort($an);
			if (!isset($both[$key])){
				$both[$key] = $an;
			}
			$in1[$key] = 1;
		}
	}
	
	foreach ( $ans2 as $an){
		if ($annotation_type == null || $annotation_type == $an['type_id']){
			if ( $notype ){
				$an['type_id'] = "";
			}			
			$key = row_key_full_sort($an);
			if (!isset($both[$key])){
				$both[$key] = $an;
			}
			$in2[$key] = 1;
		}
	}
		
	echo "<table>";
	echo "<thead><tr><th>Only A</th><th>Both A and B</th><th>Only B</th></tr></thead>";
	echo "<tbody>";
	
	$keys = array_keys($both);
	sort($keys);
	$lastid = null;
	
	foreach ($keys as $key){
		$an = $both[$key];
		if ( $lastid!= null && $lastid != $an['id'] ){
			echo "<tr><td colspan='3'><hr/></td></tr>";
		}
		$lastid = $an['id'];
		$a = "";
		$b = "";
		$c = "";
		$val = sprintf("%s (%s)", $key, $an['text']); 
		if ( isset($in1[$key]) && isset($in2[$key]) ){
			$b = $val;
		}
		else if ( isset($in1[$key]) ){
			$a = $val;
		}
		else{
			$c = $val;
		}
		echo sprintf("<tr><td style='color: red'>%s</td><td style='color: blue'>%s</td><td style='color: orange'>%s</td></tr>\n", $a, $b, $c);		
	}

	echo "</tbody>";
	echo "</table>";
}
	
?>
