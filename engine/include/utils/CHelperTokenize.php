<?

class HelperTokenize{

	static function tagWithTakipiWs($text, $guesser){
		global $config;
		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text, $guesser);
		$text_tagged = "<doc>".$tagger->tagged."</doc>"; 
		return $text_tagged;		
	}

	static function tagWithMaca($text, $format="xces"){
		$text = str_replace('\\', '\\\\', $text);
		$text = str_replace('"', '\"', $text);
		$text = str_replace('$', '\$', $text);
		$text = str_replace("`", '\`', $text);
		$text = str_replace("\n", ' ', $text);
		$text = preg_replace("/( )+/", " ", $text);
		$cmd = sprintf('echo "%s" | maca-analyse -qs morfeusz-nkjp -o %s 2>/dev/null', $text, $format);
		
		$text_tagged = shell_exec($cmd);
		$lines = explode("\n", $text_tagged);
		if ($format == "xces"){
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
		}
	
		return $text_tagged;		
	}	

	static function tagWithMacaWmbt($text){
		global $config;
		$wmbt = $config->wmbt_cmd;
		$text = str_replace('\\', '\\\\', $text);
		$text = str_replace('"', '\"', $text);
		$text = str_replace('$', '\$', $text);
		$text = str_replace('`', '\`', $text);
		$text = str_replace("\n", ' ', $text);
		$text = preg_replace("/( )+/", " ", $text);
		$cmd = sprintf('echo "%s" | maca-analyse -qs morfeusz-nkjp -o xces 2>/dev/null | %s - -o ccl 2>/dev/null', $text, $wmbt);
		ob_start();
		$text_tagged = shell_exec($cmd);
		ob_end_clean();
		$lines = explode("\n", $text_tagged);
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
}

?>
