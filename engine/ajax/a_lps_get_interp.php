<?php

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
		
		$interp = strval($_POST['interp']);
		
		$rows = db_fetch_rows("SELECT content, id, title, subcorpus_id FROM reports WHERE corpora = 3");
		$subcorpora = db_fetch_rows("SELECT * FROM corpus_subcorpora WHERE corpus_id = 3");
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
						$docs[] = array("id"=>$row['id'], "title"=>$row["title"], "subcorpus"=>$headers["sub_".$row['subcorpus_id']]);
						continue;
					}
				}
			}
		}
		
		$json = array( "success"=>1, "errors"=>$c->errors, "docs"=>$docs );
				
		echo json_encode($json);
	}
	
	
	
}
?>
