<?php
 /*
  * Test view corpus=8 and corpus=7
  * insert into report_perspectives value ('relation_statistic', 'Relation Statistic', 82 );
  * insert into corpus_and_report_perspectives value ('relation_statistic', 8, 'loggedin' );
  * insert into corpus_and_report_perspectives value ('relation_statistic', 7, 'loggedin' );
  * 
  */
 
 class PerspectiveRelation_statistic extends CPerspective {
 	
 	function checkPermission(){
		return hasCorpusRole("read");
	}
	
	function execute()
	{
		
		global $corpus;
		$document_id = $this->document[id];
		// Parametry stronicowania - liczba relacji na stronę
		$relations_limit = 40;
		
		$relation_types = DbCorpusRelation::getRelationsData($corpus['id'],$document_id); 
		$relation_list = DbCorpusRelation::getRelationList($corpus['id'],$relation_types[0]['relation_id'],$relations_limit,$document_id);
		
		// Obliczenie ilosci podstron
		$relations_pages = array();
		$from = 0;
		$to = $relation_types[0]['relation_count'];
		for($i=$from; $i <= $to; $i += $relations_limit){
			if($i + $relations_limit < $to){
				$relations_pages[] = array('from'=>$i, 'to'=>$i + $relations_limit);
			}
			else{
				$relations_pages[] = array('from'=>$i, 'to'=>$to);
			}			 
		}		
		
		$this->page->set('corpus_id',$corpus['id']);
		$this->page->set('document_id',$document_id);
		$this->page->set('relation_set_id',$relation_types[0]['relation_id']);
		$this->page->set('relations_limit',$relations_limit);
		$this->page->set('relations_type',$relation_types);
		$this->page->set('relations_list',$relation_list);
		$this->page->set('relations_pages', $relations_pages);		
	}
 } 
?>
