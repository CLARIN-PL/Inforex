<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_annotation_frequency_subcorpora extends CPage{

	public function execute(){
		global $corpus;
		
		$texts = $_POST['texts'];
		$annotation_stage = strval($_POST['annotation_stage']);
		$corpus_id = $_POST['corpus_id'];

		$words = DbCorpusStats::getAnnotationFrequencesPerSubcorpus($corpus_id, $texts, $annotation_stage);
		$sizes = $this->getSubcorporaSizes($corpus_id);
		$total = 0;
		foreach ($sizes as $subcorpus_id=>$size){
			$total += $size;
		}
		foreach ($words as $index=>$word){
		//	$words[$index]['c'] = (float)$word/(float)$sizes[$word['subcorpus_id']];
		}
		return $words;
	}


	public function getSubcorporaSizes($corpus_id){
		global $db;	
		$sql = "SELECT r.subcorpus_id, COUNT(t.token_id) AS tokens" .
                                " FROM reports r" .
                                " JOIN tokens t ON (t.report_id = r.id)" .
                                " WHERE r.corpora=?" .
                                " GROUP BY r.subcorpus_id ";
		$sizes = array();
                foreach ($db->fetch_rows($sql, array($corpus_id)) as $row){
                        $sizes[$row['subcorpus_id']] = $row['tokens'];
                }
		return $sizes;
	}
}
