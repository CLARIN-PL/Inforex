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

try {
	$config->test = $_GET['test'];
	$config->subcorpus_id = array();

	$config->dns1['phptype'] = 'mysql';
	$config->dns2['phptype'] = 'mysql';
	
	if ( $config->test == "spatial" ){
		$config->description = "Zgodność wyrażeń przestrzennych między nlp.pwr.wroc.pl/inforex i kotu88.ddn.net/inforex1 dla flagi Sp_4/Sp_2";
		$config->url1 = "http://nlp.pwr.wroc.pl/inforex";
		$config->url2 = "http://kotu88.ddns.net/inforex1";
		$config->dsn1 = parse_database_uri("gpw:gpw@nlp.pwr.wroc.pl:3306/gpw");
		$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
		$config->types = array('Spatial_Object', 'Spatial_Indicator_3', 'Region', 'Distance', 'Direction');	
		$config->inforex1_flag = "Sp_2";
		$config->inforex2_flag = "Sp_4";
		$config->flag = "3,4";
	}
	elseif ( $config->test == "spatial4" ){
		$config->description = "Zgodność wyrażeń przestrzennych między nlp.pwr.wroc.pl/inforex i kotu88.ddn.net/inforex1 dla flagi Sp_4/Sp_2";
		$config->url1 = "http://nlp.pwr.wroc.pl/inforex";
		$config->url2 = "http://kotu88.ddns.net/inforex1";
		$config->dsn1 = parse_database_uri("gpw:gpw@nlp.pwr.wroc.pl:3306/gpw");
		$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
		$config->types = array('Spatial_Object', 'Spatial_Indicator_3', 'Region', 'Distance', 'Direction');	
		$config->inforex1_flag = "Sp_2";
		$config->inforex2_flag = "Sp_4";
		$config->flag = "5";
	}
	elseif ( $config->test == "spatial5" ){
		$config->description = "Zgodność wyrażeń przestrzennych między nlp.pwr.wroc.pl/inforex i kotu88.ddn.net/inforex1 dla flagi Sp_4/Sp_2";
		$config->url1 = "http://nlp.pwr.wroc.pl/inforex";
		$config->url2 = "http://kotu88.ddns.net/inforex1";
		$config->dsn1 = parse_database_uri("gpw:gpw@nlp.pwr.wroc.pl:3306/gpw");
		$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
		$config->types = array('Spatial_Object', 'Spatial_Indicator_3', 'Region', 'Distance', 'Direction');	
		$config->inforex1_flag = "Sp_2";
		$config->inforex2_flag = "Sp_4";
		$config->subcorpus_ids = array(77);
		$config->flag = "3";
	}
	elseif ( $config->test == "spatial6" ){
		$config->description = "Zgodność wyrażeń przestrzennych między nlp.pwr.wroc.pl/inforex i kotu88.ddn.net/inforex1 dla flagi Sp_4/Sp_2";
		$config->url1 = "http://nlp.pwr.wroc.pl/inforex";
		$config->url2 = "http://kotu88.ddns.net/inforex1";
		$config->dsn1 = parse_database_uri("gpw:gpw@nlp.pwr.wroc.pl:3306/gpw");
		$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
		$config->types = array('Spatial_Object', 'Spatial_Indicator_3', 'Region', 'Distance', 'Direction');	
		$config->inforex1_flag = "Sp_2";
		$config->inforex2_flag = "Sp_4";
		$config->subcorpus_ids = array(1,2,4,11,13,19,26,29,30,31,50,51,63,66);
		$config->flag = "3";
	}
	else if ( $config->test == "event" || $config->test == "event2" ){
		$config->description = "Zgodność wyznaczników sytuacji między nlp.pwr.wroc.pl/inforex i kotu88.ddn.net/inforex1 dla flagi Events";
		$config->url1 = "http://nlp.pwr.wroc.pl/inforex";
		$config->url2 = "http://kotu88.ddns.net/inforex1";
		$config->dsn1 = parse_database_uri("gpw:gpw@nlp.pwr.wroc.pl:3306/gpw");
		$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
		$config->types = array('action', 'state', 'perception', 'reporting', 'aspectual', 'i_action', 'i_state', 'light_predicate');	
		$config->inforex1_flag = "Events";
		$config->inforex2_flag = "Events";
		$config->flag = "4";
		if ( $config->test == "event2" ){
			$config->flag = "3";
		}
	}

	if ( isset($_GET['type']) && $_GET['type'] != "" && $_GET['type'] != "notype" ){
		$config->types = array($_GET['type']);
	}
	
	$config->sql_reports = "SELECT r.id" .
			" FROM reports r" .
			" JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id)" .
			" JOIN reports_flags rf ON (rf.report_id=r.id)" .
			" JOIN corpora_flags cf ON (cf.corpora_flag_id=rf.corpora_flag_id)" .
			" WHERE rf.flag_id IN ({$config->flag}) AND cf.short = ?";
	if ( is_array($config->subcorpus_ids) && count(is_array($config->subcorpus_ids)) ){
		$config->sql_reports .= " AND r.subcorpus_id IN (" . implode(", ", $config->subcorpus_ids) . ")";
	}
	
	$types = array();
	foreach ( $config->types as $t){
		$types[] = "'$t'";
	}
	
	$config->sql = "SELECT r.id, a.from, a.to, at.name AS type_id, a.text
 FROM `reports_annotations_optimized` a
 JOIN annotation_types at ON (a.type_id = at.annotation_type_id)
 JOIN annotation_sets ag ON (at.group_id = ag.annotation_set_id)
 JOIN reports r ON (a.report_id = r.id)
 JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id) 
 JOIN reports_flags f ON (r.id = f.report_id)
 JOIN corpora_flags cf ON (cf.corpora_flag_id=f.corpora_flag_id)
 WHERE r.corpora = 7 AND f.flag_id IN ({$config->flag}) AND cf.short = ? AND at.name IN (".implode(", ", $types).")";

	if ( is_array($config->subcorpus_ids) && count(is_array($config->subcorpus_ids)) ){
		$config->sql .= " AND r.subcorpus_id IN (" . implode(", ", $config->subcorpus_ids) . ")";
	}
	
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

	$db1 = new Database($config->dsn1);
	foreach ($db1->fetch_rows($config->sql_reports, array($config->inforex1_flag)) as $r){
		$reports1[] = $r['id'];
	}
	$ans1 = $db1->fetch_rows($config->sql,  array($config->inforex1_flag));

	$db2 = new Database($config->dsn2);
	foreach ($db2->fetch_rows($config->sql_reports, array($config->inforex2_flag)) as $r){
		$reports2[] = $r['id'];
	}
	$ans2 = $db2->fetch_rows($config->sql,  array($config->inforex2_flag));

	$reports = array_intersect($reports1, $reports2);
	echo "<h1>{$config->description}</h1>";
	echo sprintf("Dokumenty oznakowane przez dwie osoby (%d): %s\n\n", count($reports), implode(", ", $reports));
	echo "<pre>";
	
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
		echo sprintf("<a href='?test={$config->test}&amp;type=%s'>%-{$name_max}s</a> %4d %4d %4d %6.2f%%\n", $d['type'], $d['name'], $d['ab'], $d['a'], $d['b'], $pcs);
	}
	echo "</pre>";
	
	print_in_table($config, $ans1, $ans2, $config->annotation_type);
	
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
	$copy_ans1 = array();
	$copy_ans2 = array();
	foreach ($ans1 as $as){
		$key = $key_generator($as);
		if ( isset($ans1[$key]) ){
			echo "Warning: duplicated annotation in DB1 $key with $key_generator\n";
		}
		else{
			$copy_ans1[$key] = $as;
		}
	}

	foreach ($ans2 as $as){
		$key = $key_generator($as);
		if ( isset($ans2[$key]) ){
			echo "Warning: duplicated annotation in DB2 $key with $key_generator\n";
		}
		else{
			$copy_ans2[$key] = $as;
		}
	}
	$only1 = array_diff_key($copy_ans1, $copy_ans2);
	$only2 = array_diff_key($copy_ans2, $copy_ans1);
	$both = array_intersect_key($copy_ans1, $copy_ans2);
	
	return array("name"=>$name, "a"=>count($only1), "b"=>count($only2), "ab"=>count($both), "type"=>$type);	
}

/**
 * Drukuje porównanie anotacji w postaci tabeli
 */
function print_in_table($config, $ans1, $ans2, $annotation_type){
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
	echo "<thead><tr><th>Only A ({$config->url1})</th><th>Both A and B</th><th>Only B ({$config->url2})</th></tr></thead>";
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
		$vals = explode("_", $key);
		if ( isset($in1[$key]) && isset($in2[$key]) ){
			$b = "<a href='{$config->url1}?page=report&amp;subpage=annotator&amp;id={$vals[0]}&amp;char_from={$vals[1]}&amp;char_to={$vals[2]}' target='_blank'>$val</a>";
		}
		else if ( isset($in1[$key]) ){
			$a = "<a href='{$config->url1}?page=report&amp;subpage=annotator&amp;id={$vals[0]}&amp;char_from={$vals[1]}&amp;char_to={$vals[2]}' target='_blank'>$val</a>";
		}
		else{
			$c = "<a href='{$config->url2}?page=report&amp;subpage=annotator&amp;id={$vals[0]}&amp;char_from={$vals[1]}&amp;char_to={$vals[2]}' target='_blank'>$val</a>";
		}
		echo sprintf("<tr><td style='color: red'>%s</td><td style='color: blue'>%s</td><td style='color: orange'>%s</td></tr>\n", $a, $b, $c);		
	}

	echo "</tbody>";
	echo "</table>";
}
	
?>
