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
					$xml .= "    <orth>{$token->getOrth()}</orth>\n";
					$lexemes = $token->getLexemes();
					foreach ($lexemes as &$lexeme){
						$xml .= $this->disamb ? "    <lex disamb=\"1\">\n" : "    <lex>\n";
						$xml .= "     <base>{$this->base}</base>\n";
						$xml .= "     <ctag>{$this->ctag}</ctag>\n";
						$xml .= "    </lex> \n";						
					}
					$xml .= $token->ns ? "   </tok>\n   <ns/>\n" : "   </tok>\n";
				}
				$xml .= "  </sentence>\n";
			}
			$xml .= " </chunk>\n";
		}
		$xml .= "</chunkList>\n";
		$handle = fopen($filename, "w");
		fwrite($handle, $xml);
		fclose($handle);
		
	}
	
	
	
}

?>