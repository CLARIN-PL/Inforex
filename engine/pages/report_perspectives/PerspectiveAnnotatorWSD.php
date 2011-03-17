<?php

class PerspectiveAnnotatorWSD extends CPerspective {
	
	function execute()
	{
		global $corpus;
		$corpus_id = $corpus['id'];
		
		$word = $_GET['wsd_word'];
		$rid  = intval($_GET['id']);
		$annotation_id = intval($_GET['aid']); 
		
		$content = $this->load_document_content();
		
		$this->page->set("wsd_word", $word);
		$this->page->set("wsd_edit", $annotation_id);
		$this->page->set("content_inline", $content);
		$this->page->set("words", $this->load_wsd_words());

		$sql_annotation = "SELECT * FROM reports_annotations WHERE id = ?";
		$ann = db_fetch($sql_annotation, array($annotation_id));
		$annotation_from = $ann['from'];

		list($next_word_not_report_id, $next_word_not_annotation_id) = $this->load_next_not_set($word, $corpus_id, $rid, $annotation_from); 		
		$this->page->set("next_word_not_report_id", $next_word_not_report_id);
		$this->page->set("next_word_not_annotation_id", $next_word_not_annotation_id);

		list($prev_word_not_report_id, $prev_word_not_annotation_id) = $this->load_prev_not_set($word, $corpus_id, $rid, $annotation_from); 		
		$this->page->set("prev_word_not_report_id", $prev_word_not_report_id);
		$this->page->set("prev_word_not_annotation_id", $prev_word_not_annotation_id);
		
		list($next_word_report_id, $next_word_annotation_id) = $this->load_next_word($word, $corpus_id, $rid, $annotation_from); 		
		$this->page->set("next_word_report_id", $next_word_report_id);
		$this->page->set("next_word_annotation_id", $next_word_annotation_id);
		
		list($prev_word_report_id, $prev_word_annotation_id) = $this->load_prev_word($word, $corpus_id, $rid, $annotation_from); 		
		$this->page->set("prev_word_report_id", $prev_word_report_id);
		$this->page->set("prev_word_annotation_id", $prev_word_annotation_id);
	}


	/**
	 * Odczytuje z bazy listę słów dla WSD. Zwraca tablicę identyfikator=>opis_słowa
	 */
	function load_wsd_words(){
		$sql = "SELECT * FROM annotation_types WHERE group_id = 2";
		
		$sql_first_ann = "SELECT an.report_id, an.id" .
				" FROM reports_annotations an " .
				" WHERE an.type = ? " .
				" ORDER BY an.report_id ASC, an.from ASC";
				
		$rows = db_fetch_rows($sql);
		$words = array();
		foreach ($rows as $r){
			$r['word'] = substr($r['name'], 4);
			
			// Znajdź pierwsze wystąpienie anotacji
			$row = db_fetch($sql_first_ann, array($r['name']));
			list($first_report_id, $first_annotation_id) = is_array($row) ? array_values($row) : array(null, null);
			$r['report_id'] = $first_report_id;
			$r['annotation_id'] =  $first_annotation_id;
						
			$words[$r['name']] = $r;						
		}
		
		return $words;
	}
	
	/**
	 * 
	 */
	function load_document_content(){
		// Wstaw anotacje do treści dokumentu
		$sql = "SELECT id, type, `from`, `to`, `to`-`from` AS len, group_id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types t ON (an.type=t.name)" .
				" WHERE report_id = {$this->document['id']}" .
				" AND t.group_id = 2" .
				" ORDER BY `from` ASC, `level` DESC";
		$anns = db_fetch_rows($sql);

		try{
			$htmlStr = new HtmlStr(html_entity_decode($this->document['content'], ENT_COMPAT, "UTF-8"));
			foreach ($anns as $ann){
				$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s:%d>", $ann['id'], $ann['type'], $ann['group_id']), $ann['to']+1, "</an>");
				//$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
			}
		}catch (Exception $ex){
			custom_exception_handler($ex);
		}
		
		return Reformat::xmlToHtml($htmlStr->getContent());
	}

	/**
	 * Znajduje następne wystąpienie danego słowa w dokumencie.
	 */
	function load_next_word($word_wsd, $corpus_id, $report_id, $annotation_from){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" WHERE at.group_id = 2" .
				"  AND r.corpora = ?" .
				"  AND ata.name = 'sense'" .
				"  AND ( ( r.id > ? ) OR ( r.id = ? AND an.from > ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id, an.from ASC";
		$row = db_fetch($sql, array($corpus_id, $report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}
	
	function load_prev_word($word_wsd, $corpus_id, $report_id, $annotation_from){		
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" WHERE at.group_id = 2" .
				"  AND r.corpora = ?" .
				"  AND ata.name = 'sense'" .
				"  AND ( ( r.id < ? ) OR ( r.id = ? AND an.from < ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id DESC, an.from DESC";
		$row = db_fetch($sql, array($corpus_id, $report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}	
	
	function load_next_not_set($word_wsd, $corpus_id, $report_id, $annotation_from){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = 2" .
				"  AND r.corpora = ?" .
				"  AND ata.name = 'sense'" .
				"  AND raa.value IS NULL" .
				"  AND ( ( r.id > ? ) OR ( r.id = ? AND an.from > ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id, an.from ASC";
		$row = db_fetch($sql, array($corpus_id, $report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}

	function load_prev_not_set($word_wsd, $corpus_id, $report_id, $annotation_from){
		$sql = "SELECT r.id as report_id, an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = 2" .
				"  AND r.corpora = ?" .
				"  AND ata.name = 'sense'" .
				"  AND raa.value IS NULL" .
				"  AND ( ( r.id < ? ) OR ( r.id = ? AND an.from < ?) )" .
				"  AND an.type = ?" .
				" ORDER BY r.id DESC, an.from DESC";
		$row = db_fetch($sql, array($corpus_id, $report_id, $report_id, $annotation_from, $word_wsd));
		return is_array($row) ? array_values($row) : array(null, null);
	}
	
	/**
	 * Odczytuje identyfikator pierwszej jednostki do edycji.
	 * W pierwszej kolejności wybierane jest nieopisane słowo podanego typu.
	 * Jeżeli typ słowa nie jest określony, to pobierane jest pierwsze nieopisane słowo.
	 */
	function load_wsd_edit($report_id, $wsd_word, $annotation_id){
		$sql = "SELECT an.id" .
				" FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type=at.name)" .
				" JOIN reports r ON (r.id=an.report_id)" .
				" JOIN annotation_types_attributes ata ON (ata.annotation_type = an.type)" .
				" LEFT JOIN reports_annotations_attributes raa ON (raa.annotation_id = an.id AND raa.annotation_attribute_id = ata.id)" .
				" WHERE at.group_id = 2" .
				"  AND ata.name = 'sense'" .
				"  AND r.id = ?" .
				( $wsd_word ? " AND an.type = '" . mysql_real_escape_string($wsd_word) . "'" : "" ).
				" ORDER BY an.from ASC";
		return db_fetch_one($sql, array($report_id));		
	}
	
}

?>
