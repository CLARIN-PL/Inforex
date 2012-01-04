<?
/**
 * Read files from given folder.
 */
class FolderReader{
	
	static function readFilesFromFolder($folder){
		$documents = array();
		
		if ($handle = opendir($folder)) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file[0] != "." && $file != "..") {
		            $documents[] = "$folder/$file";
		        }
		    }
		    closedir($handle);
		}
		return $documents;	
	}		
	
}

?>