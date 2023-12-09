<?php

mb_internal_encoding("UTF-8");
require_once("CorpusExporterTest.php");

class CorpusExporter_part0_Test extends CorpusExporterTest
{
// function parse_extractor($description){

    public function test_parse_extractor_throwsException_on_emptyText(){

        $emptyData = '';
        $ce = new CorpusExporter();
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'parse_extractor');

        try {
            $result = $protectedMethod->invokeArgs($ce,array($emptyData));
        } catch(Exception $e) {
            // expected Exception "Niepoprawny opis ekstraktora "
            $this->assertInstanceOf(\Exception::class,$e);
            $this->assertEquals("Niepoprawny opis ekstraktora ",$e->getMessage());
            return;
        }
        // no exception throwed on empty data
        $this->fail("Exception expected on empty data");

    } // test_parse_extractor_throwsException_on_emptyText

    public function test_parse_extractor_throwsException_on_ambigousText(){

        $syntacticallyImproperData = 'jakieś bzdury';
        $ce = new CorpusExporter();
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'parse_extractor');

        try {
            $result = $protectedMethod->invokeArgs($ce,array($syntacticallyImproperData));
        } catch(Exception $e) {
            // expected Exception "Niepoprawny opis ekstraktora "
            $this->assertInstanceOf(\Exception::class,$e);
            $this->assertEquals("Niepoprawny opis ekstraktora ".$syntacticallyImproperData,$e->getMessage());
            return;
        }
        // no exception throwed on empty data
        $this->fail("Exception expected on syntax errored data");

    } // test_parse_extractor_throwsException_on_ambigousText

    public function test_parse_extractor_throwsException_on_nonText(){

        $nonTextData = array();
        $ce = new CorpusExporter();
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'parse_extractor');

        try {
            $result = $result = $protectedMethod->invokeArgs($ce,array($emptyData));
        } catch(Exception $e) {
            // expected Exception "Niepoprawny opis ekstraktora "
            $this->assertInstanceOf(\Exception::class,$e);
            $this->assertEquals("Niepoprawny opis ekstraktora ",$e->getMessage());
            return;
        }
        // no exception throwed on empty data
        $this->fail("Exception expected on non text data");

    } // test_parse_extractor_throwsException_on_nonText

    public function test_parse_extractor()
    {
        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class

        global $db;
        $db = $dbEmu;

		$extractorDescription = "1_key_dg=3:annotations=annotation_set_ids#17;annotation_subset_ids#1,2;lemma_set_ids#1;lemma_subset_ids#2,3;attributes_annotation_set_ids#1;attributes_annotation_subset_ids#4,5;relation_set_ids#1;user_ids#70;stages#agreement";
		// zamieniamy nazwę flagi na duże litery, aby stestować lowercasing
		$extractorDescriptionwithUC = str_replace('dg','DG',$extractorDescription);

        $ce = new CorpusExporter();
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'parse_extractor');
        $result = $protectedMethod->invokeArgs($ce,array($extractorDescriptionwithUC));
/*
+        'flag_name' => '1_key_dg'
+        'flag_ids' => Array (...)
+        'name' => '1_key_dg=3:annotations=annota...ids#70'
+        'params' => Array (
+    'user_ids' => Array (...)
+    'annotation_set_ids' => Array (...)
+    'annotation_subset_ids' => null
+    'stages' => null
+    'relation_stages' => array()
+        'extractor' => Closure Object (...)
*/
        $this->assertTrue(is_array($result));
        $this->assertEquals(1,count($result));
        $this->assertTrue(is_array($result[0]));
        $this->assertEquals(5,count($result[0]));
        $this->assertEquals('1_key_dg',$result[0]["flag_name"]);
        $this->assertTrue(is_array($result[0]['flag_ids']));
        $this->assertEquals(array(3),$result[0]["flag_ids"]);
        $this->assertEquals($extractorDescription,$result[0]["name"]);
        $this->assertTrue(is_array($result[0]['params']));
        $this->assertEquals(10,count($result[0]['params']));
        $this->assertTrue(is_array($result[0]['params']['user_ids']));
        $this->assertEquals(array(70),$result[0]["params"]['user_ids']);
        $this->assertTrue(is_array($result[0]['params']['annotation_set_ids']));
        $this->assertEquals(array(17),$result[0]["params"]['annotation_set_ids']);
        $this->assertTrue(is_array($result[0]["params"]['annotation_subset_ids']));
		$this->assertEquals(array(1,2),$result[0]["params"]['annotation_subset_ids']);
        $this->assertTrue(is_array($result[0]["params"]['lemma_set_ids']));
        $this->assertEquals(array(1),$result[0]["params"]['lemma_set_ids']);
		$this->assertTrue(is_array($result[0]["params"]['lemma_subset_ids']));
		$this->assertEquals(array(2,3),$result[0]["params"]['lemma_subset_ids']);
		$this->assertTrue(is_array($result[0]["params"]['attributes_annotation_set_ids']));   
		$this->assertEquals(array(1),$result[0]["params"]['attributes_annotation_set_ids']);    
		$this->assertTrue(is_array($result[0]["params"]['attributes_annotation_subset_ids']));
		$this->assertEquals(array(4,5),$result[0]["params"]['attributes_annotation_subset_ids']);
		$this->assertTrue(is_array($result[0]["params"]['relation_set_ids']));
		$this->assertEquals(array(1),$result[0]["params"]['relation_set_ids']);
		$this->assertTrue(is_array($result[0]["params"]['stages']));
		$this->assertEquals(array('agreement'),$result[0]["params"]['stages']);
        //$this->assertNull($result[0]["params"]['stages']);
        $this->assertTrue(is_callable($result[0]['extractor']));
        $extractorFunc = $result[0]["extractor"];
        // function($report_id, $params, &$elements)
        $report_id = 1;
        // empty params -> no filters -> all data for document returned
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
'SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ?',
                            $allOptimizedAnnotationData 
        );
        $params = array(); // empty
        $funcResult = array('annotations'=>array(),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // empty answer template
        $extractorFunc($report_id,$params,$funcResult);
        $expectedResult = array('annotations'=>$allOptimizedAnnotationData,"relations"=>array(),"lemmas"=>array(),"attributes"=>array());
        $this->assertEquals($expectedResult,$funcResult);  

        // second test for non empty response environment
        $funcResult = array('annotations'=>array(array("id"=>1)),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // former result exists
        $extractorFunc($report_id,$params,$funcResult);
        // hmm, I don't know order in which array are merged. This may change..
        $expectedResult = array('annotations'=>array(array("id"=>1),$oneRowOptimizedAnnotationData),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // merged rows
        $this->assertEquals($expectedResult,$funcResult);

        // with params set - all fields must exists, all must be arrays
        $params = array("user_ids"=>array(70),"annotation_set_ids"=>array(12),"annotation_subset_ids"=>array(3),"stages"=>array('final')); 
        $dbEmu->setResponse("fetch_rows",
"SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ? AND at.group_id IN (12) AND at.annotation_subset_id IN (3) AND a.user_id IN (70) AND a.stage IN ('final')",
                            $allOptimizedAnnotationData
        );
        $funcResult = array('annotations'=>array(),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // empty answer template
        $extractorFunc($report_id,$params,$funcResult);
        $expectedResult = array('annotations'=>$allOptimizedAnnotationData,"relations"=>array(),"lemmas"=>array(),"attributes"=>array());
        $this->assertEquals($expectedResult,$funcResult);

        // some of the params field may be empty array
        $params = array("user_ids"=>array(70),"annotation_set_ids"=>array(12),"annotation_subset_ids"=>array(),"stages"=>array('final'));
        $dbEmu->setResponse("fetch_rows",
"SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ? AND at.group_id IN (12) AND at.annotation_subset_id IN () AND a.user_id IN (70) AND a.stage IN ('final')",
                            $allOptimizedAnnotationData
        );
        $funcResult = array('annotations'=>array(),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // empty answer template
        $extractorFunc($report_id,$params,$funcResult);
        $expectedResult = array('annotations'=>$allOptimizedAnnotationData,"relations"=>array(),"lemmas"=>array(),"attributes"=>array());
        $this->assertEquals($expectedResult,$funcResult);
        // some of the params field may be null
        $params = array("user_ids"=>array(70),"annotation_set_ids"=>array(12),"annotation_subset_ids"=>null,"stages"=>array('final'));
        $dbEmu->setResponse("fetch_rows",
"SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ? AND at.group_id IN (12) AND a.user_id IN (70) AND a.stage IN ('final')",
                            $allOptimizedAnnotationData
        );
        $funcResult = array('annotations'=>array(),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // empty answer template
        $extractorFunc($report_id,$params,$funcResult);
        $expectedResult = array('annotations'=>$allOptimizedAnnotationData,"relations"=>array(),"lemmas"=>array(),"attributes"=>array());
        $this->assertEquals($expectedResult,$funcResult);


        // Another canonical example
        $extractorDescription = "names (global)=3:annotation_set_id=1&annotation_set_id=20";
        $report_id = 1;

        // annotations data from database
        $type = 4;
        $from = 0; $to = 4;
        $text = 'tekst';
        $user_id = 1;
        $value = 'wartość własności';
        $ReturnedDataRow = array( "id"=>1, "report_id"=>$report_id, "type_id"=>$type, "type"=>'typ annotacji', "group"=>1, "from"=>$from, "to"=>$to, "text"=>$text, "user_id"=>$user_id, "creation_time"=>'2022-12-21 18:16:58', "stage"=>'final', "source"=>'auto', "prop"=>$value);
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
"SELECT ra.*, at.*, raa.annotation_id, raa.annotation_attribute_id, raa.`user_id` AS `attr_user_id`, raa.`value` AS `prop`  FROM reports_annotations ra LEFT JOIN annotation_types at ON (ra.type=at.name)  LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id)  WHERE ( ra.stage = 'final'  AND report_id IN ($report_id))   GROUP BY ra.id ORDER BY `from`",
            $allReturnedDataRows );



        $ce = new CorpusExporter();
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($ce,'parse_extractor');
        $result = $protectedMethod->invokeArgs($ce,array($extractorDescription));
/*
        var_dump($result);
  [0]=> array(5) {
    "flag_name" =>  "names (global)"
    "flag_ids"  =>  array("3")
    "name"      =>  "names (global)=3:annotation_set_id=1"
    "params"    =>  array("1")
    "extractor" => object(Closure)#20 (2) {...
  }
  [1]=> array(5) {
    "flag_name" =>  "names (global)"
    "flag_ids"  =>  array("3")
    "name"      =>  "names (global)=3:annotation_set_id=20"
    "params"    =>  array("20")
    "extractor" => object(Closure)#46 (2) {...
  }
*/
        $this->assertTrue(is_array($result));
        $this->assertEquals(2,count($result));
        $this->assertEquals("names (global)=3:annotation_set_id=1",$result[0]["name"]);
        $this->assertEquals("names (global)=3:annotation_set_id=20",$result[1]["name"]);
        $this->assertEquals(array(1),$result[0]['params']);
        $this->assertEquals(array(20),$result[1]['params']);
        for($i=0;$i<2;$i++){
            $this->assertTrue(is_array($result[$i]));
            $this->assertEquals(5,count($result[$i]));
            $this->assertEquals('names (global)',$result[$i]["flag_name"]);
            $this->assertTrue(is_array($result[$i]['flag_ids']));
            $this->assertEquals(array(3),$result[$i]["flag_ids"]);
            $this->assertTrue(is_callable($result[$i]['extractor']));
            $extractorFunc = $result[0]["extractor"];
            // function($report_id, $params, &$elements)
            // empty params -> no filters -> all data for document returned
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
            $dbEmu->clearAllResponses();
            $dbEmu->setResponse("fetch_rows",
'SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ?',
                            array($oneRowOptimizedAnnotationData)
        );
        $params = array(); // empty
        $funcResult = array('annotations'=>array(),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); // empty answer template
        $extractorFunc($report_id,$params,$funcResult);
        $expectedResult = array('annotations'=>array($oneRowOptimizedAnnotationData),"relations"=>array(),"lemmas"=>array(),"attributes"=>array()); 
 
        } // for $i


    } 

} // class

?>
