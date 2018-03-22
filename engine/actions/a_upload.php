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
        $subcorpus_id = intval($_POST['subcorpus_id']) ? intval($_POST['subcorpus_id']) : null;
		$autosplit = isset($_POST['autosplit']);
		$number_of_imported_documents = 0;

		if ($_FILES["files"]["tmp_name"] == ""){
			$this->set("action_error", "The zip file was not found");
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
					$source = "";
                    $author = "";
                    $date = null;
                    $basename = basename($file);
                    $title = $basename;

					$inifile = substr($file, 0, strlen($file)-4) . ".ini";
					if ( file_exists($inifile) ){
						$ini = parse_ini_file($inifile, true, INI_SCANNER_RAW);
                        $title = $ini["metadata"]["title"];
						$source = $ini["metadata"]["url"];
						$author = $ini["metadata"]["author"];
						$date = explode(" ", $ini["metadata"]["publish_date"]);
						$date = $date[0];
					} else {
                        $this->addWarning("A file with metadata for <b>" . basename($file). "</b> not found");
                    }

                    if ( $autosplit ) {
                        $parts = explode("-", $basename);
                        if (count($parts) > 1) {
                            $subcorpus = $parts[0];
                            $title = $parts[1];
                        } else {
                            $title = $basename;
                        }
                    }

					$files_filtered[] = array("path"=>$file, 
							'basename' => $basename,
							'title' => $title,
							'source' => $source,
							'date' => $date,
							'author' => $author,
							'subcorpus'=>$subcorpus);		
				}
			}
			
			$subcorpora = array();
			foreach ( DbCorpus::getCorpusSubcorpora($corpus['id']) as $row ){
				$subcorpora[strtolower($row['name'])] = $row['subcorpus_id'];
			}
			
			foreach ($files_filtered as $file){
                $document = array();

                if ( $autosplit ) {
                    $subcorpus = $file['subcorpus'];
                    if ($subcorpus != null) {
                        if (!isset($subcorpora[strtolower($subcorpus)])) {
                            $subcorpus_id = DbCorpus::createSubcopus($corpus_id, $subcorpus, "");
                            $subcorpora[strtolower($subcorpus)] = $subcorpus_id;
                        } else {
                            $subcorpus_id = $subcorpora[strtolower($subcorpus)];
						}
                        $document['subcorpus_id'] = $subcorpus_id;
                    }
                } else{
                    $document['subcorpus_id'] = $subcorpus_id;
				}

				$document['corpora'] = $corpus_id;
				$document['title'] = $file['title'];
				$document['source'] = $file['source'];
                $document['author'] = $file['author'];
                $document['date'] = $file['date'];
				$document['user_id'] = $user['user_id'];
				$document['content'] = file_get_contents($file['path']);
				$document['status'] = 2;
				$document['format_id'] = 2; // TXT
				$db->insert("reports", $document);
				$number_of_imported_documents++;
			}
			
			$this->set("action_performed", "Number of uploaded files: {$number_of_imported_documents}");
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
