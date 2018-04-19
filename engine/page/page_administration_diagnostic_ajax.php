<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Page_administration_diagnostic_ajax extends CPageAdministration {

    function execute(){
        global $config;

        $this->includeJs("libs/bootstrap-sortable/moment.min.js"); // required by bootstrap-sortable.js
        $this->includeJs("libs/bootstrap-sortable/bootstrap-sortable.js");
        $this->includeCss("libs/bootstrap-sortable/bootstrap-sortable.css");

        $js_folder = $config->path_www . "/js";
        $files = scandir($js_folder);
        $regex_pattern = "/doAjax(.+|)[(]('|\")([^'\"]+)('|\")/";
        $ajax_list = array();

        foreach($files as $file) {
            $file_code = file_get_contents($js_folder . "/" . $file, true);
            preg_match_all($regex_pattern, $file_code, $matches);
            $found_ajax = $matches[3];
            if($found_ajax){
                foreach($found_ajax as $ajax){
                    $ajax_list["ajax_".$ajax][$file] = true;
                }
            }
        }
        ksort($ajax_list);

        $this->set('items', $ajax_list);
    }

}