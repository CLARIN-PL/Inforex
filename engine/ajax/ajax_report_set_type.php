<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_report_set_type extends CPageCorpus {
	
	function execute(){
		$type = intval($_POST['type']);
		$id = intval($_POST['id']);
		$sql="UPDATE reports SET type=$type WHERE id=$id";
		$this->getDb()->execute($sql);

		$sql="SELECT name FROM reports_types WHERE id=$type";
		$type_name = $this->getDb()->fetch_one($sql);

		return array("type_name"=>$type_name);
	}
	
}
