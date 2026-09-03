<?php

mb_internal_encoding("UTF-8");

require_once dirname(__FILE__) . '/../../../../../engine/include/database/CDbDocumentAnnotationCategory.php';
require_once dirname(__FILE__) . '/../../../../../engine/page/report_perspectives/PerspectiveDocument_annotation_categories.php';

class PerspectiveDocumentAnnotationCategoriesTest extends PHPUnit_Framework_TestCase
{
    public function test_execute_sets_tree_and_selected_categories()
    {
        $dbEmu = new DatabaseEmulator();

        global $db;
        $db = $dbEmu;

        $dbEmu->setResponse(
            'fetch_rows',
            "SELECT ans.annotation_set_id AS set_id, ans.name AS set_name, ans.description AS set_description,        ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name, ansub.description AS subset_description,        at.annotation_type_id AS type_id, at.name AS type_name, at.short_description AS type_description FROM corpus_report_perspective_annotation_sets pas JOIN annotation_sets ans ON ans.annotation_set_id = pas.annotation_set_id JOIN annotation_types at ON at.group_id = ans.annotation_set_id LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id = at.annotation_subset_id WHERE pas.corpus_id = ? AND pas.perspective_id = ? ORDER BY ans.name, ansub.name, at.name",
            array(
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
            )
        );
        $dbEmu->setResponse(
            'fetch_rows',
            "SELECT annotation_type_id FROM reports_document_annotation_types WHERE report_id = ? ORDER BY annotation_type_id",
            array(
                array('annotation_type_id' => '10'),
            )
        );
        $dbEmu->setResponse(
            'fetch_rows',
            "SELECT rdat.annotation_type_id AS type_id, at.name AS type_name, at.short_description AS type_description,        ans.annotation_set_id AS set_id, ans.name AS set_name,        ansub.annotation_subset_id AS subset_id, ansub.name AS subset_name FROM reports_document_annotation_types rdat JOIN annotation_types at ON at.annotation_type_id = rdat.annotation_type_id JOIN annotation_sets ans ON ans.annotation_set_id = at.group_id LEFT JOIN annotation_subsets ansub ON ansub.annotation_subset_id = at.annotation_subset_id WHERE rdat.report_id = ? ORDER BY ans.name, ansub.name, at.name",
            array(
                array(
                    'type_id' => 10,
                    'type_name' => 'city',
                    'type_description' => 'City mention',
                    'set_id' => 7,
                    'set_name' => 'Named entities',
                    'subset_id' => 2,
                    'subset_name' => 'location',
                ),
            )
        );

        $page = new CPage();
        $document = array(
            'id' => 5,
            'corpora' => 12,
        );

        $perspective = new PerspectiveDocument_annotation_categories($page, $document);
        $perspective->execute();

        $this->assertEquals(
            array(
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
                            ),
                        ),
                    ),
                ),
            ),
            $page->get('document_annotation_category_tree')
        );
        $this->assertSame(array(10), $page->get('document_annotation_category_selected_ids'));
        $this->assertEquals(
            array(
                array(
                    'type_id' => 10,
                    'type_name' => 'city',
                    'type_description' => 'City mention',
                    'set_id' => 7,
                    'set_name' => 'Named entities',
                    'subset_id' => 2,
                    'subset_name' => 'location',
                ),
            ),
            $page->get('document_annotation_category_selected')
        );
    }
}
