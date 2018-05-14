<?php

class PerspectiveCustom_annotation_sets extends CCorpusPerspective {

    function __construct(CPage $page)
    {
        parent::__construct($page);
        $this->page->includeJs("js/corpus_custom_annotation_sets.js");
    }

    function execute()
    {
        global $corpus, $user;

        $annotationSets = DbAnnotationSet::getCustomAnnotationSets($corpus, $user);

        $this->page->set("annotationSets", $annotationSets);
    }
}
