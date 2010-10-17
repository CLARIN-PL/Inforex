<?php
/* 
 * Created on 2010-01-13
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */

require_once("ixtTextAligner.php");
require_once("ixtTakipiAnndoc.php");
require_once("utf8func.php");

class TakipiAligner{
	
	/**
	 * Align inline annotation with a TakipiDocument. 
	 */
	static function align($text, TakipiDocument &$takipiDocument){
		$content = $text;
		$content = html_entity_decode($content);
        $content = preg_replace('/<(\/)?[pP]>/s', ' ', $content);
        $content = preg_replace('/<br(\/)?>/s', ' ', $content);
        $content = trim($content);
		$aligner = new TextAligner($content);
		
		$ann_begin = null;
		$ann_name = null;
		
		$ann_stack = array();
		
		$tokens = $takipiDocument->getTokens();
		
		for($i=0; $i<count($tokens); $i++){
			$t = $tokens[$i];
			$aligner->pass_whitespaces();
			if ($aligner->align($t->orth)){
				if ($aligner->is_begin){
					if ($aligner->is_inside){
						$msg = "Annotation begins inside a token!\n";
						$msg .= "Token index: {$i}\n";
						$msg .= "Token orth : {$t->orth}\n";
						$msg .= "Annotated text:  ".$aligner->getNext(strlen($t->orth)+15);	
						throw new Exception($msg);
					}else{
						$ann_begin = $i;
						foreach ($aligner->annotation_started as $ann_name)
							array_push($ann_stack, array($ann_name, $ann_begin));
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
						try{
							foreach ($aligner->annotation_ended as $ended)
							{
								list($ann_name, $ann_begin) = array_pop($ann_stack);
								$takipiDocument->addAnnotation($ann_name, $ann_begin, $i);
							}
						}catch(Exception $ex){
							print "! " . $ex->getMessage() . "\n";
						}
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
	}		
}
?>

