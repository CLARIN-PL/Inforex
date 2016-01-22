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
	
	if ( $config->test == "tags" ){
		$config->description = "Zgodność tagów między kotu88.ddns.net/inforex1 i kotu88.ddns.net/inforex2 dla flagi tags (gotowy/finished)";
		$config->url1 = "http://kotu88.ddns.net/inforex1";
		$config->url2 = "http://kotu88.ddns.net/inforex2";
		
		$config->dsn1 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex1");
		$config->dsn2 = parse_database_uri("root:alamakota@kotu88.ddns.net:3306/inforex2");
		
		$config->types = array('acontent', 'wordnet', 'propername', 'text');	
		$config->inforex1_flag = "tags";
		$config->inforex2_flag = "tags";
		$config->flag = "3";
	}
	else {
		echo "Zły config";
		exit;
	}
	
	$config->sql_reports = "SELECT r.id, re.acontent, re.wordnet, re.propername, re.text" .
			" FROM reports r" .
			" JOIN corpus_subcorpora cs ON (r.subcorpus_id=cs.subcorpus_id)" .
			" JOIN reports_flags rf ON (rf.report_id=r.id)" .
			" JOIN corpora_flags cf ON (cf.corpora_flag_id=rf.corpora_flag_id)" .
			" JOIN reports_ext_23 re ON (r.id=re.id)" .
			" WHERE rf.flag_id IN ({$config->flag}) AND cf.short = ?";
	if ( is_array($config->subcorpus_ids) && count(is_array($config->subcorpus_ids)) ){
		$config->sql_reports .= " AND r.subcorpus_id IN (" . implode(", ", $config->subcorpus_ids) . ")";
	}

	$config->types = array('acontent', 'wordnet', 'propername', 'text');
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
	$data1 = $db1->fetch_rows($config->sql_reports, array($config->inforex1_flag));	
	foreach ($data1 as $r){
		$reports1[] = $r['id'];
	}

	$db2 = new Database($config->dsn2);
	$data2 = $db2->fetch_rows($config->sql_reports, array($config->inforex2_flag));
	foreach ($data2 as $r){
		$reports2[] = $r['id'];
	}
	

	$reports = array_intersect($reports1, $reports2);
	echo "<h1>{$config->description}</h1>";
	echo sprintf("Dokumenty oznakowane przez dwie osoby (%d): %s\n\n", count($reports), implode(", ", $reports));
	echo "<pre>";

	$data_all = array();
	
	foreach (array("data1" => $data1, "data2" => $data2) as $name => $data){
		foreach ($data as $r){		
			$id = $r['id'];
			if (in_array($id, $reports)){			
				foreach ($config->types as $type){
					$r[$type] = array_filter(explode(';;', $r[$type]));
					natcasesort($r[$type]);
				}
				if (array_key_exists($id, $data_all))
					$data_all[$id][$name] = $r;
				else
					$data_all[$id] = array($name => $r);
			}		
		}	
	}
	

	echo '<table border=1 style="border: 1px solid black; border-collapse: collapse">';
	echo "<thead><tr><th>type</th><th>report id</th><th>Only A ({$config->url1})</th><th>Both A and B</th><th>Only B ({$config->url2})</th></tr></thead>";
	echo "<tbody>";	
	
	foreach ($config->types as $type){
		echo "<tr><td>$type</td><td colspan='4'></td></tr>";
		$count_both = 0;
		$count_a = 0;
		$count_b = 0;
		foreach ($data_all as $id => $data){
			$data1 = $data['data1'];
			$data2 = $data['data2'];
			
			$both = array_intersect($data1[$type], $data2[$type]);
			$only_a = array_diff($data1[$type], $data2[$type]);
			$only_b = array_diff($data2[$type], $data1[$type]);
			
			$a_str = implode("<br/>", $only_a);
			$both_str = implode("<br/>", $both);
			$b_str = implode("<br/>", $only_b);
			
			$count_both += count($both);
			$count_a += count($only_a);
			$count_b += count($only_b);
			
			$link_a = "<a href='{$config->url1}/index.php?page=report&subpage=metadata&corpus=&id={$id}'>A</a>";
			$link_b = "<a href='{$config->url2}/index.php?page=report&subpage=metadata&corpus=&id={$id}'>B</a>";
			echo sprintf("<tr><td></td><td>%s %s %s</td><td style='color: red'>%s</td><td style='color: blue'>%s</td><td style='color: orange'>%s</td></tr>\n", $id, $link_a, $link_b, $a_str, $both_str, $b_str);
		}
		echo "<tr><td colspan='5'><hr></td></tr>";
		$psa = 0;
		if ((2 * $count_both + $count_a + $count_b) > 0)
			$psa = 2 * $count_both / (2 * $count_both + $count_a + $count_b);
		
		echo sprintf("<tr align='center'><td>%s summary</td><td>PSA: %f</td><td>%s</td><td>%s</td><td>%s</td></tr>", $type, $psa, $count_a, $count_both, $count_b);
		echo "<tr><td colspan='5'><hr></td></tr>";
	}
	
	echo "</tbody>";
	echo "</table>";
		
} 

	
?>
