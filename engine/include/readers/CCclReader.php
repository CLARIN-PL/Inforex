<?

/** 
 * Object reader for ccl format.
 * Read a ccl file and transform into CclDocument object. 
 */

class CclReader{
	
	static function readCclDocumentFromFolder($path){
		$documents = FolderReader::readFilesFromFolder($path);
		$cclDocuments = array();
		foreach ($documents as $d)
			$cclDocuments[] = WcclReader::readDomFile($d);
		return $cclDocuments;
	}	
}

?>
