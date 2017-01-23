<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_about extends CPage{
	
	function execute(){
		
		$this->includeJs("libs/fancybox/jquery.fancybox.pack.js?v=2.1.6");
		$this->includeCss("libs/fancybox/jquery.fancybox.css?v=2.1.6");

		$this->includeJs("libs/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5");
		$this->includeCss("libs/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5");
		
		$this->includeJs("libs/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7");
		$this->includeCss("libs/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7");
	}
}


?>
