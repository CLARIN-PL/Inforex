<?php

class PerspectiveRelation_sets extends CCorpusPerspective {

    function execute()
    {
        $this->page->includeJs("js/corpus_relation_sets.js");
        global $corpus, $db;
        $sql_relation_sets = "SELECT rs.relation_set_id, rs.name, rs.description, cr.corpus_id AS assigned FROM relation_sets rs LEFT JOIN corpora_relations cr ON cr.relation_set_id = rs.relation_set_id AND cr.corpus_id = ?";
        $relation_sets = $db->fetch_rows($sql_relation_sets, array($corpus['id']));

        ChromePhp::log($relation_sets);
        $this->page->set("relationSets", $relation_sets);
    }
}
