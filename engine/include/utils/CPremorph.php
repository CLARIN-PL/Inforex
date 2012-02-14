<?php
/*
 * Created on 2012-02-13
 * 
 */
class Premorph{
	
	static function set_sentence_tag($report_id){
		global $db;
		
		$comment = "Dodanie znaczników <sentence>";
		$report = new CReport($report_id);
		$content_before = $report->content;
		$sql = "SELECT * FROM tokens t WHERE t.report_id=" . $report_id . " AND t.eos=1" ;
		$tokens = $db->fetch_rows($sql);
		
		$remove_sentence_tag = preg_replace("[</sentence>]","", $content_before);
		$remove_sentence_tag = preg_replace("[<sentence>]","", $remove_sentence_tag);
		
		$htmlStr =  new HtmlStr($remove_sentence_tag, true);
		$tag_from = 0;
		foreach($tokens as $token){
			try{
				$htmlStr->insertTag($tag_from, "<sentence>", $token['to']+1, "</sentence>");
			}catch (Exception $e){
				echo "exception => " . $e->getMessage() . "\n";
				die("Exception");
			}	
			$tag_from = $token['to']+1;
		}
		
		$df = new DiffFormatter();
		$diff = $df->diff($content_before, $htmlStr->getContent(), true);
		if ( trim($diff) != "" ){
			$report->content = $htmlStr->getContent();
			$report->save();
			$deflated = gzdeflate($diff);
			$data = array(date("Y-m-d H:i:s"), 1 , $report_id, $deflated, $comment);
			$sql = "INSERT INTO reports_diffs (`datetime`, `user_id`, `report_id`, `diff`, `comment`) VALUES(?, ?, ?, ?, ?)";
			$db->execute($sql,$data);
		}		
		
	}	
}
?>
