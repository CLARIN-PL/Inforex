<?php

class ReportAnnotator{

    var $reportId;
    var $report;
    var $html;
    var $errors;

    /**
     * ReportAnnotator constructor.
     * @param TableReport $report
     * @throws Exception
     */
    function __construct($report){
        $this->report = $report;
        $this->html = new HtmlStr2($this->report->getContent());
    }

    /**
     * @param array{phrase => annotationTypeId} $phrasesWithTypes
     * @param $annotationTypeId
     * @param string $stage
     * @return TableReportAnnotation[]
     */
    function findDisjoint($phrasesWithTypes, $userId, $stage=AnnotationStage::StageNew){
        $this->errors = array();
        $annotations = $this->findPhrasesMentions($phrasesWithTypes, $userId, $stage);
        $annotationsDisjoint = $this->selectDisjointSubset($annotations);
        return $annotationsDisjoint;
    }

    function findPhrasesMentions($phrasesWithTypes, $userId, $stage=AnnotationStage::StageNew){
        $annotations = array();
        foreach ($phrasesWithTypes as $phrase=>$annotationTypeId) {
            $new = $this->findPhraseMentions($phrase, $annotationTypeId, $userId, $stage);
            $annotations = array_merge($annotations, $new);
        }
        return $annotations;
    }

    /**
     * @param String $content
     * @param AnnotationEid $annotation
     * @param HtmlStr2 $html
     * @return TableReportAnnotation[]
     * @throws Exception
     */
    function findPhraseMentions($phrase, $annotationTypeId, $userId, $stage){
        $records = array();
        $matches = $this->findPhrasesInReportContent($phrase);
        foreach ($matches as $match){
            $begin = $this->html->rawToVisIndex($match[0]);
            $end = $this->html->rawToVisIndex($match[1]-1);
            $text = $this->html->getText($begin, $end);
            if ( $text != $phrase ){
                $visIndex = sprintf("[%s:%s]", $match[0], $match[1]-1);
                $this->logError("Phrases does not match $visIndex=>[$begin:$end] '$text' != '$phrase'");
                $this->errorCount++;
            } else {
                $record = new TableReportAnnotation();
                $record->setFrom($begin);
                $record->setTo($end);
                $record->setTypeId($annotationTypeId);
                $record->setText($text);
                $record->setUserId($userId);
                $record->setStage($stage);
                $record->setReportId($this->report->getId());
                $record->setSource(AnnotationSource::Bootstrapping);
                $records[] = $record;
            }
        }
        return $records;
    }

    function findPhrasesInReportContent($phrase){
        $text = $this->report->getContent();
        $spans = array();
        $pos = 0;
        $ret = 0;
        while ( is_integer($ret) ){
            $ret = mb_strpos($text, $phrase, $pos, "utf-8");
            if ( is_integer($ret) ) {
                $len = mb_strlen($phrase, "utf-8");
                $spans[] = array($ret, $ret + $len);
                $pos = $ret + $len;
            }
        }
        return $spans;
    }

    function selectDisjointSubset($annotations){
        usort($annotations, array("ReportAnnotator", "sortAnnotationsByLength"));
        $chars = array();
        $annotationSelected = array();
        foreach ($annotations as $annotation) {
            if ( self::countOverlappingCharacters($annotation, $chars) == 0 ) {
                $annotationSelected[] = $annotation;
                for ( $i=$annotation->getFrom(); $i<=$annotation->getTo(); $i++) {
                    $chars[$i] = 1;
                }
            }
        }
        return $annotationSelected;
    }

    function filterDuplicated($annotations, $refereceSet){
        $keyPattern = "%d_%d_%d";
        $keys = array();
        foreach ( $refereceSet as $an ){
            $key = sprintf($keyPattern, $an->getFrom(), $an->getTo(),  $an->getTypeId());
            $keys[$key] = 1;
        }
        $selected = array();
        foreach ($annotations as $an){
            $key = sprintf($keyPattern, $an->getFrom(), $an->getTo(),  $an->getTypeId());
            if ( !isset($keys[$key]) ){
                $selected[] = $an;
            }
        }
        return $selected;
    }

    function filterOverlapping($annotations, $refereceSet){
        $chars = array();
        foreach ($refereceSet as $annotation){
            for ( $i=$annotation->getFrom(); $i<=$annotation->getTo(); $i++) {
                $chars[$i] = 1;
            }
        }
        $selected = array();
        foreach ($annotations as $annotation){
            if ( self::countOverlappingCharacters($annotation, $chars) == 0 ) {
                $selected[] = $annotation;
            }
        }
        return $selected;
    }

    static function countOverlappingCharacters($annotation, $chars){
        $count = 0;
        for ( $i=$annotation->getFrom(); $i<=$annotation->getTo(); $i++) {
            if ( isset($chars[$i]) ) {
                $count++;
            }
        }
        return $count;
    }

    static function sortAnnotationsByLength($a, $b){
        if ( $a->getLength() == $b->getLength()) {
            return 0;
        } else {
            return $a->getLength() > $b->getLength() ? -1 : 1;
        }
    }

    /**
     * @param TableReportAnnotation[] $annotations
     * @param TableToken[] $tokens
     * @return TableReportAnnotation[]
     */
    static function filterNotAllignToTokens($annotations, $tokens){
        $starts = array();
        $ends = array();
        foreach ($tokens as $token){
            $starts[$token->getFrom()] = 1;
            $ends[$token->getTo()] = 1;
        }
        $filtered = array();
        foreach ($annotations as $an){
            if (isset($starts[$an->getFrom()]) && isset($ends[$an->getTo()])){
                $filtered[] = $an;
            }
        }
        return $filtered;
    }

    function logError($msg){
        $this->errors[] = $msg;
    }
}