<?
class Liner{
	
	var $liner_path = null;
	var $python_path = null;
	var $model = null;
	var $chunking = null;
	var $cseq = null; 
	
	function __construct($python_path, $liner_path, $model){
		$this->liner_path = $liner_path;
		$this->python_path = $python_path;
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
		
		$cmd = sprintf("LANG=en_US.utf-8; java -Djava.library.path={$this->liner_path}/production/lib -jar {$this->liner_path}/production/liner.jar tag '%s' -nerd %s -filter all -python %s -ini %s", $str, $config->path_nerd, $this->model, $this->python_path);
		
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
	
	function chunkSentences($sentences_iob){
		global $config;
		
		$this->cseq = null;
		$this->chunking = null;
		
		$sentences_joined = array();
		
		foreach ($sentences_iob as $sentence_iob){
			
			$tokens_joined = array();
			$cseq = "";
			foreach ($sentence_iob as $token){
				$tokens_joined[] = implode(" ", $token);
				$cseq .= " " . $token[0];
			}
			$this->cseq[] = trim($cseq);
			$sentences_joined[] = implode("  ", $tokens_joined);
		}
		
		$text_to_parse = implode("   ", $sentences_joined);
		$text_to_parse = str_replace("'", "\\'", $text_to_parse);
		
		$cmd = sprintf("LANG=en_US.utf-8; java -Djava.library.path={$this->liner_path}/production/lib -jar {$this->liner_path}/production/liner.jar tag '%s' -nerd %s -ini %s -filter all -python %s", $text_to_parse, $config->path_nerd, $this->model, $this->python_path);
		
		ob_start();
		$cmd_result = shell_exec($cmd);		
		$r = ob_get_clean();

		$chunkingsStr = explode(";", $cmd_result);
		$chunkings = array();
		foreach ($chunkingsStr as $chunkingStr){
			preg_match_all("/([0-9]+),([0-9]+),([A-Z_]*)/", $chunkingStr, $matches, PREG_SET_ORDER);
			$chunking = array();
			foreach ($matches as $m){
				$chunking[] = array($m[1], $m[2], $m[3]);
			}
			$chunkings[] = $chunking;
		}
		$this->chunking = $chunkings;
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
		$sentences = array();
		$i=0;
		foreach ($this->chunking as $chunking){
			$chunkingChar = array();
			$cseq = $this->cseq[$i];
			foreach ($chunking as $chunk){
				$from = $chunk[0] - substr_count(mb_substr($cseq, 0, $chunk[0]), ' ');
				$to = $chunk[1] - substr_count(mb_substr($cseq, 0, $chunk[1]), ' ');
				$chunkingChar[] = array($from, $to, $chunk[2]);
			}
			$sentences[] = $chunkingChar;
			$i++;
		}
		return $sentences;		
	}
}
?>