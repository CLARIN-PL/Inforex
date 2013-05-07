<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PlWordnet{
	
	var $lemmas = array();
	var $lem2lex = array();
	var $hyperonyms = array();
	var $syn2lex = array();
	var $lex2syn = array();
	
	var $trace = array();
	
	function __construct(){
		
	}
	
	function loadFromDb($db){
	
		$lemmas = $db->fetch_rows("SELECT ID, lemma, domain FROM lexicalunit");
		foreach ($lemmas as $l){
			$this->lemmas[$l['id']] = $l;
			$this->lem2lex[$l['lemma']][] = $l['id'];
		}
	
		$hyph = $db->fetch_rows("SELECT PARENT_ID, CHILD_ID FROM synsetrelation WHERE REL_ID=33");	
		foreach ($hyph as $h){
			$this->hyperonyms[$h['parent_id']][] = $h['child_id'];
		}
		
		$synlem = $db->fetch_rows("SELECT LEX_ID, SYN_ID FROM unitandsynset");
		foreach ($synlem as $l){
			$this->syn2lex[$l['syn_id']][] = $l['lex_id'];
			$this->lex2syn[$l['lex_id']] = $l['syn_id'];
		}
	
		print_r($this->lex_syn);	
	}
	
	function getAllHyperonyms($lemma){
		$this->trace = array();
		$lemmas = array();
		$ids = $this->lem2lex[$lemma];
		if ( is_array($ids) ){
			foreach ($ids as $id){
				$synid = $this->lex2syn[$id];
				$lemmas = array_merge($lemmas, $this->getAllHyperonymsBySynId($synid));
			}
		}
		
		$lemmas_unique = array();
		foreach ($lemmas as $l){
			$lemmas_unique[$l] = 1;
		}
		
		return array_keys($lemmas_unique);
	} 

	function getAllHyperonymsBySynId($id){
		
		if ( isset($this->trace[$id]))
			return array();
		else
			$this->trace[$id] = 1;
		
		$lemmas = array();
		if ( isset($this->hyperonyms[$id]))
			foreach ( $this->hyperonyms[$id] as $hsid){
				foreach ( $this->syn2lex[$hsid] as $hid)
					$lemmas[] = $this->lemmas[$hid]['lemma'];
				$lemmas = array_merge($lemmas, $this->getAllHyperonymsBySynId($hsid));
			}			
		return $lemmas;
	}

	function getAllHyperonymSynsets($lemma){
		$this->trace = array();
		$lemmas = array();
		$ids = $this->lem2lex[$lemma];
		if ( is_array($ids) )
			foreach ($ids as $id){
				$sid = $this->lex2syn[$id];				
				$synid = sprintf("syn_%d_%s", $sid, $this->lemmas[$this->syn2lex[$sid][0]]['lemma'] );
				$lemmas = array_merge(array($synid), $this->getAllHyperonymSynsetsBySynId($sid));
			}
		
		$lemmas_unique = array();
		foreach ($lemmas as $l)
			$lemmas_unique[$l] = 1;
		
		return array_keys($lemmas_unique);
	} 

	function getAllHyperonymSynsetsBySynId($id){
		
		if ( isset($this->trace[$id]))
			return array();
		else
			$this->trace[$id] = 1;
		
		$lemmas = array();
		if ( isset($this->hyperonyms[$id]))
			foreach ( $this->hyperonyms[$id] as $hsid){				
				$synid = sprintf("syn_%d_%s", $hsid, $this->lemmas[$this->syn2lex[$hsid][0]]['lemma'] ); 													
				$lemmas = array_merge(array($synid), $this->getAllHyperonymSynsetsBySynId($hsid));
			}			
		return $lemmas;
	}
	
}

?>