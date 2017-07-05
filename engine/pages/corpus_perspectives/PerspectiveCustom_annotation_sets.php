<?php

class PerspectiveCustom_annotation_sets extends CCorpusPerspective {

    function execute()
    {
        $this->page->includeJs("js/c_autoresize.js");
        $this->page->includeJs("js/corpus_custom_annotation_sets.js");
        global $corpus, $db, $user;

        $sql = "SELECT ans.annotation_set_id as id, ans.name, ans.description, ans.public, u.screename  FROM annotation_sets_corpora ac " .
               " JOIN annotation_sets ans ON ans.annotation_set_id = ac.annotation_set_id AND ac.corpus_id =  ". $corpus['id'] .
               " JOIN users u ON u.user_id = ans.user_id AND ans.user_id =  " . $user['user_id'];
        $annotationSets = db_fetch_rows($sql);
        $this->page->set("annotationSets", $annotationSets);
    }
}
