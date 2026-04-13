<?php

require_once(implode(DIRECTORY_SEPARATOR, array(Config::Cfg()->get_path_engine(), "page", "page_corpus_export.php")));

class Page_export_download extends CPage{

    function __construct(){
		parent::__construct();
		$this->anyCorpusRole[] = CORPUS_ROLE_EXPORT;
	}

	function execute(){
		$export_id = intval($_GET['export_id']);
		$file = Page_corpus_export::getExportFilePath($export_id);
	    if (file_exists($file)) {
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
		    header('Content-Type: application/x-7z-compressed');
		    header("Content-Disposition: attachment; filename=\"inforex_export_{$export_id}.7z\"");
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($file));
			header('Cache-Control: private');
		    readfile($file);
	        exit();
	    } else { // File not found
            // write to error log
            error_log("Export file :$file doesn't exists.");
            $this->set('file',$file);
        }
	}
		
}
