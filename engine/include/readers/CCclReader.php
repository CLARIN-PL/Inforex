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
	
	static function readCclDocumentFromFolder2($path, $ignChannels, $contains){
		$documents = FolderReader::readFilesFromFolder($path);
		sort($documents);
		$cclDocuments = array();
		foreach ($documents as $d){
			if (substr($d, -4) != ".xml" || substr($d, -8) == ".rel.xml"){
				echo "  WARNING [file $d] : file name should be: *.xml and not *.rel.xml!\n";
				continue;
			}
			echo $d . "\n";			
			try{
				$cWcclDocument = WcclReader::readDomFile($d);
				$fileName = array_shift(explode(".",array_pop(explode("/", $d))));
				
				$ccl = new CclDocument();
				$ccl->setFileName($fileName);
				foreach($cWcclDocument->chunks as $chunk){
					$c = new CclChunk();
					$c->setId($chunk->id);
					$c->setType($chunk->type);
					foreach($chunk->sentences as $sentence){
						$s = new CclSentence();
						$s->setId($sentence->id);
						foreach($sentence->tokens as $token){
							$t = new CclToken();
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
								if (!($ignChannels && in_array($name, $ignChannels))){
									if (!($contains && mb_strstr($name,$contains)===false)){
										$t->channels[$name] = $value;	
									}
								}
							}							
							//$t->channels = $token->channels;
							$s->addToken($t);
						}
						$c->addSentence($s);
					}
					$ccl->addChunk($c);
				}
				$cclDocuments[] = $ccl;				
			}
			catch(Exception $ex){
				print_r($ex);
			}
		}
		return $cclDocuments;
	}		
	
	
}

?>
