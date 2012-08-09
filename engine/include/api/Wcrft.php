<?

class Wcrft{
	
	var $wcrft_folder = null;
	var $model = null;
	
	function __construct($wcrft_folder){
		$this->wcrft_folder = $wcrft_folder;
		if ( !file_exists($this->getExec()))
			throw new Exception("Path to wcrft.py is incorrect.\nCheck: '{$this->getPath()}'");
	}
	
	function setModel($model){
		$this->model = $model;
	}
	
	function getModel(){
		if ( !isset($this->model) )
			throw new Exception("Path to WCRFT model not set. Use 'setModel(model)'");
		return $this->model;
	}

	function getExec(){
		return $this->wcrft_folder . "/wcrft/wcrft.py";
	}
	
	function getPath(){
		return $this->wcrft_folder;
	}
	
	function tag($text, $input_format="ccl", $output_format="ccl"){
		$p = $this->getPath();
		$m = $this->getModel();
		$cmd = "{$this->getExec()} {$this->getPath()}/config/nkjp.ini -d {$m} -i {$input_format} -o {$output_format} -";
		$cmd = sprintf("LANG=en_US.utf-8; echo %s | %s", escapeshellarg($text), $cmd);
		return exec_shell_asserted($cmd);
	}
	
}

?>