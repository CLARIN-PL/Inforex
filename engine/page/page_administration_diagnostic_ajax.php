<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_administration_diagnostic_ajax extends CPageAdministration {

    function execute(){
        $this->includeJs("libs/bootstrap-sortable/moment.min.js"); // required by bootstrap-sortable.js
        $this->includeJs("libs/bootstrap-sortable/bootstrap-sortable.js");
        $this->includeCss("libs/bootstrap-sortable/bootstrap-sortable.css");

        $file_keywords = array('corpus', 'report');
        $ajax_list = PageAjaxDiagnostic::findAjaxUsage($file_keywords);
        $this->set('items', $ajax_list);
    }

}