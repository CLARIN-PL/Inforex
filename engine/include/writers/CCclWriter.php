<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class CclWriter{
	public static $CCLREL = 1;
	public static $CCL    = 2;
	public static $REL    = 3;
	
	
	static function write($ccl, $filename, $mode){
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<!DOCTYPE chunkList SYSTEM \"ccl.dtd\">\n";
		
		if ($mode == self::$CCLREL || $mode == self::$CCL){
			$xml .= "<chunkList>\n";
			$chunks = $ccl->getChunks();
			foreach ($chunks as &$chunk){
				$xml .= " <chunk id=\"{$chunk->getId()}\" type=\"{$chunk->getType()}\">\n";
				$sentences = $chunk->getSentences();
				foreach ($sentences as &$sentence){
					$xml .= "  <sentence id=\"{$sentence->getId()}\">\n";
					$tokens = $sentence->getTokens(); 
					foreach ($tokens as &$token){
						$xml .= "   <tok>\n";
						$xml .= "    <orth>" . htmlspecialchars($token->getOrth()) . "</orth>\n";
						$lexemes =  $token->getLexemes();
						$channels = $token->getChannels();
						foreach ($lexemes as &$lexeme){
							$xml .= $lexeme->getDisamb() ? "    <lex disamb=\"1\">" : "    <lex>";						
							$xml .= "<base>" . htmlspecialchars($lexeme->getBase()) . "</base>";
							$xml .= "<ctag>{$lexeme->getCtag()}</ctag>";
							$xml .= "</lex>\n";						
						}
						foreach ($channels as $type=>$number)
							$xml .= "    <ann chan=\"{$type}\">{$number}</ann>\n";
						if ($token->prop){
							/*ORIGINAL
							foreach ($token->prop as $key=>$val)
								$xml .= sprintf("    <prop key=\"%s\">%s</prop>\n", htmlspecialchars($key), htmlspecialchars($val));
							*/
							/*TO DELETE - BEGIN*/
							foreach ($token->prop as $key=>$val){
								if (strpos($val, ';;') !== FALSE){
									$values = explode(";;", $val);
									$key2 = $key . "2";
									$xml .= sprintf("    <prop key=\"%s\">%s</prop>\n", htmlspecialchars($key), htmlspecialchars($values[0]));
									$xml .= sprintf("    <prop key=\"%s\">%s</prop>\n", htmlspecialchars($key2), htmlspecialchars($values[1]));
								}
								else
									$xml .= sprintf("    <prop key=\"%s\">%s</prop>\n", htmlspecialchars($key), htmlspecialchars($val));
							}
							/*TO DELETE - END*/
						}
						$xml .= $token->ns ? "   </tok>\n   <ns/>\n" : "   </tok>\n";
					}
					$xml .= "  </sentence>\n";
				}
				$xml .= " </chunk>\n";
			}
		}
		if ($mode==self::$REL || $mode==self::$CCLREL){
			$spc = $mode == self::$REL ? "" : " ";
			
			if ( $mode==self::$REL || count($ccl->getRelations()) > 0 ){
				$xml .= "$spc<relations>\n";
				$relations = $ccl->getRelations();
				foreach ($relations as &$relation){
					$xml .= "$spc <rel name=\"".mb_strtolower($relation->getName())."\" set=\"{$relation->getSet()}\">\n";
					$xml .= "$spc   <from sent=\"{$relation->getFromSentence()}\" chan=\"{$relation->getFromType()}\">{$relation->getFromChannel()}</from>\n";
					$xml .= "$spc   <to sent=\"{$relation->getToSentence()}\" chan=\"{$relation->getToType()}\">{$relation->getToChannel()}</to>\n";
					$xml .= "$spc  </rel>\n";			
				}
				$xml .= "$spc</relations>\n";
			}
		}
		if ($mode==self::$CCL || $mode==self::$CCLREL)
			$xml .= "</chunkList>\n";		
		$handle = fopen($filename, "w");
		fwrite($handle, $xml);
		fclose($handle);		
	}
	
	
	
}

?>
