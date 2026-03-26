<?php
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

	static function getReportImages($report_id){
	    global $db;

	    $sql = "SELECT rai.image_id AS 'id', i.original_name AS 'name' FROM reports_and_images rai 
                JOIN images i ON rai.image_id = i.id
                WHERE report_id = ?";
	    $images = $db->fetch_rows($sql, array($report_id));

	    return $images;
    }

    static function deleteImage($image_id, $image_name){
        global $db;

	    $sql = "DELETE FROM reports_and_images WHERE image_id = ?";
	    $db->execute($sql, array($image_id));

        $sql = "DELETE FROM images WHERE id = ?";
        $db->execute($sql, array($image_id));

        $image_path = Config::Cfg()->get_path_www() . "/images/" . $image_id . "_" . $image_name;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
	
}

?>
