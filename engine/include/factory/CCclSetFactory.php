<?

class CClSetFactory { 
	
	function __construct(){
		$this->ccl_documents = array();
		$this->report_ids = array();
		$this->all_reports = array();
		$this->relationsTypes = array();
		$this->all_tokens = array();
		$this->all_tokens_tags = array();
		$this->all_relations = array();
		$this->all_ann_types = array();		
		
		$this->all_relations_id = array();
		$this->all_ann_types_id = array();		
		$this->all_reports_id = array();
		$this->all_tokens_id = array();
		$this->all_tokens_tags_id = array();	
		$this->relationsTypes_names = array();	
		
		$this->relations = array();
	}
	
	function setReports(&$report_ids, &$all_reports){
		$this->report_ids = &$report_ids;
		$this->all_reports = &$all_reports;
	}
	
	function setRelationSets(&$relationsTypes){
		$this->relationsTypes = &$relationsTypes;
		foreach ($this->relationsTypes as &$relationType){
			$relations[] = &$relationType['type'];
			$relationsTypes_names[$relationType['type']] = &$relationType['name'];
		}		
	}
	
	function setRelationTypeIds(&$relation_type_ids){
		if (!empty($relation_type_ids)) $this->relations = &$relation_type_ids;
	}
	
	function setTokens(&$all_tokens, &$all_tokens_tags){
		$this->all_tokens = &$all_tokens;
		$this->all_tokens_tags = &$all_tokens_tags;
	}
	
	function create(){
		
	}
	
	
	
	
	
	
}




?>