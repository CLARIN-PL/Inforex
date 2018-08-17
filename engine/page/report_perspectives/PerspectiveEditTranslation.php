<?php

/**
 * Created by PhpStorm.
 * User: mikolaj
 * Date: 19.07.18
 * Time: 11:34
 */
class PerspectiveEditTranslation extends CPerspective {

    function __construct(CPage $page, $document)
    {
        parent::__construct($page, $document);
        $this->page->includeJs("js/c_widget_annotation_type_tree.js");
        $this->page->includeJs("js/c_widget_relation_sets.js");
        $this->page->includeJs("js/c_autoaccordionview.js");
    }

    function execute()
    {
        global $corpus;

        $report = $this->page->report;

        if($report['parent_report_id'] == null){
            $this->page->set("no_translation", true);
        } else{
            $parent_report = DbReport::getParentReport($report['parent_report_id']);
            $parent_html = ReportContent::getHtmlStr($parent_report);
            $parent_content = $parent_html->getContent();

            $this->page->set("parent_content", $parent_content);
            $this->page->set("parent_report", $parent_report);
        }

        $html = ReportContent::getHtmlStr($report);
        $content = $html->getContent();
        $this->page->set("content", $content);
        $this->page->set("corpus_id", $corpus['id']);
        $this->page->set("report_id", $report['id']);
    }
}