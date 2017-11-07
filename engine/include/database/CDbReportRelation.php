<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbReportRelation{

	/**
	 * Returns a list of relations from given document.
     * @param int $corpusId
	 * @param int $reportId
	 * @return An array of annotation schemas.
	 */
	static function getReportRelations($corpusId, $reportId, $relationSetIds, $relStages){
		global $db;
		global $user;

        $params = array($reportId, $corpusId, $corpusId);

        if($relStages == "final"){
            $where_sql = "WHERE relations.stage = 'final' AND rasrc.stage = 'final' ";
        } else if ($relStages == "agreement"){
            $where_sql = "WHERE (relations.stage = 'agreement' AND rasrc.stage = 'agreement' AND radst.stage = 'agreement' AND relations.user_id = ?)";
            $params[] = $user['user_id'];
        } else if ($relStages == "relation_agreement"){
            $where_sql = "WHERE (relations.stage = 'agreement' AND rasrc.stage = 'final' AND radst.stage = 'final' AND relations.user_id = ?)";
            $params[] = $user['user_id'];
        }


        $sql = 	"SELECT relations.id, " .
            "   relations.source_id, " .
            "   relation_sets.relation_set_id, " .
            "   relations.stage, " .
            "   srct.group_id AS source_group_id, " .
            "   srct.annotation_subset_id AS source_annotation_subset_id, " .
            "   dstt.group_id AS target_group_id, " .
            "   dstt.annotation_subset_id AS target_annotation_subset_id, " .
            "   relations.target_id, " .
            "   relation_types.name, " .
            "   rasrc.text source_text, " .
            "   rasrc.type source_type, " .
            "   radst.text target_text, " .
            "   radst.type target_type " .
            " FROM relations " .
            " JOIN relation_types ON (relations.relation_type_id=relation_types.id " .
            "  AND relations.source_id IN " .
            "    (SELECT ran.id " .
            "     FROM reports_annotations ran " .
            "     JOIN annotation_types aty " .
            "       ON (ran.report_id=? " .
            "           AND ran.type=aty.name " .
            "           AND aty.group_id IN " .
            "             (SELECT annotation_set_id " .
            "               FROM annotation_sets_corpora  " .
            "               WHERE corpus_id=?) " .
            "  ))) " .
            //($_COOKIE['active_annotation_types'] && $_COOKIE['active_annotation_types']!="{}"
            //    ? " AND (relation_types.relation_set_id IN (" . preg_replace("/\:1|id|\{|\}|\"|\\\/","",$_COOKIE['active_annotation_types']) . ") OR relation_types.name='Continous') "
            //    : "") .
            " JOIN reports_annotations rasrc ON (relations.source_id=rasrc.id) " .
            " JOIN relation_sets ON (relation_types.relation_set_id = relation_sets.relation_set_id) " .
            " JOIN corpora_relations ON (relation_sets.relation_set_id = corpora_relations.relation_set_ID) AND corpora_relations.corpus_id = ? " .
            " JOIN reports_annotations radst ON (relations.target_id=radst.id) " .
            " LEFT JOIN annotation_types srct ON (rasrc.type=srct.name) " .
            " LEFT JOIN annotation_types dstt ON (radst.type=dstt.name) " .
            $where_sql.
            " ORDER BY relation_types.name";

        $report_relations = $db->fetch_rows($sql, $params);
        return $report_relations;
	}
	
	
}

?>