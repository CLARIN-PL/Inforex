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

    } // test_getReportAnnotations() 

    /*
        static function getAnnotationsBySets($report_ids=null, $annotation_layers=null, $annotation_names=null, $stage = null){
    */

    public function test_getAnnotationsBySets()
    {
        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class

        global $db;
        $db = $dbEmu;

        $oneRowOptimizedAnnotationData = array(
 // all fields from reports_annotations
 "id"                       =>8995317,
 "report_id"                =>1,
 "type_id"                  =>20,
 "type"						=>'nam_oth', 	// from annotation_types
 "group"					=>1,			// from annotation_types
 "from"                     =>0,
 "to"                       =>11,
 "text"                     =>'Uczestniczki',
 "user_id"                  =>203,
 "creation_time"            =>'2020-07-16 19:15:47',
 "stage"                    =>'agreement',
 "source"                   =>'user',
 // all fields from annotation_types
 "annotation_type_id"		=>20,
 "name"						=>'nam_oth',
 "description"				=>'Nazwy własne niezaklasyfikowane do pozostałych grup',
 "group_id"					=>1,
 "annotation_subset_id"		=>8,
 "level"					=>0,
 "short_description"		=>'',
 "css"						=>'background: lightgreen; border: 1px dashed red; border-bottom: 2px solid red;',
 "cross_sentence"			=>0,
 "shortlist"				=>0,
 // all fields from reports_annotations_attributes 
 "annotation_id"			=>null,
 "annotation_attribute_id"	=>null,
 "attr_user_id"				=>null,

 "prop"						=>null			// raa.value as prop
                                    );
        $allOptimizedAnnotationData = array (
            $oneRowOptimizedAnnotationData
        );
        $dbEmu->setResponse("fetch_rows",
"SELECT ra.*, at.*, raa.annotation_id, raa.annotation_attribute_id, raa.`user_id` AS `attr_user_id`, raa.`value` AS `prop`  FROM reports_annotations ra LEFT JOIN annotation_types at ON (ra.type=at.name)  LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id)  WHERE ( ra.stage = 'final'  AND report_id IN (1))   GROUP BY ra.id ORDER BY `from`",
                            $allOptimizedAnnotationData
        );

        $report_ids = array(1);
        $annotation_set_ids = null;
        $annotation_name_ids = null;
        $stage = 'final';

        $result = DbAnnotation::getAnnotationsBySets($report_ids,$annotation_set_ids,$annotation_name_ids,$stage);

        $this->assertTrue(is_array($result));
        // returns raw DB response
        $expectedResult = $allOptimizedAnnotationData;
        $this->assertEquals($expectedResult,$result);

	} // test_getAnnotationsBySets()

    // static function getAnnotationStructureByCorpora($corpus_id)
public function test_getAnnotationStructureByCorpora()
    {
        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class
        
        global $db;
        $db = $dbEmu;

        $corpus_id = 30;
        $allReturnedRows = array(
            // 2 rows for the same set/subset with different types
            array(  'set_id' => '17','set_name' => 'keywords','subset_id' => 54,'subset_name' => 'keywords','type_name' => 'keyword','type_id' => '371'),
            array(  'set_id' => '17','set_name' => 'keywords','subset_id' => 54,'subset_name' => 'keywords','type_name' => 'keyword first instance','type_id' => '476'),
            // row with set without subset
            array(  'set_id' => '15','set_name' => 'TimeML','subset_id' => NULL,'subset_name' => NULL,'type_name' => 'modality','type_id' => '558')
        );
        $dbEmu->setResponse("fetch_rows",
"SELECT ans.annotation_set_id AS set_id, ans.name AS set_name, ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name, at.name AS type_name, at.annotation_type_id AS type_id FROM annotation_types at LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id=at.annotation_subset_id LEFT JOIN annotation_sets ans ON ans.annotation_set_id=at.group_id LEFT JOIN annotation_sets_corpora ac ON ac.annotation_set_id=ans.annotation_set_id WHERE ac.corpus_id = ?",
                            $allReturnedRows
        );
        $result = DbAnnotation::getAnnotationStructureByCorpora($corpus_id);

        $this->assertTrue(is_array($result));
        // returns raw DB response
        $expectedStructure = array(
            17 => array('name' => 'keywords',   // set name
                        54 => array(            // subset id
                            'name' => 'keywords',   // subset name
                            371 => 'keyword',       // type_id/name pair
                            476 => 'keyword first instance' // 2nd one
                        )
                    ),
            15 => array('name' => 'TimeML') // w/o subtype
        );
        $this->assertEquals($expectedStructure,$result);

        
    } // test_getAnnotationStructureByCorpora()

} // class

?>
