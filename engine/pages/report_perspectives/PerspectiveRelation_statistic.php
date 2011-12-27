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
		
		// Parametry stronicowania - liczba relacji na stronę
		$relations_limit = 40;
		
		$relation_types = $this->get_relations_data(); 
		$relation_list = $this->get_relation_list($relation_types,$relations_limit);
		
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
		$this->page->set('corpus_id',$this->get_corpus_id());
		$this->page->set('relation_set_id',$relation_types[0]['relation_id']);
		$this->page->set('relations_limit',$relations_limit);
		$this->page->set('relations_type',$relation_types);
		$this->page->set('relations_list',$relation_list);
		$this->page->set('relations_pages', $relations_pages);		
	}
	
	
	// Funkcja pobiera z bazy danych nazwy typów relacji i ich ilości dla danego korpusu 
	function get_relations_data(){
  		global $db;
  		$cid = $this->get_corpus_id();
		//SELECT rs.name AS relation_name, count(rel.id) AS relation_count, rs.relation_set_id AS relation_id FROM relation_sets rs LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) LEFT JOIN reports_annotations an ON (rel.source_id=an.id) LEFT JOIN reports rep ON (rep.id=an.report_id) WHERE rep.corpora=? GROUP BY rs.relation_set_id;
		$sql = "SELECT rs.name AS relation_name, count(rel.id) AS relation_count, rs.relation_set_id AS relation_id " .
				"FROM relation_sets rs " .
				"LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) " .
				"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
				"LEFT JOIN reports_annotations an ON (rel.source_id=an.id) " .
				"LEFT JOIN reports rep ON (rep.id=an.report_id) " .
				"WHERE rep.corpora=? " .
				"GROUP BY rs.relation_set_id";							
		return $db->fetch_rows($sql, array($cid));
	}
	
	// Funkcja pobiera z bazy danych listę relacji dla pierwszego typu w relation_types - pobiera tylko relations_limit wpisów
	// dane wyjściowe postaci: document_id, subcorpus_name, source_text, source_type, target_text, target_type;   
	function get_relation_list($relation_types, $relations_limit){
		global $db;
  		$cid = $this->get_corpus_id();
		//SELECT rep.id AS document_id, cor.name AS subcorpus_name, an_sou.text AS source_text, an_sou.type AS source_type, an_tar.text AS target_text, an_tar.type AS target_type FROM relation_sets rs LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) LEFT JOIN reports_annotations an_sou ON (rel.source_id=an_sou.id) LEFT JOIN reports_annotations an_tar ON (rel.target_id=an_tar.id) LEFT JOIN reports rep ON (rep.id=an_sou.report_id) LEFT JOIN corpora cor ON (cor.id=rep.subcorpus_id) WHERE rep.corpora=? AND rs.relation_set_id=? LIMIT 0, ? ;
  		$sql = "SELECT rep.id AS document_id, cor.name AS subcorpus_name, an_sou.text AS source_text, an_sou.type AS source_type, an_tar.text AS target_text, an_tar.type AS target_type " .
  				"FROM relation_sets rs " .
  				"LEFT JOIN relation_types rty ON (rs.relation_set_id=rty.relation_set_id) " .
  				"LEFT JOIN relations rel ON (rel.relation_type_id=rty.id) " .
  				"LEFT JOIN reports_annotations an_sou ON (rel.source_id=an_sou.id) " .
  				"LEFT JOIN reports_annotations an_tar ON (rel.target_id=an_tar.id) " .
  				"LEFT JOIN reports rep ON (rep.id=an_sou.report_id) " .
  				"LEFT JOIN corpora cor ON (cor.id=rep.subcorpus_id) " .
  				"WHERE rep.corpora=? " .
  				"AND rs.relation_set_id=? " .
  				"LIMIT 0, ? ;";	
  		return $db->fetch_rows($sql, array($cid, $relation_types[0][relation_id], $relations_limit));
	}
	
	// Funkcja pobiera ze zmiennej globalnej id aktualnego korpusu
	function get_corpus_id(){
		global $corpus;
		return $corpus['id'];
	}
 } 
?>
