<?php

require_once dirname(__FILE__) . '/../../../../../engine/include/database/CDbDocumentAnnotationCategory.php';

class CDbDocumentAnnotationCategoryTest extends PHPUnit_Framework_TestCase
{
    public function test_getAvailableAnnotationTree()
    {
        $dbEmu = new DatabaseEmulator();

        global $db;
        $db = $dbEmu;

        $corpusId = 12;
        $rows = array(
            array(
                'set_id' => 7,
                'set_name' => 'Named entities',
                'set_description' => 'Main entity layer',
                'subset_id' => 2,
                'subset_name' => 'location',
                'subset_description' => 'Location entities',
                'type_id' => 10,
                'type_name' => 'city',
                'type_description' => 'City mention',
            ),
            array(
                'set_id' => 7,
                'set_name' => 'Named entities',
                'set_description' => 'Main entity layer',
                'subset_id' => 2,
                'subset_name' => 'location',
                'subset_description' => 'Location entities',
                'type_id' => 11,
                'type_name' => 'country',
                'type_description' => 'Country mention',
            ),
            array(
                'set_id' => 8,
                'set_name' => 'Keywords',
                'set_description' => 'Keyword layer',
                'subset_id' => null,
                'subset_name' => null,
                'subset_description' => null,
                'type_id' => 15,
                'type_name' => 'keyword',
                'type_description' => 'Keyword category',
            ),
        );

        $dbEmu->setResponse(
            'fetch_rows',
            "SELECT ans.annotation_set_id AS set_id, ans.name AS set_name, ans.description AS set_description,        ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name, ansub.description AS subset_description,        at.annotation_type_id AS type_id, at.name AS type_name, at.short_description AS type_description FROM corpus_report_perspective_annotation_sets pas JOIN annotation_sets ans ON ans.annotation_set_id = pas.annotation_set_id JOIN annotation_types at ON at.group_id = ans.annotation_set_id LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id = at.annotation_subset_id WHERE pas.corpus_id = ? AND pas.perspective_id = ? ORDER BY ans.name, ansub.name, at.name",
            $rows
        );

        $result = DbDocumentAnnotationCategory::getAvailableAnnotationTree($corpusId);

        $expected = array(
            array(
                'id' => 7,
                'name' => 'Named entities',
                'description' => 'Main entity layer',
                'subsets' => array(
                    array(
                        'id' => 2,
                        'name' => 'location',
                        'description' => 'Location entities',
                        'types' => array(
                            array(
                                'id' => 10,
                                'name' => 'city',
                                'description' => 'City mention',
                            ),
                            array(
                                'id' => 11,
                                'name' => 'country',
                                'description' => 'Country mention',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'id' => 8,
                'name' => 'Keywords',
                'description' => 'Keyword layer',
                'subsets' => array(
                    array(
                        'id' => null,
                        'name' => 'Uncategorized',
                        'description' => null,
                        'types' => array(
                            array(
                                'id' => 15,
                                'name' => 'keyword',
                                'description' => 'Keyword category',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertEquals($expected, $result);
    }

    public function test_getDocumentAnnotationTypeIds()
    {
        $dbEmu = new DatabaseEmulator();

        global $db;
        $db = $dbEmu;

        $reportId = 33;
        $dbEmu->setResponse(
            'fetch_rows',
            "SELECT annotation_type_id FROM reports_document_annotation_types WHERE report_id = ? ORDER BY annotation_type_id",
            array(
                array('annotation_type_id' => '11'),
                array('annotation_type_id' => '15'),
            )
        );

        $this->assertSame(array(11, 15), DbDocumentAnnotationCategory::getDocumentAnnotationTypeIds($reportId));
    }
}
