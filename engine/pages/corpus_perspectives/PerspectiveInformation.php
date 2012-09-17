<?php

class PerspectiveInformation extends CCorpusPerspective {
	
	function execute()
	{
		global $corpus, $db;
		$owner = $db->fetch("SELECT * FROM users WHERE user_id = {$corpus['user_id']}");
		$this->page->set('owner', $owner);
	}
}
?>
