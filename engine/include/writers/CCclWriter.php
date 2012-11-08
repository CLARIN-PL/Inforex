<?
/*
 * Jan Kocoń <janek.kocon@gmail.com>
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
				$xml .= " <chunk id=\"{$chunk->getId()}\">\n";
				$sentences = $chunk->getSentences();
				foreach ($sentences as &$sentence){
					$xml .= "  <sentence id=\"{$sentence->getId()}\">\n";
					$tokens = $sentence->getTokens(); 
					foreach ($tokens as &$token){
						$xml .= "   <tok>\n";
						$xml .= "    <orth>" . htmlspecialchars($token->getOrth()) . "</orth>\n";
						$lexemes = $token->getLexemes();
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
							foreach ($token->prop as $key=>$val)
								$xml .= sprintf("    <prop key=\"%s\">%s</prop>\n", $key, $val);
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
			
			$xml .= "$spc<relations>\n";
			$relations = $ccl->getRelations();
			foreach ($relations as &$relation){
				$xml .= "$spc <rel name=\"".strtolower($relation->getName())."\" set=\"{$relation->getSet()}\">\n";
				$xml .= "$spc   <from sent=\"{$relation->getFromSentence()}\" chan=\"{$relation->getFromType()}\">{$relation->getFromChannel()}</from>\n";
				$xml .= "$spc   <to sent=\"{$relation->getToSentence()}\" chan=\"{$relation->getToType()}\">{$relation->getToChannel()}</to>\n";
				$xml .= "$spc  </rel>\n";			
			}
			$xml .= "$spc</relations>\n";
		}
		if ($mode==self::$CCL || $mode==self::$CCLREL)
			$xml .= "</chunkList>\n";		
		/*if ($mode==self::$REL)
			$filename = "$filename.rel.xml";		
		else $filename = "$filename.xml";*/
		$handle = fopen($filename, "w");
		fwrite($handle, $xml);
		fclose($handle);		
	}
	
	
	
}

?>
