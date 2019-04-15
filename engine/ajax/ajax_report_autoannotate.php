<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class ajax_report_autoannotate extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyPerspectiveAccess[] = 'annotation_autoextension';
    }

    function execute(){
	    $reportId = $this->getRequestParameterRequired("documentId");
        $annotationSetId = $this->getRequestParameterRequired("annotationSetId");

        $report = new TableReport($reportId);
        $phraseDict = $this->createPhraseDictionary($report, $annotationSetId);

        $annotator = new ReportAnnotator($report);
        $annotations = $annotator->findDisjoint($phraseDict, $this->getUserId());

        $discarded = $this->getDiscardedAnnotations($reportId, $annotationSetId);
        $annotations = $annotator->filterDuplicated($annotations, $discarded);

        $newFinal = $this->getNewAndFinalAnnotations($reportId, $annotationSetId);
        $annotations = $annotator->filterOverlapping($annotations, $newFinal);

        $tokens = DbToken::getTokenByReportIdObj($reportId);
        if ( count($tokens) ){
            $annotations = $annotator->filterNotAllignToTokens($annotations, $tokens);
        }

        foreach ($annotations as $annotation){
            $annotation->save();
        }

        return $annotations;
	}

	function createPhraseDictionary($report, $annotationSetId){
	    global $db;
	    $builder = new SqlBuilder(DB_TABLE_REPORTS_ANNOTATIONS, "an");
	    $builder->addJoinTable(new SqlBuilderJoin(DB_TABLE_REPORTS, "r", "an.report_id = r.id"));
	    $builder->addJoinTable(new SqlBuilderJoin(DB_TABLE_ANNOTATION_TYPES, "at", "at.annotation_type_id = an.type_id"));
	    $builder->addSelectColumn(new SqlBuilderSelect("an.text"));
        $builder->addSelectColumn(new SqlBuilderSelect("an.type_id"));
	    $builder->addWhere(new SqlBuilderWhere("r.lang = ?", array($report->getLang())));
        //$builder->addWhere(new SqlBuilderWhere("r.subcorpus_id = ?", array($report->getSubcorpusId())));
        $builder->addWhere(new SqlBuilderWhere("r.corpora = ?", array($report->getCorpusId())));
        $builder->addWhere(new SqlBuilderWhere("an.stage = ?", array(AnnotationStage::StageFinal)));
        $builder->addWhere(new SqlBuilderWhere("at.group_id= ?", array($annotationSetId)));

        list($sql, $params) = $builder->getSql();
        $rows = $db->fetch_rows($sql, $params);
        $dict = array();
        foreach($rows as $row){
            $text = trim($row['text']);
            if (strlen($text)>0) {
                $typeId = $row['type_id'];
                $dict[$text][$typeId] += 1;
            }
        }

        $dictDistinct = array();
        foreach ($dict as $text=>$types){
            arsort($types);
            $keys = array_keys($types);
            $dictDistinct[$text] = $keys[0];
        }
        return $dictDistinct;
    }

    function getDiscardedAnnotations($reportId, $annotationSetId){
        $annotations = DbAnnotation::getReportAnnotations(
            $reportId, null, array($annotationSetId), null, null,
            array(AnnotationStage::StageDiscarded));
        return $this->rowsToAnnotations($annotations);
    }

    function getNewAndFinalAnnotations($reportId, $annotationSetId){
        $annotations = DbAnnotation::getReportAnnotations(
            $reportId, null, array($annotationSetId), null, null,
            array(AnnotationStage::StageNew, AnnotationStage::StageFinal));
        return $this->rowsToAnnotations($annotations);
    }

    function rowsToAnnotations($rows){
	    $annotations = array();
	    foreach ($rows as $row){
	        $an = new TableReportAnnotation();
	        $an->assign($row);
	        $annotations[] = $an;
        }
	    return $annotations;
    }


}