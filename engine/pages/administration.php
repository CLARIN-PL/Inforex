<?php
class Page_administration extends CPage{

	function execute(){		
		global $db;
		$sql = "SELECT id, name FROM `corpora`";
		$this->set("corpusList", $db->fetch_rows($sql));
	}
}


?>