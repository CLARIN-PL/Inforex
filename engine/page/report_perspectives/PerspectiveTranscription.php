<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveTranscription extends CPerspective {

    function __construct(CPage $page, $document){
        parent::__construct($page, $document);

        $this->page->includeCss("css/styles_lps.css");
        $this->page->includeCss("js/jquery/jquery.iviewer-0.4.2/jquery.iviewer.css");
        $this->page->includeJs("js/jquery/jquery.iviewer-0.4.2/jquery.iviewer.js");
        $this->page->includeJs("js/c_editor_transcription.js");
        $this->page->includeJs("js/jquery/splitter/splitter.jss");
    }
	
	function execute(){

		$orientation = isset($_GET['orientation']) ? $_GET['orientation'] : $_COOKIE['orientation'];
				
		$_COOKIE['orientation'] = $orientation; 
						
		$images = $this->page->getDb()->fetch_rows("SELECT * FROM reports_and_images ri JOIN images i ON (ri.image_id=i.id) WHERE ri.report_id = ? ORDER BY `position`", array($this->document['id']));
		$this->page->set('images', $images);
		$this->page->set('orientation', $orientation);
	}
	
}
