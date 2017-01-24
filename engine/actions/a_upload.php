<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Action_upload extends CAction{
		
	function checkPermission(){
		global $user, $corpus;
		if (!isset($user['role']['admin']) && $corpus['user_id']!=$user['user_id'])
			return "Tylko administrator i właściciel korpusu mogą ustalać prawa dostępu";
		else
			return true;
	} 
	
	function execute(){
		global $corpus, $db, $user;
		$corpus_id = $corpus['id'];
		$number_of_imported_documents = 0;
		
		if ($_FILES["files"]["tmp_name"] == ""){
			$this->set("action_error", "File not found");
			return null;
		}
		
		$zip = new ZipArchive();
		$res = $zip->open($_FILES["files"]["tmp_name"]);
		
		if ($res === TRUE) {
			$tempfile = tempnam(sys_get_temp_dir(), "upload_" . $corpus['id']);
			if (file_exists($tempfile)) { unlink($tempfile); }
			mkdir($tempfile);
			
			// extract it to the path we determined above
			$zip->extractTo($tempfile);
			$zip->close();
			
			$files = array();
			$this->getDirContents($tempfile, $files);
			
			$files_filtered = array();
			foreach ($files as $file){
				if ( strtolower(substr($file, strlen($file)-4, 4)) == ".txt" ){
					$subcorpus = null;
					$basename = basename($file);
					$title = null;
					
					$parts = explode("-", $basename);
					if ( count($parts) > 1 ){
						$subcorpus = $parts[0];
						$title = $parts[1];
					}
					else{
						$title = $basename;
					}					
					$files_filtered[] = array("path"=>$file, "basename"=>$basename, "title"=>$title, 'subcorpus'=>$subcorpus);		
				}
			}
			
			$subcorpora = array();
			foreach ( DbCorpus::getCorpusSubcorpora($corpus['id']) as $row ){
				$subcorpora[strtolower($row['name'])] = $row['subcorpus_id'];
			}
			
			foreach ($files_filtered as $file){
				$subcorpus = $file['subcorpus'];
				if ( $subcorpus != null ){
					if ( !isset($subcorpora[$subcorpus]) ){
						$subcorpus_id = DbCorpus::createSubcopus($corpus_id, $subcorpus, "");
						$subcorpora[strtolower($subcorpus)] = $subcorpus_id;						
					}
				}
				
				$document = array();
				$document['corpora'] = $corpus_id;
				$document['title'] = $file['title'];
				if ( $subcorpus != null ){
					$document['subcorpus_id'] = $subcorpora[$subcorpus];					
				}
				$document['user_id'] = $user['user_id'];
				$document['content'] = file_get_contents($file['path']);
				$document['status'] = 2;
				$document['format_id'] = 2; // TXT
				$db->insert("reports", $document);
				$number_of_imported_documents++;
			}
			
			$this->set("action_performed", $temp_file);
			return;
		} else {
			 $this->set("action_error", "Couldn't open file");
			 return null;
		}
		
		$this->set("action_performed", "Numer of uploaded documents: " . $number_of_imported_documents);
		return null;
	}	
	
	function getDirContents($dir, &$results = array()){
		$files = scandir($dir);
	
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				$this->getDirContents($path, $results);
				$results[] = $path;
			}
		}
	
		return $results;
	}
} 

?>
