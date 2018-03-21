<?php

class PerspectiveCustom_annotation_sets extends CCorpusPerspective {

    function execute()
    {
        $this->page->includeJs("js/corpus_custom_annotation_sets.js");
        global $corpus, $user;

        $annotationSets = DbAnnotationSet::getCustomAnnotationSets($corpus, $user);

        $this->page->set("annotationSets", $annotationSets);
    }
}
