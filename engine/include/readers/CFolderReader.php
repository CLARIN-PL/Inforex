<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Read files from given folder.
 */
class FolderReader{
	
	static function readFilesFromFolder($folder){
		$documents = array();
		
		if ($handle = opendir($folder)) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file[0] != "." && $file != "..") {
		            $documents[] = "$folder/$file";
		        }
		    }
		    closedir($handle);
		}
		return $documents;	
	}		
	
}

?>