<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class TableReport extends ATable{
 	
 	var $_meta_table = "reports";
 	var $_meta_key = "id";
 	var $_meta_stmt = null;
 	
 	var $id = null;
 	var $corpora = null;
 	var $subcorpus_id = null;
 	var $date = null;
 	var $title = null;
 	var $source = null;
	var $author = null; 	
	var $content = null; 	
	var $type = null; 	
	var $status = null; 	
	var $user_id = null; 	
	var $format_id = null;
	var $filename = null;
	var $lang = null;
	var $parent_report_id = null;

	function getId(){
		return $this->id;
	}

	function getContent(){
		return $this->content;
	}

	function getFormatId(){
		return $this->format_id;
	}

	function getLang(){
		return $this->lang;
	}

	function getSubcorpusId(){
		return $this->subcorpus_id;
	}

	function getCorpusId(){
		return $this->corpora;
	}

	public function validateSchema(){
		global $config;
				
		// Do not validate an empty document.
		if(strlen(trim($this->content))==0){
			return array();
		}
		
		switch(DbReport::formatName($this->format_id)){
			case "xml":
				$parse = HtmlParser::parseXml($this->content);
				break;
			case "premorph":
				$parse = HtmlParser::validateXmlWithXsd($this->content, $config->path_engine."/resources/synat/premorph.xsd");
				break;
			default:
				$parse = array();
		}
		
		return $parse;
	}

}