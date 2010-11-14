<?php

class PerspectiveTranscription extends CPerspective {
	
	function execute()
	{
		$images = db_fetch_rows("SELECT * FROM reports_and_images ri JOIN images i ON (ri.image_id=i.id) WHERE ri.report_id = ?", array($this->document['id']));
		$this->page->set('images', $images);
	}
	
}

?>
