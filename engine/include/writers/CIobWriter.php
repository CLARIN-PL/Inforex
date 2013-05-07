<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
  
function get_value($array, $key){
	return $array[$key];
} 
 
class IobWriter{

	var $droppedAnns = array();
	var $exportedAnns = array();
	var $channelPriority = array();
	var $currentFileName = null;
	var $currentSentenceNumber = null;
	
	/**
	 * Example channel priority table $name => $value:
	 * 	$channelPriority = array(
	 * 		'organization_nam' => 2,
	 * 		'country_nam' => 1
	 *  );
	 * Channels with greater priority values are chosen as first. If channel is not set
	 * in table, the default value is 0.
	 */
	function __construct($filename, $channelPriority = array()){
		$this->channelPriority = $channelPriority;
		$this->handle = fopen($filename, "w");
		$this->writeLine("-DOCSTART CONFIG FEATURES orth base ctag");		
	}
	
	/**
	 * Close file.
	 */
	function close(){
		fclose($this->handle);
	}
	
	/**
	 * Print stats of written and droped annotations.
	 */
	function printStats(){
		$anns = array_keys(array_merge($this->exportedAnns, $this->droppedAnns));
		sort($anns);
		echo sprintf(" %-22s exported ignored total\n", "Annotation");
		echo str_repeat("—", 50) . "\n";
		foreach ($anns as $ann){
			$exported = isset($this->exportedAnns[$ann]) ? $this->exportedAnns[$ann] : 0;
			$dropped = isset($this->droppedAnns[$ann]) ? $this->droppedAnns[$ann] : 0;
			echo sprintf("- %23s %4d + %4d = %4d\n", 
				$ann, $exported, $dropped, $exported + $dropped);
		}
		echo str_repeat("—", 50) . "\n";
		$exported = array_sum(array_values($this->exportedAnns));
		$dropped = array_sum(array_values($this->droppedAnns));
			echo sprintf("- %23s %4d + %4d = %4d\n", 
				"Total", $exported, $dropped, $exported + $dropped);
	}
	
	/**
	 * Write single line to the opened file.
	 */
	function writeLine($str=""){
		fwrite($this->handle, $str . "\n");
	}
	
	/**
	 * Returns channel pririty.
	 * @return int — prority
	 */
	function getChannelPriority($channel){
		if ( isset($this->channelPriority[$channel]) )
			return $this->channelPriority[$channel];
		else 
			return 0;
	}
	
	/**
	 * Return array of channels with highest priority.
	 * @param $channels — array of channel names
	 * @return array of channel names
	 */
	function getHighestPriority($channels){
		if ( count($channels) == 0)
			return array();
			
		$ordered = array();
		foreach ($channels as $channel){
			$priority = $this->getChannelPriority($channel);
			if ( !isset($ordered[$priority]) )
				$ordered[$priority] = array();
			$ordered[$priority][] = $channel;			
		}
		$max = max(array_keys($ordered));
		return $ordered[$max];
	}
	
	/**
	 * Return annotaton length in counted in tokens.
	 * @param &$tokens — array of tokens
	 * @param $channel — channel for which the annotation
	 *                    length will be counted
	 * @param $startIndex — index of token starting the annotation.
	 * @return int — annotation length
	 */
	function getAnnotationLength(&$tokens, $channel, $startIndex){
		$i = $startIndex + 1;
		$v = $tokens[$startIndex]->channels[$channel];
		while ( $i < count($tokens)
				&& $tokens[$i]->channels[$channel] == $v )
			$i++;
		return $i - $startIndex;
	}
	
	/**
	 * Writes array of ccl documents.
	 * @param &$cclDocuments — array of CclDocument objects.
	 */
	function writeAll(&$cclDocuments){		
		foreach ($cclDocuments as &$ccl){
			if ( ! $ccl instanceof CclDocument )
				throw new Exception("Not CclDocument object");
				 
			$this->writeLine(sprintf("-DOCSTART FILE %s.txt", $ccl->getFileName()));
			$this->currentFileName = $ccl->getFileName();
			$chunks = & $ccl->getChunks();
			for ( $i=0; $i<count($chunks); $i++){
				$sentences = & $chunks[$i]->getSentences();				
				for ($s=0; $s<count($sentences); $s++){
					$this->currentSentenceNumber = $s;
					$this->writeSentence($sentences[$s]);
				}
			}
		}
	}

	/**
	 * Write sentence to given file handle in IOB format.
	 * @param &$sentence — a CclSentence object representing a sentence.
	 */	
	function writeSentence(&$sentence){
		$current = false;
		$begin = false;
		$currentVal = -1;
		$startIndex = -1;
		$tokens = & $sentence->getTokens();

		for ($i=0; $i<count($tokens); $i++)
		{
			$token = & $tokens[$i];
			$channels = & $token->getChannels();	
											
			// Check if current is still in the channel
			if ($current){
				if ($channels[$current] != $currentVal){
					$current = false;
					$currentVal = -1;
					$startIndex = -1;
				}
			}
				
			// If current is not set, find out which one is the current
			if (!$current)				
			{
				// Array of annotations starting at given position group by priority
				$annb = array();
				$prevChannels = $i > 0 ? $tokens[$i-1]->getChannels() : null;
				foreach ($channels as $channel=>$value){
					if (intval($value) > 0 
							&& (!$prevChannels || intval($prevChannels[$channel])!=intval($value))){
						$length = $this->getAnnotationLength($tokens, $channel, $i);
						if ( !isset($annb[$length]) )
							$annb[$length] = array();
						$annb[$length][] = $channel;
					}						
				}
				
				if ( count($annb) > 0){
					$longest = max(array_keys($annb));
					$begins = $this->getHighestPriority($annb[$longest]);
	
					if ( count($begins) == 1 ){
						$current = $begins[0];
						$currentVal = $channels[$current];
						$startIndex = $i;
						if ( isset($this->exportedAnns[$current]))
							$this->exportedAnns[$current]++;
						else
							$this->exportedAnns[$current]=1;
					}
					else{
						echo "ERROR! More begins at position $i!\n";
						echo "file name: " . $this->currentFileName . "\n"; 
						echo "sentence number: " . $this->currentSentenceNumber . "\n";
						var_dump($begins);
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
				// Set token label
				$neStr =  ($startIndex == $i ? "B" : "I") . "-" .strtoupper($current);

				// Check if there are dropped annotations
				$cc = & $tokens[$i]->getChannels();
				$pc = $i>0 ? $tokens[$i-1]->getChannels() : null;
				foreach ($cc as $channel=>$value){
					if ( $channel != $current 
							&& intval($cc[$channel]) > 0 
							&& ( $pc == null || $cc[$channel] != $pc[$channel] )){
						if ( !isset($this->droppedAnns[$channel]) )
							$this->droppedAnns[$channel] = 1;
						else
							$this->droppedAnns[$channel]++;
					}											
				}
			}
											
			$attr = array();
			$attr[] = htmlspecialchars($token->getOrth());
			$attr[] = htmlspecialchars($lexemeDisamb->getBase());
			$attr[] = $lexemeDisamb->getCtag();
			$attr[] = $neStr;
			$this->writeLine(implode(" ", $attr));						
		}		
		$this->writeLine();
	}
	
}

?>