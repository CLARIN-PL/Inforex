<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTag{
	
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
	
	
}

?>