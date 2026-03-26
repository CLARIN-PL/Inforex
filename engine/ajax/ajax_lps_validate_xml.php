<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 */
class Ajax_lps_validate_xml extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
	
		$report_id = intval($_POST['report_id']);
		$content = stripslashes(strval($_POST['content']));

		if ( strlen(trim($content)) == 0 ){
			return array("errors"=>array(array("line"=>"", "col"=>"", "description"=>"Empty document content")));
		}

		$c = new MyDOMDocument();
		$c->loadXML($content);
		$c->schemaValidate(Config::Cfg()->get_path_engine()."/resources/lps/lps.xsd");

		return array("errors"=>$c->errors );
	}
	
}
