<?

class Semql{

	var $semql_path = null;
	
	function __construct($semql_path){
		$this->semql_path = $semql_path;
		if ( !file_exists($this->getExec()))
			throw new Exception("Path to semqell-analyze.py is incorrect.\nCheck: '{$this->getExec()}'");
	}
	
	function getExec(){
		return $this->semql_path . "/semquel-analyze.py";
	}
	
	function getPath(){
		return $this->semql_path;
	}
	
	function analyze($text){
		$cmd = sprintf("echo %s | {$this->getExec()} -j -i {$this->getPath()}/data/transformations.bin", escapeshellarg($text));
		return exec_shell_asserted($cmd);
	}
		
}