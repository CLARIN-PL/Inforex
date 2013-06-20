<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
  
/**
 * Contains set of functions to modify the ccl object structure.
 */
class CclAnnotationFlattern{

	var $channelPriority = array();

	function __construct($channelPriority = array()){
		$this->channelPriority = $channelPriority;
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
	 * Removes nested annotations in a document.
	 * @param $ccl CclDocument — ccl document to modify,
	 * @param $prorities array(String=>int) — array with annotation priorities,
	 * @param $contains String — filter annotations to be processed.
	 */
	function flattenDocument(&$ccl){
		
		foreach ($ccl->chunks as &$chunk)
			foreach ($chunk->sentences as &$sentence)
				CclAnnotationFlattern::flattenSentence($sentence);
		
	}

	/**
	 * Removes nested annotations in a sentence.
	 * @param $sentence CclSentence — ccl sentence to flatten,
	 * @param $prorities array(String=>int) — array with annotation priorities,
	 * @param $contains String — filter annotations to be processed.
	 */
	function flattenSentence(&$sentence){

		$current = false;
		$currentVal = -1;
		$tokens = &$sentence->tokens;
		
		for ($i=0; $i<count($tokens); $i++){
			
			$token = & $tokens[$i];
			$channels = & $token->channels;	
											
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
					}
					else{
						echo "ERROR! More begins at position $i!\n";
						echo "file name: " . $this->currentFileName . "\n"; 
						echo "sentence number: " . $this->currentSentenceNumber . "\n";
						var_dump($begins);
					}
				}
			}
															
			if ($current){
				foreach ($channels as $name=>$value){
					if ( $name != $current )
						$channels[$name] = 0;
				}
			}		
		}
		$this->removeEmptyChannels($sentence);
	}
	
	/**
	 * @param $sentence CclSentence — a sentence structure
	 */
	function removeEmptyChannels(&$sentence){
		if ( count($sentence->tokens) == 0 || count($sentence->tokens[0]->channels) == 0)
			return;
			
		$channels = array_keys($sentence->tokens[0]->channels);
		
		foreach ($channels as $channel){
			$count = 0;
			for ($i = 0; $i<count($sentence->tokens); $i++){
				if ( $sentence->tokens[$i]->channels[$channel] != 0)
					$count++;
			}
			if ( $count == 0){
				for ($i = 0; $i<count($sentence->tokens); $i++)
					unset($sentence->tokens[$i]->channels[$channel]);
			}			
		}
	}
	
}

?>