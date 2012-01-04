<?
/**
 * 
 */
class DbBrowse{
	
	/**
	 * Input (reports.corpora, corpora_flags.short)
	 * Return (flags.flag_id, flags.name, count)
	 */
	static function getCorpusFlagsData($corpus_id,$flag_short){
  		global $db;
  		$flag_name_s = 'AND cf.short=\'' . $flag_short . '\' ';
  		$sql = "SELECT f.flag_id AS id, f.name AS name, COUNT(DISTINCT r.id) as count " .
  				"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				"LEFT JOIN reports_annotations an ON an.report_id=r.id " .
  				"WHERE r.corpora=? " .
  				$flag_name_s .
  				"GROUP BY f.name " .
  				"ORDER BY f.name ASC ";			
				
		$args = array($corpus_id);
		
		return $db->fetch_rows($sql, $args);
	}
	
	static function getCorpusFilterData($corpus_id,$select,$where,$group_by,$flag_short){
  		global $db;
  		$flag_name_s = ($flag_short ? 'AND cf.short=\'' . $flag_short . '\' ' : '' );
  		$sql = "SELECT ".
  				$select .
  				"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				"LEFT JOIN reports_annotations an ON an.report_id=r.id " .
  				"WHERE r.corpora=? " .
  				$flag_name_s .
  				$where .
  				$group_by;			
		//echo $sql ."\n\n";				
		$args = array($corpus_id);
		
		return $db->fetch_rows($sql, $args);
	}
	
	static function getCorpusSelectedFiltersData($corpus_id,$where,$sql_where_flag_name_parts,$sql_where_part,$flag_short){
  		global $db;
  		$flag_name_s = ($flag_short ? 'AND cf.short=\'' . $flag_short . '\' ' : '' );
  		$sql = "SELECT r.id AS id ".
  				"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				"LEFT JOIN reports_annotations an ON an.report_id=r.id " .
  				"WHERE r.corpora=? " .
  				$where .
  				" GROUP BY r.id ORDER BY r.id ASC ";
  		
  		$args = array($corpus_id);
  		$report_ids = array();
		if($flag_short == 'Names Rel'){
			//var_dump($sql_where_part);
		}  				
			$rows = $db->fetch_rows($sql, $args);
			//var_dump($rows);
			foreach($rows as $key => $value){
				$report_ids[] = $value['id'];				
			}
			//var_dump($report_ids);
		//}
			
		foreach($sql_where_part as $key => $value){
			if(preg_match("/^flag_/",$key)){
				if($value){
					$sql = "SELECT r.id AS id  ".
	  					"FROM reports r " .
  						"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  						"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  						"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
	  					"WHERE r.id IN  ('". implode("','",$report_ids) ."') " .
	  					" AND " . $sql_where_flag_name_parts[$key] .
  						" AND " . $sql_where_part[$key] .
	  					" GROUP BY r.id ORDER BY r.id ASC ";  							
					//echo $sql ."\n\n";				
					$rows = $db->fetch_rows($sql);
					$report_ids = array();
					foreach($rows as $key => $value){
						$report_ids[] = $value['id'];				
					}
				}
			}
		}							
  				
  				
		//echo $sql ."\n";  				
		$sql = "SELECT f.flag_id AS id, f.name AS name, COUNT(DISTINCT r.id) as count " .
  				"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				"WHERE r.id IN  ('". implode("','",$report_ids) ."') " .
  				$flag_name_s .
  				"GROUP BY f.name " .
  				"ORDER BY f.name ASC ";
		if($flag_short == 'Names Rel'){
			//echo $sql;
		}
		return $db->fetch_rows($sql);
	}
}
?>