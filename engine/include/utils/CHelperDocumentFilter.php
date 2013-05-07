<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class HelperDocumentFilter{
	
	/**
	 * Return list of corpus custom filters — enum fields from extended data table.
	 * @return array of arrays({name=>String, values=>array(), selected=>true|false})
	 */
	function getCorpusCustomFilters($corpus_id, $ext_filters=array()){
		$filters = array();
		$table_name = DbCorpus::getCorpusExtTable($corpus_id);
		$columns = DbCorpus::getCorpusExtColumns($table_name);
		foreach ($columns as $c){
			if ( preg_match("/enum\((.*)\)/", $c["type"], $m) ){
				$name = $c["field"];
				
				$values = array();
				foreach (explode(",", str_replace("'", "", $m[1])) as $v){
					$values[$v] = $v;
				}
				
				$filters[] = array(
					"name"     => $name,
					"values"   => $values,
					"selected" => array_get_str($ext_filters, $name, null) ,
					"all"      => true
				);												
			}
		}	
		return $filters;		
	}
	
	/**
	 * From given array select variables matching pattern filter_name. The result
	 * contains variables with stripped "filter_" prefix.
	 * 
	 * $input = array("filter_owner_id"=>1, "page"=>"2");
	 * ...
	 * $output = array("owner_id"=>1);
	 * 
	 * @return assoc array({name=>value})
	 */
	function gatherCorpusCustomFilters($tab){
		$ext_filters = array();
		foreach ($_GET as $k=>$v){
			if ( $v && preg_match("/^filter_(.*)/", $k, $m) ){
				$ext_filters[$m[1]] = $v;
			}
		}
		return $ext_filters;		
	}
	
}

?> 