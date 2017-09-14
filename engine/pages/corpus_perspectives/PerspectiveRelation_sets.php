<?php

class PerspectiveRelation_sets extends CCorpusPerspective {

    function execute()
    {
        $this->page->includeJs("js/corpus_relation_sets.js");

        global $corpus, $db, $user;
        $sql_relation_sets = "SELECT rs.relation_set_id, rs.name, rs.public, rs.description, rs.user_id, u.screename, cr.corpus_id AS assigned FROM relation_sets rs 
        LEFT JOIN corpora_relations cr ON cr.relation_set_id = rs.relation_set_id AND cr.corpus_id = ? 
        LEFT JOIN users u ON u.user_id = rs.user_id";

        $relation_sets = $db->fetch_rows($sql_relation_sets, array($corpus['id']));

        ChromePhp::log($relation_sets);

        foreach($relation_sets as $key => $relation_set){
            if($relation_set['user_id'] != $user['user_id'] && $relation_set['public'] != 1 && $relation_set['assigned'] == NULL){
                ChromePhp::log($relation_set['assigned']);
                unset($relation_sets[$key]);
            }
        }
        $this->page->set("relationSets", $relation_sets);
    }
}
