<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_delete extends CPageCorpus {
	
	function execute(){
		global $corpus;

		$corpusId = intval($corpus['id']);
		$key = strval($_REQUEST['actionKey']);

		if ( $key == "xc98" ) {
            ob_start();
            DbCorpus::deleteCorpus($corpusId);
            $error_buffer_content = ob_get_contents();
            ob_clean();

            if (strlen($error_buffer_content))
                throw new Exception("Error: " . $error_buffer_content);
            else
                return;
        } else {
            throw new Exception("The action key is missing in the request");
        }
	}	
}