<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_document_image_upload extends CAction{
	
	var $annotations_to_update = array();
	var $annotations_to_delete = array();
	
	function checkPermission(){
		if (hasRole("admin") || hasCorpusRole("add_documents") || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji dokumentów";
	} 
		
	function execute(){
		global $user, $mdb2, $corpus, $config;
		$report_id = intval($_POST['report_id']);

		if ($_FILES['image']['error']>0){
			$this->set('error', "ERROR " . $_FILES['image']['error']);
			return "";
		}
		
		$filename = $_FILES['image']['name'];
		$ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
		$hashname = preg_replace("/[^a-zA-Z0-9_\-.]/m", "_", $filename);
		
		$image = new CImage();
		$image->setCorpusId($corpus['id']);
		$image->setOriginalName($filename);
		$image->setHashName($hashname);
		$image->save();
				
		$path = $config->path_secured_data . "/images" . "/" . $image->getServerFileName();
		DbImage::addImageToReport($report_id, $image->id);
		
		if (move_uploaded_file($_FILES['image']['tmp_name'], $path))
			$this->set("info", "The image was successfully uploaded");
		else
			$this->set("error", "There was an error during the file upload. Please try again.");
		
				
		return "";
	}
		
} 

?>
