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
	    if (is_file($file) && is_readable($file)) {
			session_write_close();
			set_time_limit(0);
			ini_set('zlib.output_compression', 'Off');
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
		    header('Content-Type: application/octet-stream');
		    header("Content-Disposition: attachment; filename=\"inforex_export_{$export_id}.zip\"");
			header('Content-Transfer-Encoding: binary');
			header('Content-Encoding: identity');
			header('Content-Length: ' . filesize($file));
			header('Cache-Control: no-store, no-cache, must-revalidate, no-transform');
			header('Pragma: no-cache');
			header('X-Accel-Buffering: no');
			$handle = fopen($file, 'rb');
			while (!feof($handle)) {
				echo fread($handle, 1048576);
				flush();
			}
			fclose($handle);
	        exit();
	    } else { // File not found
            // write to error log
            error_log("Export file :$file doesn't exists.");
            $this->set('file',$file);
        }
	}
		
}
