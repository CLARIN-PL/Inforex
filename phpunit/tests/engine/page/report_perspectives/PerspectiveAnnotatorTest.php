<?php

mb_internal_encoding("UTF-8");

class PerspectiveAnnotatorTest extends PHPUnit_Framework_TestCase
{

    // below must be for testing execute method which use setcookie()
    // w/o we have 'headers already sent' error
    /**
     * @runInSeparateProcess
     */
    public function test_execute()
    {
        $dbEmu = new DatabaseEmulator();
        // set results emulation of querries external for class

        $user_id = 1;
        $report_id = 1;

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
        $allAnnotationTypesData = array();
        $dbEmu->setResponse("fetch_rows",
'SELECT t.*, s.name as `set` , ss.name AS subset , ss.annotation_subset_id AS subsetid , s.annotation_set_id AS groupid , t.shortlist AS common FROM annotation_types t JOIN annotation_sets_corpora c ON (t.group_id=c.annotation_set_id) JOIN annotation_sets s ON (s.annotation_set_id = t.group_id) LEFT JOIN annotation_subsets ss USING (annotation_subset_id) WHERE (c.corpus_id = 1 AND t.group_id IN (NULL)) ORDER BY `set`, subset, t.name',
                            $allAnnotationTypesData
        );

        $oneAnnotationTypesShortlistData = array ( 'annotation_type_id'=>510, 'user_id'=>$user_id, 'shortlist'=>1 );
        $secondAnnotationTypesShortlistData = array ( 'annotation_type_id'=>477, 'user_id'=>$user_id, 'shortlist'=>1 );
        $allAnnotationTypesShortlistData = array (
            $oneAnnotationTypesShortlistData,$secondAnnotationTypesShortlistData
        );
        $dbEmu->setResponse("fetch_rows",
'SELECT * FROM annotation_types_shortlist ats WHERE ats.user_id = ?',
                            $allAnnotationTypesShortlistData 
        );

        $allReportsAnnotationsOptimized=$allOptimizedAnnotationData;
        $dbEmu->setResponse("fetch_rows",
"SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename FROM reports_annotations_optimized a LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id) JOIN annotation_types at ON (a.type_id = at.annotation_type_id) LEFT JOIN users u ON (u.user_id = a.user_id) WHERE a.report_id = ? AND a.type_id IN (-1) AND a.stage IN ('final')",
                            $allReportsAnnotationsOptimized
        );
    
        $allTokensData = array();
        $dbEmu->setResponse("fetch_rows",
' SELECT  *  FROM tokens  LEFT JOIN orths USING (orth_id) WHERE report_id = ? ',
                            $allTokensData
        );

        $allAnnotationTypesData = array();
        $dbEmu->setResponse("fetch_rows",
"SELECT ans.annotation_set_id AS set_id, ans.name AS set_name, ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name, at.name AS type_name, at.annotation_type_id AS type_id FROM annotation_types at JOIN annotation_subsets ansub USING(annotation_subset_id) JOIN annotation_sets ans USING(annotation_set_id) LEFT JOIN annotation_sets_corpora ac USING(annotation_set_id) WHERE ac.corpus_id = ?",
                            $allAnnotationTypesData
        );

        $allReportsAnnotationsSharedAttributes = array();
        $dbEmu->setResponse("fetch_rows",
"SELECT * FROM reports_annotations_shared_attributes WHERE annotation_id = ?",
                            $allReportsAnnotationsSharedAttributes
        );

        $allRelationSets = array();
        $dbEmu->setResponse("fetch_rows",
'SELECT * FROM relation_sets rs                  JOIN corpora_relations cr ON cr.relation_set_id = rs.relation_set_id AND cr.corpus_id = ? ',
                            $allRelationSets
        );

        $emptyResponse = array();
        $dbEmu->setResponse("fetch_rows",
'SELECT DISTINCT event_groups.event_group_id, event_groups.name FROM corpus_event_groups JOIN event_groups ON (corpus_event_groups.corpus_id= AND corpus_event_groups.event_group_id=event_groups.event_group_id) JOIN event_types ON (event_groups.event_group_id=event_types.event_group_id)',
                            $emptyResponse 
        );
        $dbEmu->setResponse("fetch_rows",
'SELECT reports_events.report_event_id, event_groups.name AS groupname, event_types.name AS typename, event_types.event_type_id, count(reports_events_slots.report_event_slot_id) AS slots FROM reports_events JOIN reports ON (reports_events.report_id= AND reports_events.report_event_id=reports.id) JOIN event_types ON (reports_events.event_type_id=event_types.event_type_id) JOIN event_groups ON (event_types.event_group_id=event_groups.event_group_id) LEFT JOIN reports_events_slots ON (reports_events.report_event_id=reports_events_slots.report_event_id) GROUP BY (reports_events.report_event_id)',
                            $emptyResponse
        );

        $user_ids = array( $user_id );
        $annotation_set_ids = null;
        $annotation_subset_ids = null;
        $annotation_type_ids = null;
        $stages = array('final');

        $page = new CPage();
        $document = array('corpora'=>1);        

        $obj = new PerspectiveAnnotator($page,$document);
        // execute() returns void, only modifies $this->page attributes
        $obj->execute();

        $expectedAnnotation = array( $oneRowOptimizedAnnotationData );
        $expectedAnnotation[0]["attributes"] = '';
        $expectedPageSettings = array(
            'content' => '',
            'annotation_types' => array(),
            'relation_sets' => array(), 
            'annotations'   => $expectedAnnotation,
            'relations'     => array(),
            'annotation_mode'   => 'final',
            'active_accordion' => 'collapseConfiguration'
        );
        foreach(array_keys($expectedPageSettings) as $key) {
            $this->assertSame($expectedPageSettings[$key],$page->get($key),
                                "failed setting $key in CPage");
        }

    } 

} // class

?>
