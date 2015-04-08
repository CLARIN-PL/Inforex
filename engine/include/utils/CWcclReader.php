<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class WcclReader{
	
	static function createFromString($content, $content_rels=null, $filename="not given"){
		
		/* Wczytuje tekst i anotacje */
		$tr = new TakipiReader();
		$tr->loadText($content);
		
		$chunks = array();
		while ($tr->nextChunk())
		 	$chunks[] = $tr->readChunk();
		
		/* Wczytuje relacje, jeżeli został podany content_rels. */ 
		$relations = array();
		if ( $content_rels ){
			$rs = simplexml_load_string($content_rels);
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
	
	/**
	 * 
	 */
	static function readDomFile($filename){		
		$content = file_get_contents($filename);
		$content = preg_replace('/[\p{Cf}\p{Co}\p{Cs}\p{Cn}\x00-\x09\x11-\x1f]/u','',$content);
		$content_rels = null;
		$cclrel = substr($filename, 0, strlen($filename)-4) . ".rel.xml";
		if ( file_exists($cclrel) ){
			$content_rels = file_get_contents($cclrel);
		}		
		return WcclReader::createFromString($content, $content_rels, $filename);
	}
	
}

?>



