<?php
require_once __DIR__ . '/../../../../engine/ajax/ajax_metadata_batch_edit_update.php';

class MetadataBatchDatabaseStub extends Database {
    public $queries = array();
    public $ids = array(10, 11);
    public $fail = false;
    public function __construct() {}
    public function fetch_one($sql, $args = null) { return null; }
    public function fetch_rows($sql, $args = null) {
        return array_map(function($id) { return array('id' => $id); }, $this->ids);
    }
    public function execute($sql, $args = null) {
        $this->queries[] = array($sql, $args);
        if ($this->fail && strpos($sql, 'UPDATE') === 0) throw new Exception('Simulated write failure');
        return true;
    }
}

class MetadataBatchEditTest extends PHPUnit_Framework_TestCase {
    private $post;
    private $oldDb;
    private $oldCorpus;
    protected function setUp(): void {
        $this->post = $_POST;
        $this->oldDb = $GLOBALS['db'] ?? null;
        $this->oldCorpus = $GLOBALS['corpus'] ?? null;
        $GLOBALS['db'] = new MetadataBatchDatabaseStub();
        $GLOBALS['corpus'] = array('id' => 157);
        $_POST = array('corpus_id' => '157');
    }
    protected function tearDown(): void {
        $_POST = $this->post;
        $GLOBALS['db'] = $this->oldDb;
        $GLOBALS['corpus'] = $this->oldCorpus;
    }
    private function action() {
        return (new ReflectionClass(Ajax_metadata_batch_edit_update::class))->newInstanceWithoutConstructor();
    }
    public function test_json_batch_larger_than_input_variable_limit_is_complete() {
        $docs = array();
        for ($id=1; $id<=6000; $id++) $docs[$id.'_Title'] = array('value' => 'Synthetic '.$id);
        $GLOBALS['db']->ids = range(1,6000);
        $_POST['docs_json'] = json_encode($docs);
        $this->assertTrue($this->action()->execute());
        $this->assertCount(6002, $GLOBALS['db']->queries);
        $this->assertSame(array('COMMIT', null), end($GLOBALS['db']->queries));
        $this->assertSame(array('Synthetic 6000', 6000, 157), $GLOBALS['db']->queries[6000][1]);
    }
    public function test_legacy_request_is_supported_and_scoped() {
        $GLOBALS['db']->ids = array(10);
        $_POST['docs'] = array('10_Title' => array('value' => 'Test'));
        $this->assertTrue($this->action()->execute());
        $this->assertSame('UPDATE reports SET `title`=? WHERE id=? AND corpora=?', $GLOBALS['db']->queries[1][0]);
    }
    public function test_mismatched_corpus_cannot_use_another_corpus_permission() {
        $_POST['corpus_id'] = 999;
        $_POST['docs'] = array();
        $this->expectException(Exception::class);
        $this->action()->execute();
    }
    public function test_invalid_json_does_not_write() {
        $_POST['docs_json'] = '{broken';
        try { $this->action()->execute(); $this->fail('Expected rejection'); }
        catch (Exception $e) { $this->assertSame(array(), $GLOBALS['db']->queries); }
    }
    public function test_unknown_field_does_not_write() {
        $_POST['docs'] = array('10_title = NULL' => array('value' => 'Test'));
        try { $this->action()->execute(); $this->fail('Expected rejection'); }
        catch (Exception $e) { $this->assertSame(array(), $GLOBALS['db']->queries); }
    }
    public function test_cross_corpus_document_rolls_back_without_update() {
        $_POST['docs'] = array('999_Title' => array('value' => 'Test'));
        $GLOBALS['db']->ids = array();
        try { $this->action()->execute(); $this->fail('Expected rejection'); }
        catch (Exception $e) {
            $this->assertSame(array(array('START TRANSACTION',null),array('ROLLBACK',null)), $GLOBALS['db']->queries);
        }
    }
    public function test_database_failure_is_not_reported_as_success() {
        $_POST['docs'] = array('10_Title' => array('value' => 'Test'));
        $GLOBALS['db']->ids = array(10);
        $GLOBALS['db']->fail = true;
        try { $this->action()->execute(); $this->fail('Expected rejection'); }
        catch (Exception $e) {
            $this->assertSame('Simulated write failure', $e->getMessage());
            $this->assertSame(array('ROLLBACK',null), end($GLOBALS['db']->queries));
        }
    }
    public function test_manager_permission_is_still_required() {
        $a=$this->action();
        $a->anyCorpusRole=array(CORPUS_ROLE_MANAGER, CORPUS_ROLE_OWNER);
        $user=array('user_id'=>181,'role'=>array());
        $corpus=array('id'=>157,'role'=>array(181=>array(CORPUS_ROLE_MANAGER=>1)));
        $this->assertTrue($a->hasAccess($user,$corpus));
        $corpus['role'][181]=array(CORPUS_ROLE_READ=>1);
        $this->assertInstanceOf(AccessError::class,$a->hasAccess($user,$corpus));
    }
}
