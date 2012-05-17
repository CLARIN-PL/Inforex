<?
/*
 * Jan Kocoń <janek.kocon@gmail.com>
 */
 
function get_value($array, $key){
	return $array[$key];
} 
 
class IobWriter{
	
	static function write($cclDocuments, $filename){
		$iobStr = "-DOCSTART CONFIG FEATURES orth base ctag\n";		
		foreach ($cclDocuments as $ccl){
			$iobStr .= "-DOCSTART FILE {$ccl->getFileName()}.txt\n";
			$chunks = $ccl->getChunks();
			foreach ($chunks as &$chunk){
				$sentences = $chunk->getSentences();
				
				// Remove nested annotations
				for ($s=0; $s<count($sentences); $s++)
				{
					$current = false;
					$begin = false;
					$currentVal = -1;
					$tokens = $sentences[$s]->getTokens();
					for ($i=0; $i<count($tokens); $i++)
					{
						$token = $tokens[$i];
						$channels = $token->getChannels();	
														
						// Check if current is still in the channel
						if ($current){
							if ($channels[$current] != $currentVal){
								$current = false;
								$currentVal = -1;
							}
						}
							
						// If current is not set, find out which one is the current
						if (!$current)				
						{
							$begins = array();
							$prevChannels = $i > 0 ? $tokens[$i-1]->getChannels() : null;
							foreach ($channels as $name=>$type)
							{
								if ($type && (!$prevChannels || !($prevChannels[$name]==$type)))
									$begins[] = $name;																	
							}

							if (count($begins) == 1){
								$current = $begins[0];//
								$begin = true;
								$currentVal = $channels[$current];
							}
							elseif ( count($begins) > 1 ){
								// Choose the longest one
								$length = 0;
								$current = null;
								foreach ( $begins as $channel ){
									$j = $i + 1;
									while ( $j < count($tokens)
									 && get_value($tokens[$j]->getChannels(),$channel) == $channels[$channel]){
										$j++;
									}
									if ( $j - $i > $length){
										$length = $j - $i;
										$current = $channel;
										$currentVal = $channels[$channel];
										$begin = true;
									}						
								}
							}
						}
						
						
						$lexemes = $token->getLexemes();
						$lexemeDisamb = $lexemes[0];
						foreach ($lexemes as &$lexeme){
							if ($lexeme->getDisamb()){
								$lexemeDisamb = $lexeme;
							}
						}						
						
						$neStr = "O";						
						if ($current){
							$nePrefix = "I";
							if ($begin){
								$nePrefix = "B";
								$begin = false;
							}
							$neStr = "$nePrefix-" . strtoupper($current);
						}
						
						$iobStr .= htmlspecialchars($token->getOrth()) . " " . htmlspecialchars($lexemeDisamb->getBase()) . " " . $lexemeDisamb->getCtag() .  " $neStr\n";
						
						/*
						//FOR TEST ONLY 
						// Reset other than current
						if ($current){
							echo "reseting...\n";
							foreach ($channels as $name=>$type)
								if ( $name != $current ){
									$channels[$name] = 0;
									echo "reset $name\n";
								}
						}
				
						$count = 0;				
						foreach ($channels as $name=>$type){
							$count += $type ? 1 : 0;
							echo "$name $type\n";
						}
			
						if ( $count > 1){
							echo "Aaaa";
							//print_r($token);
							die();
						}
						*/
						
						
						
					}
					$iobStr .= "\n";
					
				}				
				
				
				
				
				
				
				
				
				
				/*foreach ($sentences as &$sentence){
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
						$iobStr .= htmlspecialchars($token->getOrth()) . " " . htmlspecialchars($lexemeDisamb->getBase()) . " " . $lexemeDisamb->getCtag() .  " 0\n";
					}
					$iobStr .= "\n";
				}*/
			}
		}
		$handle = fopen($filename, "w");
		fwrite($handle, $iobStr);
		fclose($handle);		
		//var_dump($cclDocuments);
	}
	
	
	
}

?>