<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class PerspectiveMetadata extends CPerspective {

    public function __construct(CPage $page, $document){
        parent::__construct($page, $document);

        $this->page->includeJs("libs/select2/js/select2.js");
        $this->page->includeCss("libs/select2/css/select2.min.css");

        $this->page->includeJs('js/c_widget_select_parent_language.js');
        $this->page->includeJs("libs/bootstrap-select/bootstrap-select.min.js");
        $this->page->includeCss("libs/bootstrap-select/bootstrap-select.min.css");
        $this->page->includeCss("css/inc_report_metadata_form.css");
    }

    function execute(){

        global $corpus;
        $report = $this->page->report;

		if($report['parent_report_id'] !== null){
            $parent_report = DbReport::getParentReport($report['parent_report_id']);
            $this->page->set("parent_report", $parent_report);
        }
		$ext = DbReport::getReportExtById($report["id"]);
		$features = DbCorpus::getCorpusExtColumns($corpus['ext']);
		$subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
		$statuses = DbStatus::getAll();
		$formats = DbReport::getAllFormats();
		$language = DbReport::getFullLanguageName($report['lang']);
        $content_with_format = DBReport::getReportContentAndFormatById($report['id']);
		$this->page->set("report_language", $language);

		/* Jeżeli nie ma rozszrzonego wiersza atrybutów, to utwórz pusty */
		if ( $ext == null && $corpus['ext'] != "" ){
			DbReport::insertEmptyReportExt($report['id']);
			$ext = DbReport::getReportExtById($report['id']);
		}
		
		$features_index = array();

		if (is_array($features)){
			foreach ($features as $index=>&$f){
				$features_index[$f['field']] = &$f;
			    if($features[$index]['type'] == "text" && $features[$index]['value'] == null && $features[$index]['default'] != "empty"){
			        $features[$index]['value'] = $features[$index]['default'];
                }
			}
		}

		if (is_array($ext)){
			foreach ($ext as $k=>$v){
				if ($k != "id"){
                    if($features_index[$k]['type'] == 'text' && $features_index[$k]['default'] != null && $v === ""){
                        $features_index[$k][] = $features_index[$k]['default'];
                    } else{
                        $features_index[$k][] = $v;
                        $features_index[$k]['value'] = $v;
                    }
                }
			}
		}

        $content = htmlspecialchars( $content_with_format['content']);

		$this->page->set("content", $content);
		$this->page->set("features", $features);
        $this->page->set("features", $features);
		$this->page->set("subcorpora", $subcorpora);
		$this->page->set("statuses", $statuses);	
		$this->page->set("formats", $formats);
	}
}