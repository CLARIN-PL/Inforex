<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
mb_internal_encoding("utf-8");

require_once("{$config->path_engine}/pages/ner.php");

/**
 */
class Ajax_events_process extends CPage {
	
	var $isSecure = false;
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus, $config;
	
		$text = strval($_POST['text']);

		$tagged = Page_ner::tag($text);		
		$sentences = Ajax_events_process::xml2iob($tagged);

		$struct = "<table class='tablesorter' cellspacing='1'><thead><tr><th>Typ</th><th>Treść</th></tr></thead><tbody>";
		$htmlStr = new HtmlStr($text, true);
		$offset = 0;
		
		foreach ($sentences as $tokens){
		
			$chunking = $this->chunk($tokens);
			$chunkingC = $this->chunkingByToken2Chars($chunking, $tokens);

			foreach ($chunkingC as $c){
				try{
					$htmlStr->insertTag($offset + $c[0], sprintf("<span class='%s' title='%s'>", strtolower($c[2]), strtolower($c[2])), $offset + $c[1]+1, "</span>");
					$struct .= sprintf("<tr><td>%s</td><td>%s</td></tr>", $c[2], $htmlStr->getText($offset + $c[0], $offset + $c[1]));
				}
				catch(Exception $ex){}
			}
			
			foreach ($tokens as $t)
				$offset += mb_strlen($t[0]);
		}
		
		$struct .= "</tbody></table>";
		$html = $htmlStr->getContent();
		$html = str_replace("\n", "<br/>", $html);
		
		return array("chunking"=> $chunking, "chunkingc" => $chunkingC, "html" => $html, "struct" => $struct );
	}
	
	function xml2iob($xml){
		$takipiDocument = TakipiReader::createDocumentFromText("<doc>".$xml."</doc>");
		
		$sentences = array();
		foreach ($takipiDocument->sentences as $sentence){
			$tokens = array();
			foreach ($sentence->tokens as $token)
				$tokens[] = array(custom_html_entity_decode($token->orth), $token->getDisamb()->base, $token->getDisamb()->ctag);
			//$tokens[] = array(html_entity_decode($token->orth), $token->getDisamb()->base, $token->getDisamb()->ctag);
			$sentences[] = $tokens;
		}		
		return $sentences;			
	}
	
	function chunk($tokens){
		global $config;
		
		$tokens_joined = array();
		foreach ($tokens as $token)
			$tokens_joined[] = implode(" ", $token);
		$str = implode("  ", $tokens_joined);
		$str = str_replace("'", "\\'", $str);
		
		//$str = "Pani pani subst:sg:nom:f  Kamila kamil subst:sg:gen:m1  Nowa nowy adj:sg:nom:f:pos  ma mieć fin:sg:ter:imperf  kota kot subst:sg:nom:f";
		$path = $config->path_liner;
		//$model = "{$path}/models/crf_model_5nam_orth-base-ctag.bin";
		$model = "{$path}/models/crf_model_nwza_orth-base-ctag.bin";
		$cmd = sprintf("LANG=en_US.utf-8; java -Djava.library.path={$path}/production/lib -jar {$path}/production/liner.jar tag '%s' -chunker crfpp-load:%s", $str, $model);
		
		ob_start();
		$cmd_result = shell_exec($cmd);
		$r = ob_get_clean();

		$chunking = array();
		preg_match_all("/([0-9]+),([0-9]+),([A-Z_]*)/", $cmd_result, $matches, PREG_SET_ORDER);
		foreach ($matches as $m){
			$chunking[] = array($m[1], $m[2], $m[3]);
		}

		return $chunking;	
	}
	
	function chunkingByToken2Chars($chunking, $tokens){
		
		$cseq = "";
		foreach ($tokens as $token)
			$cseq .= $token[0] . " ";
		fb($cseq);
		$chunkingChar = array();
		foreach ($chunking as $chunk){
			$from = $chunk[0] - substr_count(mb_substr($cseq, 0, $chunk[0]), ' ');
			$to = $chunk[1] - substr_count(mb_substr($cseq, 0, $chunk[1]), ' ');
			$chunkingChar[] = array($from, $to, $chunk[2]);
		}
		
		return $chunkingChar;
	}
	
}
?>
