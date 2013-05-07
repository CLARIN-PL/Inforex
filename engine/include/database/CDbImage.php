<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbImage{

	static function addImageToReport($report_id, $image_id){
		global $db;
		$pos = DbImage::getMaxDocumentImagePosition($report_id) + 1;
		$sql = "INSERT INTO reports_and_images(report_id, image_id, position) VALUES(?, ?, ?)";
		$db->execute($sql, array($report_id, $image_id, $pos));
	}
	
	static function getMaxDocumentImagePosition($report_id){
		global $db;
		$sql = "SELECT MAX(position) FROM reports_and_images WHERE report_id = ?";
		return intval($db->fetch_one($sql, array($report_id)));
	}
	
}

?>