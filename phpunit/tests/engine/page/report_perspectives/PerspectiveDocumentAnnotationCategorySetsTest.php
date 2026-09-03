<?php

mb_internal_encoding("UTF-8");

require_once dirname(__FILE__) . '/../../../../../engine/include/database/CDbDocumentAnnotationCategory.php';
require_once dirname(__FILE__) . '/../../../../../engine/page/corpus_perspectives/PerspectiveDocument_annotation_category_sets.php';

class PerspectiveDocumentAnnotationCategorySetsTest extends PHPUnit_Framework_TestCase
{
    public function test_execute_sets_assigned_and_selected_sets()
    {
        $dbEmu = new DatabaseEmulator();

        global $db, $corpus;
        $db = $dbEmu;
        $corpus = array('id' => 12);

        $dbEmu->setResponse(
            'fetch_rows',
            'SELECT s.* FROM `annotation_sets` s JOIN `annotation_sets_corpora` sc USING (annotation_set_id) WHERE corpus_id = ? ORDER BY s.description',
            array(
                array('annotation_set_id' => 7, 'name' => 'Named entities', 'description' => 'Main entity layer'),
                array('annotation_set_id' => 8, 'name' => 'Keywords', 'description' => 'Keyword layer'),
            )
        );
        $dbEmu->setResponse(
            'fetch_rows',
            "SELECT pas.annotation_set_id, ans.name, ans.description FROM corpus_report_perspective_annotation_sets pas JOIN annotation_sets ans ON ans.annotation_set_id = pas.annotation_set_id WHERE pas.corpus_id = ? AND pas.perspective_id = ? ORDER BY ans.name, ans.annotation_set_id",
            array(
                array('annotation_set_id' => 7, 'name' => 'Named entities', 'description' => 'Main entity layer'),
            )
        );
        $dbEmu->setResponse(
            'fetch_one',
            'SELECT COUNT(*) FROM corpus_and_report_perspectives WHERE corpus_id = ? AND perspective_id = ?',
            1
        );

        $page = new CPage();
        $perspective = new PerspectiveDocument_annotation_category_sets($page);
        $perspective->execute();

        $this->assertTrue($page->get('documentAnnotationCategoryPerspectiveEnabled'));
        $this->assertEquals(
            array(
                array(
                    'annotation_set_id' => 7,
                    'name' => 'Named entities',
                    'description' => 'Main entity layer',
                    'selected_for_perspective' => true,
                ),
                array(
                    'annotation_set_id' => 8,
                    'name' => 'Keywords',
                    'description' => 'Keyword layer',
                    'selected_for_perspective' => false,
                ),
            ),
            $page->get('documentAnnotationCategoryPerspectiveSets')
        );
    }
}
