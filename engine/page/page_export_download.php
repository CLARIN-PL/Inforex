<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
require_once(implode(DIRECTORY_SEPARATOR, array(Config::Config()->get_path_engine(), "page", "page_corpus_export.php")));

class Page_export_download extends CPage{

    function __construct(){
		parent::__construct();
		$this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
	}

	function execute(){
		$export_id = $_GET['export_id'];
		$file = Page_corpus_export::getExportFilePath($export_id);
        ob_clean();
        ob_end_flush();
		header('Content-Type: application/x-7z-compressed;');
		header("Content-Disposition: attachment; filename=\"inforex_export_{$export_id}.7z\"");		
	 	header('Content-Length: '.filesize($file)."\\n");
		readfile($file);
		exit();
	}
		
}
