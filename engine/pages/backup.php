<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_backup extends CPage{
	
	function execute(){
		
		$sql_backup_folder = "/home/czuk/nlp/gpwc/sql"; 

		if ($_POST['backup']){		
			$date = date("Ymd_His");
			$file = "{$sql_backup_folder}/sql_backup_gpwc_{$date}.sql";	
			shell_exec("mysqldump -u root -pkrasnal gpw > $file");
			shell_exec("7zr a {$file}.7z {$file}");
			unlink($file);
			$output = true;
		}
		
		$files = scandir($sql_backup_folder);
		$files = array_diff($files, array(".", ".."));
		rsort($files);
		
		$files_assoc = array();
		foreach ($files as $file){
			$ob = null;
			$ob['file'] = "{$sql_backup_folder}/$file";
			$ob['size'] = sprintf("%2.1d", filesize("$sql_backup_folder/$file")/1024) ." kb";
			$files_assoc[] = $ob;
		}
					
		$this->set('file', $file);
		$this->set('files', $files_assoc);
		$this->set('output', $output);
		$this->set('display', $display);
		$this->set('display_content', $display_content);
	}
}


?>
