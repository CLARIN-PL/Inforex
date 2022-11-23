<?php

mb_internal_encoding("UTF-8");

class CorpusExporterTest extends PHPUnit_Framework_TestCase
{
    public function test_exportToCcl_createOutput()
    {

        $table =    array(
                        '1' => 'jeden'
                    );

        $dbcorpus = $this->getMockBuilder('DbCorpus')->getMock();
        $dbcorpus->method('getSubcorpora')
            ->will($this->returnValue($table));

        $ce = new CorpusExporter($dbcorpus);
        //$result = $ce->exportToCcl('','','','');
        $this->assertEquals('',$result);

    }

} // class

?>
