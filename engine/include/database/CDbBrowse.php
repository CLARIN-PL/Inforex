<?
/**
 * 
 */
class DbBrowse{

	/**
	 * Input (report_ids.array() - indeksy raportów, 
	 * 		  sql_where_flag_values_part - where sql (where_or format),
	 *        $sql_where_flag_name_parts - opcjonalnie where sql z nazwą flagi)
	 * Return (reports.id)
	 */	
	static function getCorpusReportsIdsForFlags($report_ids,$sql_where_flag_values_part,$sql_where_flag_name_parts=null){
  		global $db;
  		$sql = "SELECT r.id AS id  ".
	  			"FROM reports r " .
  				"LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				"LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
	  			"WHERE r.id IN ('". implode("','",$report_ids) ."') " .
	  			($sql_where_flag_name_parts ? " AND " . $sql_where_flag_name_parts : "" ) .
  				($sql_where_flag_values_part ? " AND " . $sql_where_flag_values_part : "") .
	  			" GROUP BY r.id ORDER BY r.id ASC ";  
	  	return $db->fetch_rows($sql);
	}
	
	/**
	 * Input (reports.corpora, select, join, where, group_by, corpora_flags.flag_short)
	 * Return (select)
	 */
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
	
	/**
	 * Input (report_ids.array(), select, join, where, group_by)
	 * Return (select)
	 */
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
	
	/**
	 * Input (report_ids.array(), 
	 *        $sql_where_flag_name_parts - where sql z nazwą flagi,       
	 *        sql_where_flag_values_part - opcjonalnie where sql,
	 *        
	 * Return (flags.flag_id, flags.name, count(reports.id))
	 */
	static function getCorpusSelectedFilterData($report_ids,$sql_where_flag_name_parts,$sql_where_part=null){
  		global $db;
  		
  		$sql = "SELECT f.flag_id AS id, f.name AS name, COUNT(DISTINCT r.id) as count " .
  				" FROM reports r " .
  				" LEFT JOIN reports_flags rf ON rf.report_id=r.id " .
  				" LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id " .
  				" LEFT JOIN flags f ON f.flag_id=rf.flag_id " .
  				" WHERE r.id IN  ('". implode("','",$report_ids) ."') " .
  				' AND ' . $sql_where_flag_name_parts . 
  				($sql_where_part ? " AND ". $sql_where_part : '')  .
  				" GROUP BY f.flag_id " .
  				" ORDER BY f.flag_id ASC ";
		return $db->fetch_rows($sql);
	}
}
?>