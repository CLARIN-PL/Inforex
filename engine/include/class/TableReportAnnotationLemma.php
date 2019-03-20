<?php
class TableReportAnnotationLemma extends ATable
{

    var $_meta_table = "reports_annotations_lemma";
    var $_meta_key = "report_annotation_id";
    var $_meta_stmt = null;

    var $report_annotation_id = null;
    var $lemma = null;

    /**
     * @return null
     */
    public function getReportAnnotationId()
    {
        return $this->report_annotation_id;
    }

    /**
     * @param null $report_annotation_id
     */
    public function setReportAnnotationId($report_annotation_id)
    {
        $this->report_annotation_id = $report_annotation_id;
    }

    /**
     * @return null
     */
    public function getLemma()
    {
        return $this->lemma;
    }

    /**
     * @param null $lemma
     */
    public function setLemma($lemma)
    {
        $this->lemma = $lemma;
    }



}