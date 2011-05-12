<?
class Liner{
	
	var $liner_path = null;
	var $model = null;
	var $chunking = null;
	var $cseq = null; 
	
	function __construct($liner_path, $model){
		$this->liner_path = $liner_path;
		$this->model = $model;	
	}
	
	function chunk($sentence_iob){
		global $config;
		
		$cseq = "";
		$tokens_joined = array();
		
		foreach ($sentence_iob as $token){
			$tokens_joined[] = implode(" ", $token);
			$cseq .= " " . $token[0];
		}
		$str = implode("  ", $tokens_joined);
		$str = str_replace("'", "\\'", $str);
		$this->cseq = trim($cseq);
		
		//$cmd = sprintf("LANG=en_US.utf-8; java -Djava.library.path={$this->liner_path}/production/lib -jar {$this->liner_path}/production/liner.jar tag '%s' -chunker crfpp-load:%s", $str, $this->model);
		$cmd = sprintf("LANG=en_US.utf-8; java -Djava.library.path={$this->liner_path}/production/lib -jar {$this->liner_path}/production/liner.jar tag '%s' -nerd %s -ini %s -filter all", $str, $config->path_nerd, $this->model);
		fb($cmd);
		
		ob_start();
		$cmd_result = shell_exec($cmd);		
		$r = ob_get_clean();

		$chunking = array();
		preg_match_all("/([0-9]+),([0-9]+),([A-Z_]*)/", $cmd_result, $matches, PREG_SET_ORDER);
		foreach ($matches as $m){
			$chunking[] = array($m[1], $m[2], $m[3]);
		}

		$this->chunking = $chunking;
		return true;			
	}

	/**
	 * 
	 */
	function getChunking(){
		return $this->chunking;
	}	
	
	/**
	 * 
	 */
	function getChunkingChars(){
		$chunkingChar = array();
		foreach ($this->chunking as $chunk){
			$from = $chunk[0] - substr_count(mb_substr($this->cseq, 0, $chunk[0]), ' ');
			$to = $chunk[1] - substr_count(mb_substr($this->cseq, 0, $chunk[1]), ' ');
			$chunkingChar[] = array($from, $to, $chunk[2]);
		}
		
		return $chunkingChar;		
	}
}
?>