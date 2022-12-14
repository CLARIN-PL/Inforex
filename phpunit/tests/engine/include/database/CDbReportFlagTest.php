<?php

mb_internal_encoding("UTF-8");

class CDbReportFlagTest extends PHPUnit_Framework_TestCase
{

/*
   Zwraca wartości flag przypisanych wskazanemu dokumentowi.
   @return tablica asocjacyjna, której kluczem jest skrócona nazwa flagi zrzutowana do małych liter, a wartością identyfikator flagi (1,..,5)

    static function getReportFlags($report_id){
*/ 
    public function test_getReportFlags_returnsOneRowOfData()
    {
        // parameters
        $report_id = 1;

        // expected results
        $expectedResult = array( 'jeden' => 1 );

        // DB answers injected
        $dbEmu = new DatabaseEmulator();
        $ReturnedDataRow = array( "id"=>1, "short"=>'Jeden', "flag_id"=>1 );
        $allReturnedDataRows = array( $ReturnedDataRow );
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

        // do test...
        global $db;
        $db = $dbEmu;
        $result = DbReportFlag::getReportFlags($report_id);
        $this->assertTrue(is_array($result));
        $this->assertEquals($expectedResult,$result);
 
    } // test_getReportFlags_returnsOneRowOfData()

    public function test_getReportFlags_returnsMultiRowsOfData()
    {
        // parameters
        $report_id = 1;

        // expected results
        $expectedResult = array( 'jeden' => 1, 'dwa' => 2 );

        // DB answers injected
        $dbEmu = new DatabaseEmulator();
        $ReturnedDataRow = array( "id"=>1, "short"=>'jeden', "flag_id"=>1 );
        $ReturnedDataRow2 = array( "id"=>2, "short"=>'DWA', "flag_id"=>2 );
        $allReturnedDataRows = array( $ReturnedDataRow,$ReturnedDataRow2 );
        $dbEmu->setResponse("fetch_rows",
'SELECT cf.short, rf.flag_id FROM reports_flags rf  JOIN corpora_flags cf USING (corpora_flag_id) WHERE rf.report_id = ?',
            $allReturnedDataRows );

        // do test...
        global $db;
        $db = $dbEmu;
        $result = DbReportFlag::getReportFlags($report_id);
        $this->assertTrue(is_array($result));
        $this->assertEquals($expectedResult,$result);

    } // test_getReportFlags_returnsMultiRowsOfData()
 

} // class

?>
