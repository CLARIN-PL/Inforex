<?php

class PerspectiveTopic extends CPerspective {
	
	function execute()
	{
				
		$topics = db_fetch_rows("SELECT * FROM reports_types ORDER BY `name`");
		$this->page->set('topics', $topics);
	}
	
}

?>
