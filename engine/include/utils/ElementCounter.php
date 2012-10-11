<?
class ElementCounter{
	
	var $elements = array();
	
	function add($name, $docid){
		if ( !isset($this->elements[$name]) )
			$this->elements[$name] = array("count"=>0, "docset"=>array());
	
		$this->elements[$name]['count']++;
		$this->elements[$name]['docset'][$docid] = 1;			
	}
	
	function getDict(){
		return $this->elements;
	}
	
}
?>