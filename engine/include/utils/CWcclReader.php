<?

class WcclReader{
	
	static function createFromString($content, $filename="not given"){
		
		$relation = null;

		$regex = "/<relations>.*<\/relations>/mus";

		if ( preg_match($regex, $content, $m) ){
			$relation = $m[0];
			$content = preg_replace($regex, "", $content);
		}
			
		$tr = new TakipiReader();
		$tr->loadText($content);
		
		$chunks = array();
		while ($tr->nextChunk())
		 	$chunks[] = $tr->readChunk();
		
		$relations = array();
		if ( $relation ){
			$rs = simplexml_load_string($relation);
			foreach ($rs as $r){
				$name = (string)$r['name'];
				$source_sent = (string)$r->from['sent'];
				$source_chan = (string)$r->from['chan'];
				$source_val = (string)$r->from[0];
				$target_sent = (string)$r->to['sent'];
				$target_chan = (string)$r->to['chan'];
				$target_val = (string)$r->to[0];
			
				$relation = new WcclRelation($name, $source_sent, $source_chan, $source_val, $target_sent, $target_chan, $target_val);
				$relations[] = $relation;
			}
		}
		
		$wd = new WcclDocument();
		$wd->name = $filename;
		$wd->chunks = $chunks;
		$wd->relations = $relations;
		
		return $wd;
	}
	
	
	static function readDomFile($filename){		
		$content = file_get_contents($filename);		
		return WcclReader::createFromString($content, $filename);
	}
	
}

?>



