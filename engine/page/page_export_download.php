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
        $downloadName = Page_corpus_export::getExportDownloadFilename($export_id);
        $contentType = substr($downloadName, -4) === '.zst' ? 'application/zstd' : 'application/octet-stream';
	    if (is_file($file) && is_readable($file)) {
			session_write_close();
			set_time_limit(0);
			ini_set('zlib.output_compression', 'Off');
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
				$fileSize = filesize($file);
				$start = 0;
				$end = $fileSize - 1;
				$isPartial = false;
				if (isset($_SERVER['HTTP_RANGE'])) {
					if (!preg_match('/^bytes=(\d+)-(\d*)$/', trim($_SERVER['HTTP_RANGE']), $matches)) {
						header('HTTP/1.1 416 Range Not Satisfiable');
						header('Content-Range: bytes */' . $fileSize);
						exit();
					}
					$start = intval($matches[1]);
					$end = $matches[2] === '' ? $end : min(intval($matches[2]), $end);
					if ($start > $end || $start >= $fileSize) {
						header('HTTP/1.1 416 Range Not Satisfiable');
						header('Content-Range: bytes */' . $fileSize);
						exit();
					}
					$isPartial = true;
					header('HTTP/1.1 206 Partial Content');
					header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
				}
		    header('Content-Type: ' . $contentType);
		    header("Content-Disposition: attachment; filename=\"{$downloadName}\"");
			header('Content-Transfer-Encoding: binary');
			header('Content-Encoding: identity');
			header('Accept-Ranges: bytes');
			header('Content-Length: ' . ($end - $start + 1));
			header('Cache-Control: no-store, no-cache, must-revalidate, no-transform');
			header('Pragma: no-cache');
			header('X-Accel-Buffering: no');
			if ($isPartial) {
				$handle = fopen($file, 'rb');
				fseek($handle, $start);
				$remaining = $end - $start + 1;
				while ($remaining > 0 && !feof($handle)) {
					$chunk = fread($handle, min(8192, $remaining));
					if ($chunk === false || $chunk === '') {
						break;
					}
					echo $chunk;
					$remaining -= strlen($chunk);
				}
				fclose($handle);
			} else {
				readfile($file);
			}
	        exit();
	    } else { // File not found
            // write to error log
            error_log("Export file :$file doesn't exists.");
            $this->set('file',$file);
        }
	}
		
}
