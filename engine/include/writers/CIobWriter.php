<?
/*
 * Jan Kocoń <janek.kocon@gmail.com>
 */
class IobWriter{
	
	static function write($cclDocuments, $filename){
		$iobStr = "-DOCSTART CONFIG FEATURES orth base ctag\n";		
		foreach ($cclDocuments as $ccl){
			$iobStr .= "-DOCSTART FILE {$ccl->getFileName()}.txt\n";
			$chunks = $ccl->getChunks();
			foreach ($chunks as &$chunk){
				$sentences = $chunk->getSentences();
				foreach ($sentences as &$sentence){
					$tokens = $sentence->getTokens(); 
					foreach ($tokens as &$token){
						//echo $token->getOrth();
						$lexemes = $token->getLexemes();
						$channels = $token->getChannels();
						$lexemeDisamb = $lexemes[0];
						foreach ($lexemes as &$lexeme){
							if ($lexeme->getDisamb()){
								$lexemeDisamb = $lexeme;
							}
						}
						/*foreach ($channels as $type=>$number)
							$iobStr .= "    <ann chan=\"{$type}\">{$number}</ann>\n";
						if ($token->prop)
							$iobStr .= "    <prop key=\"sense:sense_id\">{$token->prop}</prop>\n";
						$iobStr .= $token->ns ? "   </tok>\n   <ns/>\n" : "   </tok>\n";*/
						$iobStr .= htmlspecialchars($token->getOrth()) . " " . htmlspecialchars($lexemeDisamb->getBase()) . " " . $lexemeDisamb->getCtag() .  " 0\n";
					}
					$iobStr .= "\n";
				}
			}
		}
		$handle = fopen($filename, "w");
		fwrite($handle, $iobStr);
		fclose($handle);		
		//var_dump($cclDocuments);
	}
	
	
	
}

?>