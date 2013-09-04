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
		//$text = escapeshellarg($text);
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


	static function tagWithMaca($text, $format="xces"){
		$text = escapeshellarg($text);
		$text = preg_replace("/( )+/", " ", $text);
		$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -o %s 2>/dev/null', $text, $format);
		
		$text_tagged = shell_exec($cmd);
		if ($format == "xces"){
			$text_tagged = HelperTokenize::xcesToCcl($text_tagged);
		}
	
		return $text_tagged;		
	}	

	/**
	 * $useSentencer --- wymusza użycie sentencera. Jeżeli w dokumencie są znaczniki <sentence>, to są one usuwane.
	 */ 
	static function tagWithMacaWmbt($text, $useSentencer=false){
		if (preg_match("/<cesAna/", $text))
			return HelperTokenize::tagPremorphWithMacaWmbt($text, $useSentencer);
		else
			return HelperTokenize::tagPlainWithMacaWmbt($text, $useSentencer); 
	}

	static function tagPremorphWithMacaWmbt($text, $useSentencer=false){
		global $config;
		$input = $useSentencer ? "premorph-stream" : "premorph-stream-nosent";
		$wmbt = $config->wmbt_cmd;
		$text = escapeshellarg($text);
		$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -o xces -i %s 2>/dev/null | %s - -o ccl 2>/dev/null', $text, $input, $wmbt);
		ob_start();
		$text_tagged = shell_exec($cmd);
		ob_end_clean();
		return $text_tagged;		
	}		
	
	static function tagPremorphWithMacaWcrft($text, $useSentencer=false){
		global $config;
		$input = $useSentencer ? "premorph" : "premorph-stream-nosent";
		$wmbt = sprintf("wcrft %s -d %s -i ccl -A -o ccl -", $config->get_wcrft_config(), $config->get_path_wcrft_model());
		$text = escapeshellarg($text);
		$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -i %s -o ccl 2>/dev/null | %s 2>/dev/null', $text, $input, $wmbt);
		ob_start();
		$text_tagged = shell_exec($cmd);
		ob_end_clean();
		return trim($text_tagged);		
	}	
	
	static function tagPlainWithMacaWmbt($text, $sentences=false){
		if (preg_match("[<sentence>]", $text)){
			$text = strip_tags($text, "<sentence>");
		}else{
			$text = strip_tags($text);
		}
		$text = htmlspecialchars($text);
		$text = "<cesAna><chunkList><chunk><chunk type='s'>$text</chunk></chunk></chunkList></cesAna>";
		return HelperTokenize::tagPremorphWithMacaWmbt($text, $sentences);
	}		

	static function tagPlainWithWcrft($text){
		global $config;
		$wcrft = sprintf("wcrft %s -d %s -i ccl -A -o ccl -", $config->get_wcrft_config(), $config->get_path_wcrft_model());
		$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -i plain -o ccl 2>/dev/null | %s 2>/dev/null', escapeshellarg($text), $wcrft);
		ob_start();
		$text_tagged = shell_exec($cmd);
		ob_end_clean();		
		return $text_tagged;
	}		

	static function tagWithMacaWmbtBatch($texts){
		global $config;
		
		$files = array();
		$basepath = "/tmp/wmbt"; 
		$input = "/tmp/wmbt/input";
		$output = "/tmp/wmbt/output";
		
		if (!file_exists($basepath)) mkdir($basepath);
		
		if (file_exists($input) ) unlink($input);
		mkdir($input);

		if (file_exists($output) ) unlink($output);
		mkdir($output);

		foreach ($texts as $k=>$text){		
			$text = escapeshellarg($text);
			$text = preg_replace("/( )+/", " ", $text);
			$text = "<doc>" . strip_tags($text, "<sentence>") . "</doc>";
			
			$cmd = sprintf('echo %s | maca-analyse -qs morfeusz-nkjp -o %s 2>/dev/null', $text, "xces");
			ob_start();
			$text_tagged = shell_exec($cmd);
			ob_end_clean();
						
			$path = "$input/$k";
			file_put_contents($path, $text_tagged);
			$files[] = $path;
		}
		file_put_contents("$basepath/list.txt", implode("\n", $files));
	}
}

?>
