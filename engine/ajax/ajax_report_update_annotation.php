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
		global $mdb2, $user, $db;
		$annotation_id = intval($_POST['annotation_id']);
		// ToDo: !! Annotation types should be passed by id, not name.
		$type = strval($_POST['type']);
		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$text = stripslashes(strval($_POST['text']));
		$lemma = strval($_POST['lemma']);
		$report_id = intval($_POST['report_id']);
		$attributes = strval($_POST['attributes']);
		$shared_attributes = $_POST['shared_attributes'];
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
                "Typ: <b>$type</b><br/>" .
                "Pozycja: [<b>$from,$to</b>]<br/>" .
                "Przesłana jednostka: <b>'$text'</b><br/>" .
                "Jednostka z bazy: <b>'$html_revalidate'</b>";

            throw new Exception($error);
        }
		
		$table_annotations = $mdb2->tableBrowserFactory('reports_annotations', 'id');		

		if ($row = $table_annotations->getRow($annotation_id)){
			
			/* Zapisz dane anotacji */
			$db->update("reports_annotations_optimized",
				array("from"=>$from,"to"=>$to,"text"=>$text,"type_id"=>DbAnnotation::getIdByName($type)),
				array("id"=>$annotation_id));
			
			/* Zapisz lemat anotacji */
			DbReportAnnotationLemma::saveAnnotationLemma($annotation_id, $lemma);

            /***
             * ToDo: The bolow code needs na update

			// Get and iterate through list of annotation attributes
			$annotation_attributes = db_fetch_rows("SELECT * FROM annotation_types_attributes WHERE annotation_type = '$type'");
			$annotation_attributes_names = array();
			foreach ($annotation_attributes as $a)
				$annotation_attributes_names[$a['name']] = $a['id'];
			
			$attributes = explode("\n", $attributes);
			foreach ($attributes as $a){
				list($name, $value) = explode("=", $a);
				if (isset($annotation_attributes_names[$name]))
					db_replace("reports_annotations_attributes",
						array(
							"value"=>$value,
							"annotation_id"=>$annotation_id, 
							"annotation_attribute_id"=>$annotation_attributes_names[$name],
							"user_id"=>$user['user_id'])
							);
			}
			
			// Update shared attributes
			if (is_array($shared_attributes)){
				$empty_shared_attributes = array($annotation_id);
				$empty_pattern = array();
				$shared_attributes_values = array();
				$shared_attributes_pattern = array();
				foreach ($shared_attributes as $shared_attribute_id => $shared_attribute_value){
					if (empty($shared_attribute_value)){
						array_push(
								$empty_shared_attributes, 
								$shared_attribute_id);
						array_push(
								$empty_pattern,
								"?");
					}
					else {
						array_push(
								$shared_attributes_values, 
								$annotation_id, 
								$shared_attribute_id, 
								$shared_attribute_value, 
								$user['user_id']);
						array_push(
								$shared_attributes_pattern,
								"(?, ?, ?, ?)");
					}
				}
				$empty_sql =
					"DELETE FROM reports_annotations_shared_attributes " .
					"WHERE annotation_id = ? " .
					"AND shared_attribute_id " . 
					"IN (" . implode(",", $empty_pattern) . ")";
				
				$values_sql =
					"INSERT INTO reports_annotations_shared_attributes " .
					"(`annotation_id`, `shared_attribute_id`, `value`, `user_id`) " .
					"VALUES " . implode(",", $shared_attributes_pattern) . " " .
					"ON DUPLICATE KEY UPDATE " . 
					"value=VALUES(`value`), user_id = VALUES(`user_id`);";
				
				if (count($empty_shared_attributes) > 1)
					$db->execute($empty_sql, $empty_shared_attributes);
				if (count($shared_attributes_values) > 0)
					$db->execute($values_sql, $shared_attributes_values);
			}
             */
		}else{
			throw new Exception("Wystąpił nieznany problem z zapisem anotacji.");
			return;			
		}
		
		return array("from"=>$from, "to"=>$to, "text"=>$html->getText($from, $to), "annotation_id"=>$annotation_id);		
	}
	
}