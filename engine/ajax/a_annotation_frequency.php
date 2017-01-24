<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_frequency extends CPage{

	public function execute(){
		global $corpus;

		$sortName		= $_POST['sortname'];
		$sortOrder		= $_POST['sortorder'];
		$pageElements	= intval($_POST['rp']);  // Liczba elementów na stronę
		$page			= intval($_POST['page']); // Numer strony
		$phrases		= $_POST['phrase'];
		$annotation_type_id = intval($_POST['annotation_type_id']);
		
		$phrases = explode(",", $phrases);
		array_walk($phrases, trim);
		$phrases = array_filter($phrases);		
		if ( count($phrases) == 0 ) $phrases = null;
		
		$subcorpus_id = $_POST['subcorpus_id'];		
		$corpus_id = $_POST['corpus'];
		
		$rows = DbCorpusStats::getAnnotationFrequency($corpus_id, $subcorpus_id, $annotation_type_id, $phrases, null, ($page-1)*$pageElements, $pageElements);
		$total = DbCorpusStats::getUniqueAnnotationCount($corpus_id, $subcorpus_id, $annotation_type_id, $phrases);
				
		// UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
		echo json_encode(array('page' => $page, 'total' => $total, 'rows' => $rows, 'post' => $_POST));
		die;
	}
}