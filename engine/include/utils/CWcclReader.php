<?

class WcclReader{
	
	static function readDomFile($filename){
		
		$content = file_get_contents($filename);

		$regex = "/(<chunkList>.*<\/chunkList>)(\n)?(<relations>.*<\/relations>)?/mus";
		if (!preg_match($regex, $content, $m))
			throw new Exception("The content does not match to '$regex'");
			
		$tr = new TakipiReader();
		$tr->loadText("<doc>".$m[1]."</doc>");
		
		$chunks = array();
		while ($tr->nextChunk())
		 	$chunks[] = $tr->readChunk();
		
		$relations = array();
		if ( $m[3] ){
			$rs = simplexml_load_string($m[3]);
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
	
}

?>



