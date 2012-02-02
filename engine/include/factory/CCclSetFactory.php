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
	
	
	var $report_ids = array(); 		//array, value: id
	var $reports = array();			//array, key: report id; value: report
	var $tokens = array();			//array, key: report id; value: token
	var $annotations = array();		//array, key: report id; value: annotation
	
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
		$this->annotationLayers = $annotation_layers;
	}
	
	function setAnnotationNames($annotation_names){
		$this->annotationNames = $annotation_names;
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
		
		
		//get annotations
		$annotations = DbAnnotation::getAnnotationsBySets($this->report_ids, 
														  $this->annotation_layers, 
														  $this->annotation_names);
		foreach ($tokens as &$annotation){
			$report_id = $annotation['report_id'];
			if (!array_key_exists($report_id, $this->annotations))
				$this->annotations[$report_id] = array();
			$this->annotations[$report_id][] = &$annotation; 
		}
	}
	
	function create(){
		foreach ($this->report_ids as $report_id){
			$report = $this->reports[$report_id];
			$tokens = array();
			if (array_key_exists($report_id, $this->tokens))
				$tokens = $this->tokens[$report_id];
			if (count($tokens)==0)
				echo "No tokenization in report: $report_id \n";
			else 
				$this->cclDocuments[] = CclFactory::createFromReportAndTokens($report, $tokens);
			
		}
	}
	

	function write(){
		$subfolder = $this->folder . "/";
		if (!is_dir($subfolder)) mkdir($subfolder, 0777);
		foreach ($this->cclDocuments as $cclDocument){
			//echo $cclDocument->getFileName() . "--\n";
			CclWriter::write($cclDocument, $subfolder . $cclDocument->getFileName());			
		}
		
	}	
	
		
		
	
	
}




?>