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

		$tmp_name = $_FILES["files"]["tmp_name"];

		if ( isset($_FILES['files']['error']) && intval($_FILES['files']['error'])>0){
            $phpFileUploadErrors = array(
                0 => 'There is no error, the file uploaded with success',
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk.',
                8 => 'A PHP extension stopped the file upload.',
            );
            $this->set("action_error", $phpFileUploadErrors[intval($_FILES['files']['error'])]);
            return null;
        }

		if ($tmp_name == ""){
			$this->set("action_error", "The zip file was not found");
			return null;
		}

		if ( !file_exists($tmp_name) ){
            $this->set("action_error", "The tmp file '$tmp_name' not found");
            return null;
        }

		$zip = new ZipArchive();
		$res = $zip->open($tmp_name);

        if ($res !== TRUE) {
            $this->set("action_error", "Couldn't open file");
            return null;
        }

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

                //Get filename without the extension.
                $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = basename($file, ".".$file_extension);

                $inifile = substr($file, 0, strlen($file)-4) . ".ini";
                if ( file_exists($inifile) ){
                    $ini = parse_ini_file($inifile, true, INI_SCANNER_RAW);
                    $title = $this->parseTitle($ini["metadata"]["title"], $basename);
                    $source = $ini["metadata"]["url"];
                    $author = $ini["metadata"]["author"];
                    $date =  $this->parseDate($ini["metadata"]["publish_date"]);
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
                        'filename' =>$filename,
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
            $document['filename'] = $file['filename'];
            $document['content'] = file_get_contents($file['path']);
            $document['status'] = 2;
            $document['format_id'] = 2; // TXT
            $db->insert("reports", $document);
            $number_of_imported_documents++;

            $report_id = $db->last_id();
            DbReport::insertEmptyReportExt($report_id);
        }

        $this->set("action_performed", "Number of uploaded files: {$number_of_imported_documents}");
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

	function parseDate($date){
	    $date = explode(" ", $date);
	    $date = $date[0];
	    $date = trim($date);
	    if ( $date == "" ){
	        return null;
        } else {
	        return date("Y-m-d", strtotime($date));
        }
    }

    function parseTitle($title, $titleIfEmpty){
	    $title = trim($title);
	    return $title == "" ? $titleIfEmpty : $title;
    }

}
