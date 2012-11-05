<?
/*
 * Jan Kocoń <janek.kocon@gmail.com>
 */
class ExportManager { 
	var $cclDocuments = array();
	//input parameters
	var $db = null; 				//instance of Database 
	var $corpus_ids = null; 		//array, value: id
	var $subcorpus_ids = null; 		//array, value: id
	var $document_ids = null; 		//array, value: id
	var $flags = null; 				//array, key: flag name; value: array of flag values
	var $annotation_layers = null; 	//array, value: id
	var $annotation_names = null;	//array, value: type name
	var $folder = null;				//string
	var $relation_set_ids = null;
	var $relation_type_ids = null;

	var $split_documents = false;
	var $separate_relations = false;
	
	var $iob_file_name = false;	
	
	var $report_ids = array(); 		//array, value: id
	var $reports = array();			//array, key: report id; value: report
	var $metadata = array();
	var $tokens = array();			//array, key: report id; value: token
	var $tags = array();			//array, key: report id; value: array (key: token_id, value: tag)
	var $annotations = array();		//array, key: report id; value: annotation
	var $relations = array();
	
	var $verbose = false;
	var $no_disamb = false;
	
	function setCorpusIds($corpus_ids){
		$this->corpus_ids = $corpus_ids;
	}	
	
	function setSubcorpusIds($subcorpus_ids){
		$this->subcorpus_ids = $subcorpus_ids;
	}
	
	function setDocumentIds($document_ids){
		$this->document_ids = $document_ids;
	}
	
	function setFlags($flags){
		$this->flags = $flags;
	}
	
	function setAnnotationLayers($annotation_layers){
		$this->annotation_layers = $annotation_layers;
	}
	
	function setAnnotationNames($annotation_names){
		$this->annotation_names = $annotation_names;
	}
	
	function setRelationSetIds($relation_set_ids){
		$this->relation_set_ids = $relation_set_ids;
	}
	
	function setRelationTypeIds($relation_type_ids){
		$this->relation_type_ids = $relation_type_ids;
	}	
	
	
	function setDb($db){
		assert('$db instanceof Database');
		$this->db = $db;
	}
	
	function setFolder($folder){
		$this->folder = $folder;
	}
	
	function setSplit($split_documents){
		$this->split_documents = $split_documents;
	}
	
	function setSeparateRelations($separate_relations){
		$this->separate_relations = $separate_relations;
	}
	
	function setIob($iob_file_name){
		$this->iob_file_name = $iob_file_name;
	}
	
	function log($text){
		if ( $this->verbose )
			echo date("[H:i:s]") . " $text\n";
	}
	
	function setVerbose($verbose){
		$this->verbose = $verbose;
	}
	
	function setNoDisamb($no_disamb){
		$this->no_disamb = $no_disamb;
	}
	
	/**
	 * Wczytuje dokumenty do eksportu na podstawie ustawionych filtrów.
	 */
	function readDocuments(){
		$reports = DbReport::getReports2($this->corpus_ids, $this->subcorpus_ids, 
						$this->document_ids, $this->flags);

		foreach ($reports as &$r)
			$this->reports[$r['id']] = &$r;
		$this->report_ids = array_keys($this->reports);
		$this->log(sprintf("Number of documents to export: %d", count($this->report_ids)));		
	}
	
	/**
	 * Wczytuje dane o treści dokumentów, tokenizacji, anotacjach i relacjach
	 * z bazy danych.
	 */
	function readContent(){
		$this->log("Reading content ...");
		$this->log(" a) reading tokens ...");
		$tokens = DbToken::getTokensByReportIds($this->report_ids);
		foreach ($tokens as &$token){
			$report_id = $token['report_id'];
			if (!array_key_exists($report_id, $this->tokens))
				$this->tokens[$report_id] = array();
			$this->tokens[$report_id][] = &$token; 
		}
		
		$this->log(" b) reading tags ...");
		$tags = DbTag::getTagsByReportIds($this->report_ids);
						
		$this->log(" c) assigning tags to tokens ...");
		foreach ($tags as &$tag){
			$report_id = $tag['report_id'];
			$token_id = $tag['token_id'];
			if ( !isset($this->tags[$report_id]) )
				$this->tags[$report_id] = array();
			if ( !isset($this->tags[$report_id][$token_id]) )
				$this->tags[$report_id][$token_id] = array();
			$this->tags[$report_id][$token_id][] = &$tag; 
		}
		
		/* If no_disamb is set then reset the disamb properties */
		if ($this->no_disamb){
			foreach ($this->tags as $report_id=>$tokens){
				foreach ($tokens as $token_id=>$tags){
					$ign = null;
					foreach ($tags as $i_tag=>$tag){
						$this->tags[$report_id][$token_id][$i_tag]['disamb'] = 0;
						if ($tag['ctag'] == "ign")
							$ign = $tag; 
					}
					/* Jeżeli jedną z interpretacji jest ign, to usuń pozostałe. */
					if ($ign)
						$this->tags[$report_id][$token_id] = array($ign);
				}				
			}
		}
		
		$this->log(" d) reading annotations ...");
		$annotations = DbAnnotation::getAnnotationsBySets($this->report_ids, 
							$this->annotation_layers, $this->annotation_names);												  
		foreach ($annotations as &$annotation){
			$report_id = $annotation['report_id'];
			if (!array_key_exists($report_id, $this->annotations))
				$this->annotations[$report_id] = array();
			$this->annotations[$report_id][] = &$annotation; 
		}
		
		$this->log(" e) reading relations ...");
		$relations = DbCorpusRelation::getRelationsBySets2($this->report_ids, 
							$this->relation_set_ids, $this->relation_type_ids);
		foreach ($relations as &$relation){
			$report_id = $relation['report_id'];			
			if (!array_key_exists($report_id, $this->relations)){
				$this->relations[$report_id] = array();
			}
			$this->relations[$report_id][] = &$relation; 
		}		
		$this->log("Reading content is done.");		
	}
	
	/**
	 * Read documents metadata from the database.
	 */
	function readMetadata(){
		$corpora = array();
		$cnt=0;
		foreach ($this->report_ids as $report_id){
			$cnt ++;
			$report = $this->reports[$report_id];
			$corpora[$report['corpora']][] = $report['id'];	
		}
		foreach ($corpora as $corpus_id => $report_ids){
			$corpus = DbCorpus::getCorpusById($corpus_id);
			$ext = $corpus['ext'];
			if ($ext){
				$exts = DbReport::getReportExtByIds($report_ids, $ext);
				foreach ($exts as $ext){
					$this->metadata[$ext['id']] = $ext;
				}
			}
		}
	} 
	
	/**
	 * Read documents segmentation, annotation and relations from databse.
	 */
	function processContent(){
		$allReports = count($this->report_ids);
		$cnt = 0;
		foreach ($this->report_ids as $report_id){
			$cnt ++;
			$report = $this->reports[$report_id];
			
			$tokens = array();
			$tags = array();	
			$annotations = array();
			$relations = array();		
			
			if (array_key_exists($report_id, $this->tokens))
				$tokens = $this->tokens[$report_id];				
			if (array_key_exists($report_id, $this->tags))
				$tags = $this->tags[$report_id];
			if (array_key_exists($report_id, $this->annotations))
				$annotations = $this->annotations[$report_id];			
			if (array_key_exists($report_id, $this->relations))
				$relations = $this->relations[$report_id];			
			
			try{
				$ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags);
								
				if (count($tokens)==0){
					$e = new CclError();
					$e->setClassName("CclSetFactory");
					$e->setFunctionName("create");
					$e->addObject("report", $report);
					$e->addComment("010 no tokenization in report");
					$ccl->addError($e);		
				}
				else {
					CclFactory::setAnnotationsAndRelations($ccl, $annotations, $relations);	
				}
				if (count($tags)==0){
					$e = new CclError();
					$e->setClassName("CclSetFactory");
					$e->setFunctionName("create");
					$e->addObject("report", $report);
					$e->addComment("011 no tags in report");				
					$ccl->addError($e);		
				}		
				$this->cclDocuments[$report_id] = $ccl;
			}
			catch(Exception $ex){
				print "!!!!! FIX ME report_id = $report_id\n";
			} 
		}
	}
	
	/**
	 * Write documents tokens, annotations and relations to files. 
	 */
	function writeContent(){
		if ($this->iob_file_name)
			$this->_writeIob();	
		else
			$this->_writeCcl(); 		
	}

	/**
	 * 
	 */
	function _writeCcl(){
		$subfolder = $this->folder . "/";
		$failed = array();
		if (!is_dir($subfolder)) mkdir($subfolder, 0777);
		foreach ($this->cclDocuments as $cclDocument){
			if ($this->split_documents){
				$subfolder = $this->folder . "/" . $cclDocument->getSubcorpus() . "/";
				if (!is_dir($subfolder)) mkdir($subfolder, 0777);
			} 
			
			if (!$cclDocument->hasErrors()){
				if ($this->separate_relations){
					CclWriter::write($cclDocument, $subfolder . $cclDocument->getFileName() . ".xml", CclWriter::$CCL);
					CclWriter::write($cclDocument, $subfolder . $cclDocument->getFileName() . ".rel.xml", CclWriter::$REL);
				}
				else 
					CclWriter::write($cclDocument, $subfolder . $cclDocument->getFileName() . ".xml", CclWriter::$CCLREL);									
			}
			else {
				echo "ERROR in " . $cclDocument->getFileName() . " \n";
				$failed[] = $cclDocument->getFileName();
				$errors = $cclDocument->getErrors(); 
				foreach ($errors as $error){
					print (string)$error . "\n";	
				}
			}			
		}
		
		if ( count($failed) ){
			$this->log("[ERROR] Following documents were not saved because of errors:");
			foreach ($failed as $f)
				$this->log(" - $f");
		}		
	}	
	
	/**
	 * Write tokens and annotations to a single IOB file.
	 */
	function _writeIob(){
		$subfolder = $this->folder . "/";
		if (!is_dir($subfolder)) mkdir($subfolder, 0777);
		$filename = $subfolder . $this->iob_file_name;
		IobWriter::write($this->cclDocuments, $filename);
	}
	
	/**
	 * Save documents metadata to files.
	 */
	function writeMetadata(){
		$this->log("Writing medatada ...");
		$subfolder = $this->folder . "/";			
		foreach ($this->reports as $r){
			$basic = array("id", "date", "title", "source", "author", "tokenization", "name:subcorpus");			
			$lines = array();
			$lines[] = "[document]";
			
			foreach ($basic as $b=>$br){
				$parts = split(":", $br);
				$name = $parts[0];
				$name_target = $parts[1] ? $parts[1] : $name;
				$lines[] = sprintf("%s = %s", $name_target, $r[$name]);
			}				
			
			if (isset($this->metadata[$r['id']])){
				$lines[] = "";
				$lines[] = "[metadata]";
				foreach ($this->metadata[$r['id']] as $key=>$val)
					if ($key != "id"){
						$key = preg_replace("/[^\p{L}\p{N}_]+/", "_", $key);
						$lines[] = sprintf("%s = %s", $key, $val);
					}
			}

			if ($this->split_documents)
				$subfolder = $this->folder . "/" . str_replace(" ", "_", $r['name']) . "/";
								
			$filename = $subfolder . str_pad($r['id'], 8, "0", STR_PAD_LEFT) . ".ini";
			file_put_contents($filename, implode("\n", $lines));
		}
	}
		
}




?>