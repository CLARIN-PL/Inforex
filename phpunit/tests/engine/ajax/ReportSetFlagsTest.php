<?php

require_once __DIR__ . '/../../../../engine/ajax/ajax_report_set_report_flags.php';

class ReportSetFlagsDatabaseStub extends Database {
    public $selected = array(array('id' => 10), array('id' => 11));
    public $writes = array();
    public $selectionArgs;

    public function __construct() {}

    public function fetch_rows($sql, $args = null) {
        $this->selectionArgs = $args;
        return $this->selected;
    }

    public function execute($sql, $args = null) {
        $this->writes[] = array(preg_replace('/\s+/', ' ', $sql), $args);
    }
}

class ReportSetFlagsTest extends PHPUnit_Framework_TestCase {
    private $oldPost;
    private $oldDb;
    private $oldUser;

    protected function setUp(): void {
        $this->oldPost = $_POST;
        $this->oldDb = $GLOBALS['db'] ?? null;
        $this->oldUser = $GLOBALS['user'] ?? null;
        $GLOBALS['db'] = new ReportSetFlagsDatabaseStub();
        $GLOBALS['user'] = array('user_id' => 185);
        $_POST = array();
    }

    protected function tearDown(): void {
        $_POST = $this->oldPost;
        $GLOBALS['db'] = $this->oldDb;
        $GLOBALS['user'] = $this->oldUser;
    }

    private function execute() {
        (new ReflectionClass(Ajax_report_set_report_flags::class))->newInstanceWithoutConstructor()->execute();
        return $GLOBALS['db']->writes;
    }

    public function test_subcorpus_only_does_not_attempt_to_insert_flag_zero() {
        $_POST = array('multiple' => 1, 'corpus_id' => 158, 'cflag_id' => '', 'flag_id' => '', 'subcorpus_id' => '7');
        $this->assertSame(array(
            array('UPDATE reports SET subcorpus_id = ? WHERE id= ?', array(7, 10)),
            array('UPDATE reports SET subcorpus_id = ? WHERE id= ?', array(7, 11))
        ), $this->execute());
        $this->assertSame(array(158, 185), $GLOBALS['db']->selectionArgs);
    }

    public function test_flag_only_keeps_the_subcorpus() {
        $_POST = array('multiple' => 1, 'corpus_id' => 158, 'cflag_id' => '8', 'flag_id' => '3');
        $this->assertSame(array(
            array('CALL changeFlagStatus(?,?,?,?)', array(8, 3, 10, 185)),
            array('CALL changeFlagStatus(?,?,?,?)', array(8, 3, 11, 185))
        ), $this->execute());
    }

    public function test_combined_operation_updates_both_fields_for_each_selected_document() {
        $_POST = array('multiple' => 1, 'corpus_id' => 158, 'cflag_id' => 8, 'flag_id' => 3, 'subcorpus_id' => 7);
        $this->assertSame(array(
            array('CALL changeFlagStatus(?,?,?,?)', array(8, 3, 10, 185)),
            array('UPDATE reports SET subcorpus_id = ? WHERE id= ?', array(7, 10)),
            array('CALL changeFlagStatus(?,?,?,?)', array(8, 3, 11, 185)),
            array('UPDATE reports SET subcorpus_id = ? WHERE id= ?', array(7, 11))
        ), $this->execute());
    }

    public static function incompleteChoices() {
        return array(array('', '', -1), array(8, '', -1), array('', 3, -1), array(0, 0, ''), array(8, 0, null));
    }

    /** @dataProvider incompleteChoices */
    public function test_unselected_or_partial_operations_do_not_change_anything($flag, $status, $subcorpus) {
        $_POST = array('multiple' => 1, 'corpus_id' => 158, 'cflag_id' => $flag, 'flag_id' => $status, 'subcorpus_id' => $subcorpus);
        $this->assertSame(array(), $this->execute());
    }

    public function test_empty_selection_does_not_change_anything() {
        $GLOBALS['db']->selected = array();
        $_POST = array('multiple' => 1, 'corpus_id' => 158, 'cflag_id' => 8, 'flag_id' => 3, 'subcorpus_id' => 7);
        $this->assertSame(array(), $this->execute());
    }

    public function test_single_flag_can_still_be_set() {
        $_POST = array('report_id' => 10, 'cflag_id' => 8, 'flag_id' => 3);
        $this->assertSame(array(array('CALL changeFlagStatus(?,?,?,?)', array(8, 3, 10, 185))), $this->execute());
    }

    public function test_single_flag_can_still_be_unset_with_both_sql_parameters_bound() {
        $_POST = array('report_id' => 10, 'cflag_id' => 8, 'flag_id' => 0);
        $this->assertSame(array(array('DELETE FROM reports_flags WHERE corpora_flag_id= ? AND report_id= ?', array(8, 10))), $this->execute());
    }
}
