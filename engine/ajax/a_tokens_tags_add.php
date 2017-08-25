<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_tokens_tags_add extends CPage{

	public function execute(){
		global $corpus, $user;

		$tag_data = $_POST;



//        echo($tag_data);
//		$ctag = null;
//		$words = DbCorpusStats::getWordsFrequencesPerSubcorpus($corpus_id, $ctag, true, $base_ids);
//        return array('ret'=>$tag_data, 'user'=>$user);
        sleep(1);
        return array('success'=>true);
	}
}
