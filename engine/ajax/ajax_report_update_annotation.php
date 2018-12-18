<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_update_annotation extends CPageCorpus {

    function __construct(){
        // ToDo: prawo edycji anotacji CORPUS_ROLE_ANNOTATE_AGREEMENT powinno dotyczyć wyłącznie anotacji o stage=agreement
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE;
        $this->anyCorpusRole[] = CORPUS_ROLE_ANNOTATE_AGREEMENT;
    }
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $db;
		$annotation_id = intval($_POST['annotation_id']);
		$type_id = intval($_POST['type_id']);
		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$text = stripslashes(strval($_POST['text']));
		$lemma = strval($_POST['lemma']);
		$report_id = intval($_POST['report_id']);
		$error = null;

        $row = $db->fetch("SELECT r.content, f.format" .
            " FROM reports r" .
            " JOIN reports_formats f ON (r.format_id=f.id)" .
            " WHERE r.id=?", array($report_id));

        $content = $row['content'];
        $content = normalize_content($content);
        if ( $row['format'] == 'plain' ){
            $content = htmlspecialchars($content);
        }

		$html = new HtmlStr2($content, true);
        $text_revalidate = $html->getText($from, $to);
        $html_revalidate = custom_html_entity_decode($text_revalidate);

        if ( preg_replace("/\n+|\r+|\s+/","",$text) != preg_replace("/\n+|\r+|\s+/","", $html_revalidate) ){
            $error = "Synchronizacja z bazą się nie powiodła &mdash; wystąpiła rozbieżność anotacji. <br/><br/>" .
                "Type: <b>$type</b><br/>" .
                "Position: [<b>$from,$to</b>]<br/>" .
                "Send phrase: <b>'$text'</b><br/>" .
                "Database phrase: <b>'$html_revalidate'</b>";

            throw new Exception($error);
        }
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations', 'id');		

		if ($row = $table_annotations->getRow($annotation_id)){
			
			/* Zapisz dane anotacji */
			$db->update("reports_annotations_optimized",
				array("from"=>$from,"to"=>$to,"text"=>$text,"type_id"=>$type_id),
				array("id"=>$annotation_id));
			
			/* Zapisz lemat anotacji */
			DbReportAnnotationLemma::saveAnnotationLemma($annotation_id, $lemma);
		}else{
			throw new Exception("An error occurred while saving the annotation");
			return;			
		}
		
		return array("from"=>$from, "to"=>$to, "text"=>$html->getText($from, $to), "annotation_id"=>$annotation_id);		
	}
	
}