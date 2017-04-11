<?php

class PerspectiveCustom_annotation_sets extends CCorpusPerspective {

    function execute()
    {
        $this->page->includeJs("js/c_autoresize.js");
        global $corpus, $db;


        $sql = "SELECT ans.annotation_set_id AS id, ans.description, ans.public, u.screename " .
            " FROM annotation_sets ans" .
            " JOIN users u ON u.user_id = ans.user_id AND ans.user_id =  " . $corpus['user_id'] .
            " ORDER BY ans.description";

        $sql = "SELECT ans.annotation_set_id AS id, ans.description, ans.public " .
            " FROM annotation_sets ans" .
            " JOIN annotation_sets_corpora atc ON atc. corpus_id = " . $corpus['id'];
        $sql = "SELECT ans.annotation_set_id as id, ans.description, ans.public FROM annotation_sets_corpora ac" .
               " JOIN annotation_sets ans ON ans.annotation_set_id = ac.annotation_set_id ".
               " WHERE ac.corpus_id =  " . $corpus['id'];
        ChromePhp::log($sql);
        $annotationSets = db_fetch_rows($sql);
        $this->page->set("annotationSets", $annotationSets);
    }
}
