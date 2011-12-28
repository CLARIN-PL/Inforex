<?php
 
 class Page_relations extends CPage{
 	
 	function checkPermission(){
		return hasCorpusRole("read");
	}
	
	function execute()
	{
		global $corpus;
		
		// Parametry stronicowania - liczba relacji na stronę
		$relations_limit = 40;
		
		$relation_types = DbCorpusRelation::getRelationsData($corpus['id']); 
		
		$i=0;
		foreach($relation_types as $rel_t){
			$relation_types[$i++]['types'] = DbCorpusRelation::getRelationsListData($corpus['id'], $rel_t['relation_id']);
		}
		$relation_list = DbCorpusRelation::getRelationList($corpus['id'],$relation_types[0]['types'][0]['relation_type'],$relations_limit);
		
		// Obliczenie ilosci podstron
		$relations_pages = array();
		$from = 0;
		$to = $relation_types[0]['types'][0]['relation_count'];
		for($i=$from; $i <= $to; $i += $relations_limit){
			if($i + $relations_limit < $to){
				$relations_pages[] = array('from'=>$i, 'to'=>$i + $relations_limit);
			}
			else{
				$relations_pages[] = array('from'=>$i, 'to'=>$to);
			}			 
		}		
		$this->set('corpus_id',$corpus['id']);
		$this->set('relation_set_id',$relation_types[0]['relation_id']);
		$this->set('relations_limit',$relations_limit);
		$this->set('relations_type',$relation_types);
		$this->set('relations_list',$relation_list);
		$this->set('relations_pages', $relations_pages);		
	}	
 } 
?>
