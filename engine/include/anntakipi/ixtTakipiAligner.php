<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

require_once("ixtTextAligner.php");
require_once("ixtTakipiAnndoc.php");
require_once("utf8func.php");

class TakipiAligner{
	
	/**
	 * Align inline annotation in a text with 
	 */
	static function align($text, $takipiDocument){
		$content = $text;
		$content = html_entity_decode($content);
        $content = preg_replace('/<(\/)?[pP]>/s', ' ', $content);
        $content = preg_replace('/<br(\/)?>/s', ' ', $content);
        $content = trim($content);
		$aligner = new TextAligner($content);
		
		$ann_begin = null;
		$ann_name = null;
		
		$doc = new TakipiAnndoc();
		
		for($i=0; $i<count($takipiDocument->tokens); $i++){
			$t = $takipiDocument->tokens[$i];
			$aligner->pass_whitespaces();
			if ($aligner->align($t->orth)){
				//echo mb_sprintf("[%3d] %20s %s %s\n", $i, $t->orth, ($aligner->is_begin?"b":"_").($aligner->is_inside?"i":"_").($aligner->is_end?"e":"_"), $aligner->annotation_name);
				if ($aligner->is_begin){
					if ($aligner->is_inside){
						$msg = "Annotation begins inside a token!\n";
						$msg .= "Token index: {$i}\n";
						$msg .= "Token orth : {$t->orth}\n";
						$msg .= "Annotated text:  ".$aligner->getNext(strlen($t->orth)+15);	
						throw new Exception($msg);
					}else{
						$ann_begin = $i;
						$ann_name = $aligner->annotation_name;
					}						
				}
				if ($aligner->is_end){
					if ($aligner->is_inside){
						$msg = "Annotation begins inside a token!\n";
						$msg .= "Token index: {$i}\n";
						$msg .= "Token orth : {$t->orth}\n";
						$msg .= "Ann type   : {$aligner->annotation_name}\n";
						$msg .= "Annotated text:  ".$aligner->getNext(strlen($t->orth)+15);	
						throw new Exception($msg);
					}else{
						//echo sprintf("----- [%3d, %3d] %s\n", $ann_begin, $i, $ann_name);
						$doc->add($ann_begin, $i, $ann_name);
						$ann_name = null;
						$ann_begin = null;
					}
				}
			}else{
				$text = $aligner->getNext(strlen($t->orth)+15);
				$code = "[".ord($text[0]).",".ord($text[1]).",".ord($text[2]).",".ord($text[3])."]";
				throw new Exception("Tekst nie został dopasowany: '{$t->orth}' do tok:[{$aligner->_index}], code:".$code.", text:'".$text."'\n");
			} 
		}		
		
		return $doc;
	}		
}
?>

