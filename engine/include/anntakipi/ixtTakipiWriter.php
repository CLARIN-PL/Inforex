<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class TakipiWriter{
	
	var $f = null;
	
	function __construct($filename){
		$this->f = fopen($filename, "w");
		if ($this->f === false)
			throw new Exception("Could not open the file $filename");
	}
	
	function startSentence(){
		fwrite($this->f, '<chunk type="s">'."\n");		
	}

	function writeToken($t){
		if ($t->ns){
			fwrite($this->f, "<ns/>\n");
		}
		$str = "<tok>\n%s<orth>%s</orth>\n%s</tok>\n";
		$str_iob = "<iob>%s</iob>\n";
		$str_lex = "<lex><base>%s</base><ctag>%s</ctag></lex>\n";		
		$str_lex_disamb = "<lex disamb=\"1\"><base>%s</base><ctag>%s</ctag></lex>\n";

		$lexems = array();
		foreach ($t->lex as $lex){
			$pattern = $lex->disamb ? $str_lex_disamb : $str_lex;
			$lexems[] = sprintf($pattern, $lex->base, $lex->ctag); 
		}
		
		$iob = "";
		if (count($t->channels)){
			ksort($t->channels);
			$annotations = array();
			foreach ($t->channels as $name=>$type)
				$annotations[] = "$type-" . mb_strtoupper($name);
			$iob = sprintf($str_iob, implode(" ", $annotations));
		}
		
		fwrite($this->f, sprintf($str, $iob, $t->orth, implode($lexems)));		
		
	}
	
	function endSentence(){
		fwrite($this->f, '</chunk>'."\n");
	}
	
	function writeSentence(TakipiSentence $sentence){
		$this->startSentence();
		foreach ($sentence->tokens as $t)
			$this->writeToken($t);
		$this->endSentence();
	}
	
	function close(){
		fclose($this->f);
	}
	
	function writeDocument(TakipiDocument $document){
		foreach ($document->sentences as $sentence)
			$this->writeSentence($sentence);		
	}
	
}
?>