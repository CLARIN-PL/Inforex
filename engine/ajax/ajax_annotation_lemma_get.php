<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class Ajax_annotation_lemma_get extends CPage {
	var $isSecure = true;
	
 	function checkPermission(){
 		if (hasRole(USER_ROLE_ADMIN) || hasPerspectiveAccess("annotation_lemma") || hasCorpusRole(CORPUS_ROLE_ANNOTATE) || hasCorpusRole(CORPUS_ROLE_ANNOTATE_AGREEMENT) )
 			return true;
 		else
 			return "Brak prawa do edycji.";
 	}
	
	function execute(){
		$annotation_id = intval($_POST['annotation_id']);
		$lemma = strval(DbReportAnnotationLemma::getAnnotationLemma($annotation_id));		
		return array("lemma" => $lemma);
	}
}