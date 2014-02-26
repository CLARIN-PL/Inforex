<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTag{

	static function saveTag($token_id, $base_id, $ctag_id, $disamb, $pos){
		global $db;
		$sql = "INSERT INTO tokens_tags_optimized(`token_id`, `base_id`, `ctag_id`, `disamb`, `pos`) VALUES(?,?,?,?,?);";
		$db->execute($sql, array($token_id, $base_id, $ctag_id, $disamb, $pos));
		return $db->last_id();
	}
	
	static function getTagsByReportIds($report_ids){
		global $db;

		$sql = "SELECT tokens_tags.*, tokens.report_id as report_id" .
				" FROM tokens JOIN tokens_tags ON tokens_tags.token_id=tokens.token_id " .
				" WHERE tokens.report_id IN ('" . implode("','",$report_ids) . "')";
				
		return $db->fetch_rows($sql);
	}

	static function getTagsByReportId($report_ids){
		global $db;

		$sql = "SELECT token_id, base, ctag, disamb" .
				" FROM tokens_tags" .
				" WHERE report_id = $report_ids ";
		return $db->fetch_rows($sql);
	}
	
	static function getTagsByTokenIds($tokens_ids){
		global $db;

		$sql = "SELECT * " .
				" FROM tokens_tags " .
				" WHERE token_id IN ('" . implode("','",$tokens_ids) . "')";

		return $db->fetch_rows($sql);
	}
	
	static function deleteTag($tag_id){
		global $db;
		$sql = "DELETE FROM tokens_tags_optimized tto WHERE token_tag_id = ?";
		$db->execute($sql, array($tag_id));
		
		DbTag::cleanAfterDelete();
	}
	
	/**
	 * Usuwa tagi, które nie są przypisane do żadnego tokena
	 */
	static function clean(){
		global $db;
		$sql = "DELETE tto.* FROM tokens_tags_optimized tto ".
				" LEFT JOIN tokens t USING(token_id) ".
				" WHERE t.token_id IS NULL";
		
		$db->execute($sql);
		
		DbTag::cleanAfterDelete();
	}	
	
	static function cleanAfterDelete(){
		// CLEAN BASES
		DbBase::clean();
		// CLEAN CTAGS
		DbCtag::clean();
	}
	
}

?>