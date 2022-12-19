<?php

mb_internal_encoding("UTF-8");

class CDbReportTest extends PHPUnit_Framework_TestCase
{

/*
    static function getReportById($report_id)
    // returns one row only !!! or empty array()

*/ 
    public function test_getReportById()
    {
        // parameters
        $report_id = 1;

        // DB answers injected
        $dbEmu = new DatabaseEmulator();

        $ReturnedDataRow = array(
            "id" => 1,
            "corpora" => 1,
            "date" => '2022-12-16',
            "title" => 'tytuł',
            "source" => 'source',
            "author" => 'author',
            "content" => 'tekst dokumentu',
            "type" => 1,
            "status" => 1,
            "user_id" => 1,
            "subcorpus_id" => 1,
            "tokenization" => 'tokenization',
            "format_id" => 1,
            "lang" => 'pol',
            "filename" => 'nazwa pliku',
            "parent_report_id" => null,
            "deleted" => 0
        );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
            'SELECT * FROM reports WHERE id = ?',
            $allReturnedDataRows );

        // do test...
        global $db;
        $db = $dbEmu;
        $result = DbReport::getReportById($report_id);
        $expectedResult = $ReturnedDataRow;
        $this->assertTrue(is_array($result));
        $this->assertEquals($expectedResult,$result);
 
    } // 

} // class

?>
