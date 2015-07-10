<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

require_once($config->path_engine . "/pages/lps_stats.php");

/**
 */
class Ajax_lps_get_interp extends CPage {
	
	function checkPermission(){
		if ( hasRole('loggedin') )
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $db;
		
		$interp = strval($_POST['interp']);
		$corpus_id = intval($_POST['corpus_id']);
		
		$params = array($corpus_id);
		
		$rows = $db->fetch_rows("SELECT content, id, title, subcorpus_id FROM reports WHERE corpora = ?", $params);
		$subcorpora = $db->fetch_rows("SELECT * FROM corpus_subcorpora WHERE corpus_id = ?", $params);
		$seqs = array();

		foreach ($subcorpora as $s){
			$headers["sub_".$s['subcorpus_id']] = $s['name'];
		}

		$docs = array();				
	
		foreach ($rows as $row){
			$content = $row['content'];
			$content = strip_tags($content);
			if (preg_match_all('/(\p{P}+)/m', $content, $matches)){
				foreach ($matches[1] as $seq){
					if ( $seq == $interp){
						if ( !isset($docs[$row['id']]) ){
							$docs[$row['id']] = array(	'id' =>$row['id'], 
														'title' =>$row['title'], 
														'subcorpus' =>$headers["sub_".$row['subcorpus_id']],
														'count' => 1);
						}
						else{
							$docs[$row['id']]['count']++;
						}
						continue;
					}
				}
			}
		}
		
		return array( "errors"=>$c->errors, "docs"=>$docs );
	}
	
	
	
}
?>
