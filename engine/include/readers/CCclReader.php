<?

/** 
 * Object reader for ccl format.
 * Read a ccl file and transform into CclDocument object. 
 */

class CclReader{
	
	static function readCclDocumentFromFolder($path){
		$documents = FolderReader::readFilesFromFolder($path);
		$cclDocuments = array();
		foreach ($documents as $d){
			echo $d . "\n";			
			try{
				$cclDocuments[] = WcclReader::readDomFile($d);
			}
			catch(Exception $ex){
				print_r($ex);
			}
		}
		return $cclDocuments;
	}	
}

?>
