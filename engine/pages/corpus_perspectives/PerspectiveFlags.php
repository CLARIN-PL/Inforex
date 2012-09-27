<?php

class PerspectiveFlags extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$sql = "SELECT corpora_flag_id AS id, name, short, sort FROM corpora_flags WHERE corpora_id=?";
		$this->page->set("flagsList", $db->fetch_rows($sql, array($corpus['id'])));
	}
}
?>
