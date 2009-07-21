<?php
class Page_backup extends CPage{
	
	function execute(){
		
		$sql_backup_folder = "/home/czuk/nlp/gpwc/sql"; 

		if ($_POST['backup']){		
			$date = date("Ymd_His");
			$file = "{$sql_backup_folder}/sql_backup_gpwc_{$date}.sql";	
			shell_exec("mysqldump -u root -pkrasnal gpw reports_types > $file");
			$output = true;
		}
		
		if ($_GET['file']){
			$file = $_GET['file']; 
			$display = true;
			$display_content = file_get_contents("$sql_backup_folder/$file");
		}
		
		$files = scandir($sql_backup_folder);
		$files = array_diff($files, array(".", ".."));
		rsort($files);
					
		$this->set('file', $file);
		$this->set('files', $files);
		$this->set('output', $output);
		$this->set('display', $display);
		$this->set('display_content', $display_content);
	}
}


?>
