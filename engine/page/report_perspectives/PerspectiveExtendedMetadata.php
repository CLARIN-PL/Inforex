<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveExtendedMetadata extends CPerspective {

    function execute()
    {
        global $corpus;
        $row = $this->page->get("row");

        if($row['parent_report_id'] !== null){
            $parent_report = DbReport::getParentReport($row['parent_report_id']);
            $this->page->set("parent_report", $parent_report);
        }
        $ext = DbReport::getReportExtById($row['id']);
        $translation_languages = DbReport::getReportTranslationLanguages($row['id']);
        $translations = DbReport::getReportTranslations($row['id']);
        $selected_translation = reset($translations);
        $selected_language  = $selected_translation['language'];

        ChromePhp::log($selected_translation);

        ChromePhp::log($translation_languages);
        ChromePhp::log($translations);

        $features = DbCorpus::getCorpusExtColumns($corpus['ext']);
        $subcorpora = DbCorpus::getCorpusSubcorpora($corpus['id']);
        $statuses = DbStatus::getAll();
        $formats = DbReport::getAllFormats();

        /* Jeżeli nie ma rozszrzonego wiersza atrybutów, to utwórz pusty */
        if ( $ext == null && $corpus['ext'] != "" ){
            DbReport::insertEmptyReportExt($row['id']);
            $ext = DbReport::getReportExtById($row['id']);
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

        $content = $row['content'];
        if ( $row['format'] == 'plain'){
            $content = htmlspecialchars($content);
        }

        $images = array_chunk(DbImage::getReportImages($row['id']), 3);
        $this->page->set("images", $images);
        $this->page->set("content", $content);
        $this->page->set("features", $features);
        $this->page->set("subcorpora", $subcorpora);
        $this->page->set("statuses", $statuses);
        $this->page->set("formats", $formats);
        $this->page->set("translation_languages", $translation_languages);
        $this->page->set("translations", $translations);
        $this->page->set("selected_translation", $selected_translation);
        $this->page->set("selected_language", $selected_language);
    }
}

?>