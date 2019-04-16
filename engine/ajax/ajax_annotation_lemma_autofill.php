<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class ajax_annotation_lemma_autofill extends CPageCorpus {

	function execute(){
        $annotationIds = $this->getRequestParameterRequired("annotationIds");

        $textsLemmas = $this->getAnnotationPossibleLemma($this->getCorpusId());
        $lemmas = array();
        foreach ($annotationIds as $annotationId){
            $an = new TableReportAnnotation($annotationId);
            if ( isset($textsLemmas[$an->getText()]) ){
                $lemmas[] = array("annotationId" => $annotationId, "lemma" => $textsLemmas[$an->getText()]);
            }
        }

        return $lemmas;
	}

    function getAnnotationPossibleLemma($corpusId){
        global $db;

        $builder = new SqlBuilder(DB_TABLE_REPORTS_ANNOTATIONS_LEMMA, "al");
        $builder->addSelectColumn(new SqlBuilderSelect("an.text", "text"));
        $builder->addSelectColumn(new SqlBuilderSelect("al.lemma", "lemma"));
        $builder->addSelectColumn(new SqlBuilderSelect("COUNT(*)", "lc"));
        $builder->addJoinTable(
            new SqlBuilderJoin(DB_TABLE_REPORTS_ANNOTATIONS, "an", "an.id = al.report_annotation_id"));
        $builder->addJoinTable(new SqlBuilderJoin(DB_TABLE_REPORTS, "r", "an.report_id = r.id"));
        $builder->addWhere(new SqlBuilderWhere("an.text IS NOT NULL"));
        $builder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($corpusId)));
        $builder->addGroupBy("text");
        $builder->addOrderBy("lc DESC");

        list($sql, $params) = $builder->getSql();
        $rows = $db->fetch_rows($sql, $params);

        $lemmas = array();
        foreach ($rows as $row){
            $text = $row['text'];
            $lemma = $row['lemma'];

            if (!isset($lemmas[$text])){
                $lemmas[$text] = $lemma;
            }
        }
        return $lemmas;
    }

}