<?

class CclWriter{
	
	static function write($ccl, $filename){
		$xml = "<chunkList>\n";
		$chunks = $ccl->getChunks();
		foreach ($chunks as &$chunk){
			$xml .= " <chunk id=\"{$chunk->getId()}\">\n";
			$sentences = $chunk->getSentences();
						
			foreach ($sentences as &$sentence){
				$xml .= "  <sentence id=\"{$sentence->getId()}\">\n";
				$tokens = $sentence->getTokens(); 
				foreach ($tokens as &$token){
					$xml .= "   <tok id=\"{$token->getId()}\">\n";
					$xml .= "    <orth>" . htmlspecialchars($token->getOrth()) . "</orth>\n";
					$lexemes = $token->getLexemes();
					$channels = $token->getChannels();
					foreach ($lexemes as &$lexeme){
						$xml .= $lexeme->getDisamb() ? "    <lex disamb=\"1\">\n" : "    <lex>\n";						
						$xml .= "     <base>" . htmlspecialchars($lexeme->getBase()) . "</base>\n";
						$xml .= "     <ctag>{$lexeme->getCtag()}</ctag>\n";
						$xml .= "    </lex>\n";						
					}
					foreach ($channels as $type=>$number){
						$xml .= "    <ann chan=\"{$type}\">{$number}</ann>\n";
					}
					
					$xml .= $token->ns ? "   </tok>\n   <ns/>\n" : "   </tok>\n";
				}
				$xml .= "  </sentence>\n";
			}
			$xml .= " </chunk>\n";
		}
		$xml .= " <relations>\n";
		$relations = $ccl->getRelations();
		foreach ($relations as &$relation){
			$xml .= "  <rel name=\"".strtolower($relation->getName())."\" set=\"{$relation->getSet()}\">\n";
			$xml .= "   <from sent=\"{$relation->getFromSentence()}\" chan=\"{$relation->getFromType()}\">{$relation->getFromChannel()}</from>\n";
			$xml .= "   <to sent=\"{$relation->getToSentence()}\" chan=\"{$relation->getToType()}\">{$relation->getToChannel()}</to>\n";
			$xml .= "  </rel>\n";			
		}
		$xml .= " </relations>\n";
		$xml .= "</chunkList>\n";
		$handle = fopen($filename, "w");
		fwrite($handle, $xml);
		fclose($handle);
		
	}
	
	
	
}

?>