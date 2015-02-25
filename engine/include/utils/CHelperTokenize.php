<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class HelperTokenize{

	static function xcesToCcl($text){
		$lines = explode("\n", $text);
		$lines[0] = "";
		$lines[1] = "";
		$lines[2] = "";
		$lines[count($lines)-1] = "";
		$lines[count($lines)-2] = "";
		$text_tagged = implode("\n", $lines);
		$text_tagged = str_replace("<chunkList>", "", $text_tagged);
		$text_tagged = str_replace("</chunkList>", "", $text_tagged);
		$text_tagged = str_replace("<chunk>", "", $text_tagged);
		$text_tagged = preg_replace("/<\/chunk>[ \n]*<\/chunk>/", "</chunk>", $text_tagged);
		$text_tagged = "<doc>" . trim($text_tagged) . "</doc>";
		return $text_tagged;
	}

	static function tagWithTakipiWs($text, $guesser){
		global $config;
		$text = preg_replace("/<!DOCTYPE [^>]+>/", "", $text);
		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text, $guesser);
		$text_tagged = "<doc>".$tagger->tagged."</doc>"; 
		return $text_tagged;		
	}

	static function tagPremorphWithMaca($text, $sentences=false){
		$input = $sentences ? "premorph-stream-nosent" : "premorph-stream";
		$cmd = sprintf('echo "%s" | maca-analyse -qs morfeusz-nkjp -o ccl -i %s 2>/dev/null', $text, $input);
		$text_tagged = shell_exec($cmd);	
		return $text_tagged;		
	}	

	static function tagPremorphWithWcrft2($text, $sentences=false){
		global $config;
		$input = $sentences ? "premorph-stream-nosent" : "premorph-stream";
		$tmp = ".inforex_tokenize.tmp";
		file_put_contents($tmp, $text);
		$cmd_template = 'cat %s | maca-analyse -qs morfeusz-nkjp -i %s -o ccl | wcrft-app %s -i ccl -o ccl - 2>/dev/null';
		$cmd = sprintf($cmd_template, $tmp, $input, $config->get_wcrft2_config());
		$text_tagged = shell_exec($cmd);
		if (file_exists($tmp)) unlink($tmp);
		return $text_tagged;		
	}	

	static function tagPlainWithWcrft2($text){
		global $config;
		$input = "txt";
		$tmp = ".inforex_tokenize.tmp";
		file_put_contents($tmp, $text);
		$cmd_template = 'cat %s | maca-analyse -qs morfeusz-nkjp -i %s -o ccl | wcrft-app %s -i ccl -o ccl - 2>/dev/null';
		$cmd = sprintf($cmd_template, $tmp, $input, $config->get_wcrft2_config());
		$text_tagged = shell_exec($cmd);
		if (file_exists($tmp)) unlink($tmp);
		return $text_tagged;		
	}	

	static function tagWithMaca($text, $format="xces"){
		$text = escapeshellarg($text);
		$tmp = ".inforex_tokenize.tmp";
		file_put_contents($tmp, $text);
		$cmd = sprintf('cat %s | maca-analyse -qs morfeusz-nkjp -o %s 2>/dev/null', $tmp, $format);		
		$text_tagged = shell_exec($cmd);
		if ($format == "xces")
			$text_tagged = HelperTokenize::xcesToCcl($text_tagged);
		if (file_exists($tmp)) unlink($tmp);
		return $text_tagged;		
	}	
	
	static function tagPremorphWithMacaWcrft($text, $useSentencer=false){
		global $config;
		$input = $useSentencer ? "premorph" : "premorph-stream-nosent";
		$wmbt = sprintf("wcrft %s -d %s -i ccl -A -o ccl -", $config->get_wcrft_config(),$config->get_path_wcrft_model());
		$text = escapeshellarg($text);
		$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -i %s -o ccl 2>/dev/null | %s 2>/dev/null', $text, $input, $wmbt);
		ob_start();
		$text_tagged = shell_exec($cmd);
		ob_end_clean();
		return trim($text_tagged);		
	}	
	
	static function tagPlainWithWcrft($text){
		global $config;
		$wcrft = sprintf("wcrft %s -d %s -i ccl -o ccl -", $config->get_wcrft_config(), $config->get_path_wcrft_model());
		$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -i plain -o ccl | %s', escapeshellarg($text), $wcrft);
		ob_start();
		$text_tagged = shell_exec($cmd);
		ob_end_clean();		
		return $text_tagged;
	}		

}

?>
