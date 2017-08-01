<?php

class PerspectiveCustom_annotation_sets extends CCorpusPerspective {

    function execute()
    {
        $this->page->includeJs("js/corpus_custom_annotation_sets.js");
        global $corpus, $db, $user;

        $sql = "SELECT ans.annotation_set_id as id, ans.name, ans.description, ans.public, u.screename, u.user_id, uas.annotation_set_id AS 'access' FROM annotation_sets_corpora ac " .
               " JOIN annotation_sets ans ON ans.annotation_set_id = ac.annotation_set_id AND ac.corpus_id =  ?" .
               " LEFT JOIN users u ON u.user_id = ans.user_id AND ans.user_id = ?" .
               " LEFT JOIN users_annotation_sets uas ON uas.annotation_set_id = ans.annotation_set_id AND uas.user_id = ? AND uas.user_id != ans.user_id    ";
        $annotationSets = $db->fetch_rows($sql, array($corpus['id'], $user['user_id'], $user['user_id']));

        $this->page->set("annotationSets", $annotationSets);
    }
}
