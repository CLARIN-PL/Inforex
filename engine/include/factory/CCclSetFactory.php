<?

class CclSetFactory { 
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
	
	var $report_ids = array(); 		//array, value: id
	var $reports = array();			//array, key: report id; value: report
	var $tokens = array();			//array, key: report id; value: token
	var $tags = array();			//array, key: report id; value: array (key: token_id, value: tag)
	var $annotations = array();		//array, key: report id; value: annotation
	var $relations = array();
	
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
	
	function acquireData(){
		//get reports
		$reports = DbReport::getReports($this->corpus_ids, 
									    $this->subcorpus_ids, 
									    $this->document_ids, 
									    $this->flags);
		foreach ($reports as &$r){
			$id = $r['id'];
			$this->reports[$id] = &$r;
		}
		$this->report_ids = array_keys($this->reports);
		
		//get tokens
		$tokens = DbToken::getTokensByReportIds($this->report_ids);
		foreach ($tokens as &$token){
			$report_id = $token['report_id'];
			if (!array_key_exists($report_id, $this->tokens))
				$this->tokens[$report_id] = array();
			$this->tokens[$report_id][] = &$token; 
		}
		
		//get tags
		$tags = DbTag::getTagsByReportIds($this->report_ids);
		foreach ($tags as &$tag){
			$report_id = $tag['report_id'];
			$token_id = $tag['token_id'];
			if (!array_key_exists($report_id, $this->tags))
				$this->tags[$report_id] = array();
			if (!array_key_exists($token_id, $this->tags[$report_id])  )
				$this->tags[$report_id][$token_id] = array();
			$this->tags[$report_id][$token_id][] = &$tag; 
		}
		
		//get annotations
		$annotations = DbAnnotation::getAnnotationsBySets($this->report_ids, 
														  $this->annotation_layers, 
														  $this->annotation_names);												  
		foreach ($annotations as &$annotation){
			$report_id = $annotation['report_id'];
			if (!array_key_exists($report_id, $this->annotations))
				$this->annotations[$report_id] = array();
			$this->annotations[$report_id][] = &$annotation; 
		}
		
		//get relations
		$relations = DbCorpusRelation::getRelationsBySets2($this->report_ids, 
														   $this->relation_set_ids, 
														   $this->relation_type_ids);
		foreach ($relations as &$relation){
			$report_id = $relation['report_id'];			
			if (!array_key_exists($report_id, $this->relations)){
				$this->relations[$report_id] = array();
			}
			$this->relations[$report_id][] = &$relation; 
		}		
		
	}
	
	function create(){
		foreach ($this->report_ids as $report_id){
			echo "Report: {$report_id}\n";
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
			
			$ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags);				 
			CclFactory::setAnnotationsAndRelations($ccl, $annotations, $relations);					

			
			if (count($tokens)==0){
				$e = new CclError();
				$e->setClassName("CclSetFactory");
				$e->setFunctionName("create");
				$e->addObject("report", $report);
				$e->addComment("010 no tokenization in report");
				$ccl->addError($e);		
			}
			if (count($tags)==0){
				$e = new CclError();
				$e->setClassName("CclSetFactory");
				$e->setFunctionName("create");
				$e->addObject("report", $report);
				$e->addComment("011 no tags in report");				
				$ccl->addError($e);		
			}		
			if (count($annotations)==0){
				$e = new CclError();
				$e->setClassName("CclSetFactory");
				$e->setFunctionName("create");
				$e->addObject("report", $report);
				$e->addComment("012 no annotations in report");				
				$ccl->addError($e);		
			}
			if (count($relations)==0){
				$e = new CclError();
				$e->setClassName("CclSetFactory");
				$e->setFunctionName("create");
				$e->addObject("report", $report);
				$e->addComment("013 no relations in report");				
				$ccl->addError($e);		
			}
			$this->cclDocuments[$report_id] = $ccl; 
		}
	}
	

	function write(){
		$subfolder = $this->folder . "/";
		if (!is_dir($subfolder)) mkdir($subfolder, 0777);
		foreach ($this->cclDocuments as $cclDocument){
			//echo $cclDocument->getFileName() . "--\n";
			if (!$cclDocument->hasErrors()){
				echo "OK  " . $cclDocument->getFileName() . " \n";
				CclWriter::write($cclDocument, $subfolder . $cclDocument->getFileName());
			}
			else {
				echo "ERR " . $cclDocument->getFileName() . " \n";
				$errors = $cclDocument->getErrors(); 
				foreach ($errors as $error){
					$comments = $error->getComments();
					foreach ($comments as $comment)
						print $comment . "\n";	
				}
			}			
		}
		
	}	
	
		
		
	
	
}




?>