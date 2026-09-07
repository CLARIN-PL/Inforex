<?php

require_once dirname(__FILE__) . '/../../../../engine/ajax/ajax_administration_annotation_attribute_bindings_get.php';
require_once dirname(__FILE__) . '/../../../../engine/ajax/ajax_administration_annotation_attribute_bindings_apply.php';

class AnnotationAttributeBindingsDatabaseStub extends Database
{
    public $fetchResponses = array();
    public $fetchRowsResponses = array();
    public $fetchOneResponses = array();
    public $queries = array();
    public $throwOnExecuteContaining = null;

    public function __construct()
    {
    }

    public function fetch($sql, $args = null)
    {
        $this->queries[] = array('fetch', $sql, $args);
        return array_shift($this->fetchResponses);
    }

    public function fetch_rows($sql, $args = null)
    {
        $this->queries[] = array('fetch_rows', $sql, $args);
        return array_shift($this->fetchRowsResponses);
    }

    public function fetch_one($sql, $args = null)
    {
        $this->queries[] = array('fetch_one', $sql, $args);
        return array_shift($this->fetchOneResponses);
    }

    public function execute($sql, $args = null)
    {
        $this->queries[] = array('execute', $sql, $args);
        if ($this->throwOnExecuteContaining !== null
            && strpos($sql, $this->throwOnExecuteContaining) !== false) {
            throw new RuntimeException('Simulated database failure');
        }
        return true;
    }
}

class AdministrationAnnotationAttributeBindingsTest extends PHPUnit_Framework_TestCase
{
    private $requestBackup;

    protected function setUp(): void
    {
        $this->requestBackup = $_REQUEST;
        $_REQUEST = array();
    }

    protected function tearDown(): void
    {
        $_REQUEST = $this->requestBackup;
    }

    public function test_get_returns_each_attribute_with_boolean_assignment_state()
    {
        $dbStub = new AnnotationAttributeBindingsDatabaseStub();
        $dbStub->fetchResponses[] = array(
            'annotation_type_id' => 279,
            'name' => 'action',
            'set_name' => 'TimeML',
            'subset_name' => 'EVENT',
        );
        $dbStub->fetchRowsResponses[] = array(
            array('id' => 2, 'name' => 'generality', 'type' => 'enum', 'description' => '', 'assigned' => '1'),
            array('id' => 3, 'name' => 'polarity', 'type' => 'enum', 'description' => '', 'assigned' => '0'),
        );

        $GLOBALS['db'] = $dbStub;
        $_REQUEST['annotation_type_id'] = 279;

        $result = $this->newAction(Ajax_administration_annotation_attribute_bindings_get::class)->execute();

        $this->assertTrue($result['attributes'][0]['assigned']);
        $this->assertFalse($result['attributes'][1]['assigned']);
        $this->assertStringContainsString('CASE WHEN EXISTS', $dbStub->queries[1][1]);
    }

    public function test_apply_replaces_bindings_and_deduplicates_input_ids()
    {
        $dbStub = new AnnotationAttributeBindingsDatabaseStub();
        $dbStub->fetchOneResponses = array(1, 2);

        $GLOBALS['db'] = $dbStub;
        $_REQUEST = array(
            'annotation_type_id' => 279,
            'shared_attribute_ids' => '2,3,3,invalid',
        );

        $result = $this->newAction(Ajax_administration_annotation_attribute_bindings_apply::class)->execute();

        $this->assertSame(array(2, 3), $result['shared_attribute_ids']);
        $this->assertSame(2, $result['binding_count']);
        $this->assertSame('START TRANSACTION', $dbStub->queries[2][1]);
        $this->assertStringContainsString('DELETE FROM annotation_types_shared_attributes', $dbStub->queries[3][1]);
        $this->assertSame(array(279, 2), $dbStub->queries[4][2]);
        $this->assertSame(array(279, 3), $dbStub->queries[5][2]);
        $this->assertSame('COMMIT', $dbStub->queries[6][1]);
    }

    public function test_apply_accepts_empty_selection()
    {
        $dbStub = new AnnotationAttributeBindingsDatabaseStub();
        $dbStub->fetchOneResponses = array(1);

        $GLOBALS['db'] = $dbStub;
        $_REQUEST = array(
            'annotation_type_id' => 279,
            'shared_attribute_ids' => '',
        );

        $result = $this->newAction(Ajax_administration_annotation_attribute_bindings_apply::class)->execute();

        $projected = array_map(function($query){ return $query[1]; }, $dbStub->queries);
        $this->assertSame(0, $result['binding_count']);
        $this->assertContains('START TRANSACTION', $projected);
        $this->assertContains('COMMIT', $projected);
    }

    public function test_apply_rolls_back_when_insert_fails()
    {
        $dbStub = new AnnotationAttributeBindingsDatabaseStub();
        $dbStub->fetchOneResponses = array(1, 1);
        $dbStub->throwOnExecuteContaining = 'INSERT INTO annotation_types_shared_attributes';

        $GLOBALS['db'] = $dbStub;
        $_REQUEST = array(
            'annotation_type_id' => 279,
            'shared_attribute_ids' => '2',
        );

        try {
            $this->newAction(Ajax_administration_annotation_attribute_bindings_apply::class)->execute();
            $this->fail('Expected simulated database failure');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated database failure', $exception->getMessage());
        }

        $lastQuery = end($dbStub->queries);
        $this->assertSame('ROLLBACK', $lastQuery[1]);
    }

    private function newAction($className)
    {
        $reflection = new ReflectionClass($className);
        return $reflection->newInstanceWithoutConstructor();
    }
}
