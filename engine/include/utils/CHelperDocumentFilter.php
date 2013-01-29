<?
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
				$filters[] = array(
					"name"=>$name,
					"values"=>explode(",", str_replace("'", "", $m[1])),
					"selected"=>isset($ext_filters[$name])?$ext_filters[$name]:null 
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