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
	
	static function getCorpusSelectedFiltersData($corpus_id,$select,$where,$group_by,$flag_short){
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
}
?>