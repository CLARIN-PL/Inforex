<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/** 
 * Object reader for ccl format.
 * Read a ccl file and transform into CclDocument object. 
 */

class CclReader{
	
	static function readCclDocumentFromFolder($path){
		$documents = FolderReader::readFilesFromFolder($path);
		$cclDocuments = array();
		foreach ($documents as $d){
			echo $d . "\n";			
			try{
				$cclDocuments[] = WcclReader::readDomFile($d);
			}
			catch(Exception $ex){
				print_r($ex);
			}
		}
		return $cclDocuments;
	}	
	
	/**
	 * 
	 */
	static function readCclFromFile($filename, $ignChannels=array(), $reverse=false, $contains=""){
		if (substr($filename, -4) != ".xml" || substr($filename, -8) == ".rel.xml"){
			//echo "  WARNING [file $filename] : file name should be: *.xml and not *.rel.xml!\n";
			//continue;
		}
		$cWcclDocument = WcclReader::readDomFile($filename);
		$fileName = array_shift(explode(".",array_pop(explode("/", $filename))));
		
		$ccl = new CclDocument();
		$ccl->setFileName($fileName);
		$chunk_id = 1;
		foreach($cWcclDocument->chunks as $chunk){
			$from = 0;
			$c = new CclChunk();
			$c->setId($chunk_id++);
			$c->setType($chunk->type);
			foreach($chunk->sentences as $sentence){
				$s = new CclSentence();
				$s->setId($sentence->id);
				foreach($sentence->tokens as $token){
					$t = new CclToken();
					$t->setFrom($from);
					$from += mb_strlen($token->orth);
					$t->setTo($from-1);
					$t->setOrth($token->orth);
					$t->setNs($token->ns);
					foreach($token->lex as $lexeme){
						$l = new CclLexeme();
						$l->setBase($lexeme->base);
						$l->setCtag($lexeme->ctag);
						$l->setDisamb($lexeme->disamb);
						$t->addLexeme($l);
					}
					foreach ($token->channels as $name => $value){
						
						if ( !$ignChannels
								|| (in_array($name, $ignChannels) && $reverse)
								|| (!in_array($name, $ignChannels) && !$reverse)
								){								
							if (!($contains && mb_strstr($name,$contains)===false)){
								$t->channels[$name] = $value;	
							}
						}
					}							

					$s->addToken($t);
				}
				$c->addSentence($s);
			}
			$ccl->addChunk($c);
		}
				
		return $ccl;						
	}
	
	/**
	 * 
	 */
	static function readCclDocumentBatch($batch, $ignChannels=array(), $reverse=false, $contains=""){
		
		$documents = array();
		foreach (file($batch) as $d){
			if  ( trim($d) == "" )
				continue;
			$documents[] = dirname($batch) . DIRECTORY_SEPARATOR . trim($d);
		}		
		
		sort($documents);
		
		$cclDocuments = array();
		foreach ($documents as $d){
			if (substr($d, -4) != ".xml" || substr($d, -8) == ".rel.xml"){
				echo "  WARNING [file $d] : file name should be: *.xml and not *.rel.xml!\n";
				continue;
			}
			echo $d . "\n";			
			try{
				$cclDocuments[] = CclReader::readCclFromFile($d, $ignChannels, $reverse, $contains);				
			}
			catch(Exception $ex){
				print_r($ex);
			}
		}
		return $cclDocuments;
	}		
	
	
}

?>
