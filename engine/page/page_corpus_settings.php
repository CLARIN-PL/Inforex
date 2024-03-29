<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_corpus_settings extends CPageCorpus{

	var $subpages = array(
			"information" => "Basic information", 
			"users" => "Users",
			"users_roles" => "Users roles",
            "user_activity" => "Users activity",
            "subcorpora" => "Subcorpora",
			"perspectives" => "Perspectives", 
			"flags" => "Flags",
			"relation_sets" => "Relation sets",
			"annotation_sets" => "Annotation sets",
            "custom_annotation_sets" => "Custom annotation sets",
			"event_groups" => "Event groups",
			"corpus_metadata" => "Metadata");

	function execute(){

		$subpage = array_key_exists('subpage', $_GET) ? $_GET['subpage'] : "information";
		
        /**
         * Dołączonie domyślnych plików JS i CSS dla perspektyw dokumentu.
         * js/page_report_{$subpage}.js — skrypty dla perspektywy $subpage
         * js/page_report_{$subpage}_resize.js — kod JS odpowiedzialny za automatyczne dopasowanie okna do strony.
         * css/page_report_{$subpage}.css — style CSS występujące tylko w danej perspektywie.
         */
        if (file_exists(Config::Config()->get_path_www() . "/js/page_corpus_{$subpage}.js")){
            $this->includeJs("js/page_corpus_{$subpage}.js");
        }
        if (file_exists(Config::Config()->get_path_www() . "/js/page_corpus_{$subpage}_resize.js")){
            $this->includeJs("js/page_corpus_{$subpage}_resize.js");
        }
        if (file_exists(Config::Config()->get_path_www() . "/css/page_corpus_{$subpage}.css")){
            $this->includeCss("css/page_corpus_{$subpage}.css");
        }

		$this->set('subpage', $subpage);
		$this->set('subpage_file', "inc_corpus_{$subpage}.tpl");
		$this->set('subpages', $this->subpages);

        $perspective_class_name = "Perspective".ucfirst($subpage);
        if (class_exists($perspective_class_name)){
            $perspective = new $perspective_class_name($this);
            $perspective->execute();
        }else{
            $this->set("error", "Perspective $subpage does not exist");
        }

	}
}

?>
