<?php
/*
 * Created on 2009-10-30
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class TextAligner{
	
	var $text;
	var $index = 0;
	var $is_begin = false;
	var $is_end = false;
	var $annotation_name = "";
	var $logs = array();
	
	function __construct($annotated_text){
		$content = $annotated_text;
		$content = html_entity_decode($content);
        $content = preg_replace('/<(\/)?[pP]>/s', ' ', $content);
        $content = preg_replace('/<br(\/)?>/s', ' ', $content);
        $content = trim($content);
		$this->text = $content;
	}
	
	function at($index){
		return $this->text[$index];
	}
	
	/**
	 * Dopasowuje dany fragment tekstu. Jeżeli tekst został dopasowany zwraca true, wpp false. 
	 */
	function align($text_fragment){
		// Jeżeli po ostatnim wywołaniu osiągnięto koniec adnotacji, to usuń teraz jej nazwę.
		if ($this->is_end)
			$this->annotation_name = "";
			
		$this->is_begin = false;
		$this->is_end = false;
		$this->logs = array();
		
		// Sprawdz, czy jest początek adnotacji
		$i = $this->index;
		$this->log("===============");
		$this->log("@" .$this->index. ": " . mb_substr($this->text, $this->index, 40)."...");
		$this->log("   ? ".$text_fragment);
		if ($this->at($i) == "<" && substr($this->text, $i, 4) == "<an#"){
			$this->log($i, "i");
			$i = mb_strpos($this->text, ":", $i) + 1;
			$this->log($i, "i");
			$i_end = mb_strpos($this->text, ">", $i);
			$ann_name = mb_substr($this->text, $i, $i_end-$i);
			// Ustaw wskaźnik na pierwszy znak po adnotacji.
			$this->index = $i_end + 1;
			$this->log("=>@" .$this->index. ": " . mb_substr($this->text, $this->index, 40)."...");
			$this->pass_whitespaces();
			$this->is_begin = true;
			$this->annotation_name = $ann_name;
			$this->log("Ann: ".$ann_name);
		}
		$cutoff = mb_substr($this->text, $this->index, mb_strlen($text_fragment));
		$this->log($cutoff);
		$this->log(strlen($cutoff)); 
		if ( $cutoff == $text_fragment ){
			$this->index += strlen($text_fragment); 
		
			if (substr($this->text, $this->index, 5) == '</an>'){
				$this->index += 5;
				$this->is_end = true;
			}
				
			$this->pass_whitespaces();
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * Przewiń do pierwszego znagu nie będącego białym znakiem.
	 */	
	function pass_whitespaces(){
		while ( $this->index < strlen($this->text) && trim($this->text[$this->index])=='')
			$this->index++;		
	}
		
	function log($msg){
		$this->logs[] = $msg;		
	}
}
?>
