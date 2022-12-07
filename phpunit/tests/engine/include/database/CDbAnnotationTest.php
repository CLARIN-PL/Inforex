<?php

mb_internal_encoding("UTF-8");

class CDbAnnotationTest extends PHPUnit_Framework_TestCase
{

/*
    static function getReportAnnotations($report_id,
                                         $user_ids=null,
                                         $annotation_set_ids=null,
                                         $annotation_subset_ids=null,
                                         $annotation_type_ids=null,
                                         $stages=null){
*/ 
    public function test_getReportAnnotations()
    {
        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class

        global $db;
        $db = $dbEmu;

        $oneRowOptimizedAnnotationData = array(
 "id"                       =>8995317,
 "report_id"                =>1,
 "type_id"                  =>20,
 "from"                     =>0,
 "to"                       =>11,
 "text"                     =>'Uczestniczki',
 "user_id"                  =>203,
 "creation_time"            =>'2020-07-16 19:15:47',
 "stage"                    =>'agreement',
 "source"                   =>'user',
 "type"                     =>'chunk_np',
 "group_id"                 =>7,
 "annotation_subset_id"     =>22,
 "lemma"                    =>null,
 "login"                    =>'anna.j.koch',
 "screename"                =>'anna.j.koch'
                                    );
        $allOptimizedAnnotationData = array (
            $oneRowOptimizedAnnotationData
        );
        $dbEmu->setResponse("fetch_rows",
"SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ? AND a.user_id IN (1) AND a.stage IN ('final')",
                            $allOptimizedAnnotationData
        );

        $report_id = 1;
        $user_ids = array( 1 );
        $annotation_set_ids = null;
        $annotation_subset_ids = null;
        $annotation_type_ids = null;
        $stages = array('final');

        $result = DbAnnotation::getReportAnnotations($report_id,$user_ids,$annotation_set_ids,$annotation_subset_ids,$annotation_type_ids,$stages);

        $this->assertTrue(is_array($result));
        // returns raw DB response
        $expectedResult = $allOptimizedAnnotationData;
        $this->assertEquals($expectedResult,$result);

    } 

} // class

?>
