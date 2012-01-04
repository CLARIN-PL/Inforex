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
				
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	static function getCorpusReportsIdsForFlags($report_ids,$sql_where_flag_name_parts,$sql_where_flag_values_part){
  		global $db;
  		$sql = "SELECT r.id AS id  ".
	  			"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
	  			"WHERE r.id IN ('". implode("','",$report_ids) ."') " .
	  			" AND " . $sql_where_flag_name_parts .
  				" AND " . $sql_where_flag_values_part .
	  			" GROUP BY r.id ORDER BY r.id ASC ";  
	  										
		return $db->fetch_rows($sql);
	}
	
	static function getCorpusFilterData($corpus_id,$select,$join,$where,$group_by,$flag_short=null){
  		global $db;
  		$flag_name_s = ($flag_short ? 'AND cf.short=\'' . $flag_short . '\' ' : '' );
  		$sql = "SELECT ".
  				$select .
  				" FROM reports r " .
  				$join.
  				" LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				" LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				" LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				" LEFT JOIN reports_annotations an ON an.report_id=r.id " .
  				" WHERE r.corpora=? " .
  				$flag_name_s .
  				$where .
  				$group_by;
  							
		return $db->fetch_rows($sql, array($corpus_id));
	}
	
	static function getReportsFilterData($report_ids,$select,$join,$where,$group_by){
  		global $db;
  		$sql = "SELECT ".
  				$select .
  				" FROM reports r " .
  				$join .
  				" LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				" LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				" LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				" LEFT JOIN reports_annotations an ON an.report_id=r.id " .
  				" WHERE r.id IN ('". implode("','",$report_ids) ."') " .
  				$where .
  				$group_by;
  							
		return $db->fetch_rows($sql);
	}
	
	static function getCorpusSelectedFiltersData($corpus_id,$where,$sql_where_flag_name_parts,$sql_where_part,$flag_short,$flag_no_space_name){
  		global $db;
  		$sql = "SELECT r.id AS id ".
  				"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				"LEFT JOIN reports_annotations an ON an.report_id=r.id " .
  				"WHERE r.corpora=? " .
  				$where .
  				" GROUP BY r.id ORDER BY r.id ASC ";
  		  		
  		$report_ids = array();
		$rows = $db->fetch_rows($sql, array($corpus_id));
		foreach($rows as $key => $value){
			$report_ids[] = $value['id'];				
		}
			
		foreach($sql_where_part as $key => $value){
			if(preg_match("/^flag_/",$key) && $key != $flag_no_space_name){
				if($value){
					$rows = DbBrowse::getCorpusReportsIdsForFlags($report_ids,$sql_where_flag_name_parts[$key],$sql_where_part[$key]);
					$report_ids = array();
					foreach($rows as $key => $value){
						$report_ids[] = $value['id'];				
					}
				}
			}
		}							
  		
  		$flag_name_s = ($flag_short ? 'AND cf.short=\'' . $flag_short . '\' ' : '' );		
		$sql = "SELECT f.flag_id AS id, f.name AS name, COUNT(DISTINCT r.id) as count " .
  				"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				"WHERE r.id IN  ('". implode("','",$report_ids) ."') " .
  				$flag_name_s .
  				"GROUP BY f.name " .
  				"ORDER BY f.name ASC ";
		return $db->fetch_rows($sql);
	}	
}
?>