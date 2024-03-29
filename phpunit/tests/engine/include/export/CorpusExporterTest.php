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

    public function test_arrayRemoveNullElements() {

        // on empty array, returns the same
        $inputArray = array(
                    );
        $result = CorpusExporter::arrayRemoveNullElements($inputArray);
        $this->assertEquals($inputArray,$result);

        // if none item has value null, return the same
        $inputArray = array( "jeden"=>5, "dwa"=>'something', 3=>array(),
                            '4th'=>0, "piąty"=>""
                    );
        $result = CorpusExporter::arrayRemoveNullElements($inputArray);
        $this->assertEquals($inputArray,$result);

        // some null elements - should be removed
        $inputArray = array( "jeden"=>null, "dwa"=>'something', 3=>NULL );
        $result = CorpusExporter::arrayRemoveNullElements($inputArray);
        $expectedValue = array("dwa"=>'something');
        $this->assertEquals($expectedValue,$result);

    } // test_arrayRemoveNullElements()

    public function test_arrayRemoveNullElements_recursively() {

        // testing if deep nest elements be removed
        $inputArray = array( "jeden"=>5, "dwa"=>'something', 
                            3=>array(
                                "jeden"=>null, "dwa"=>'something', 3=>NULL 
                            ),
                            '4th'=>0, "piąty"=>""
                    );
        $result = CorpusExporter::arrayRemoveNullElements($inputArray);
        $expectedValue = array( "jeden"=>5, "dwa"=>'something',
                            3=>array("dwa"=>'something'),
                            '4th'=>0, "piąty"=>""
                    );
        $this->assertEquals($expectedValue,$result);

	} // test_arrayRemoveNullElements_recursively()

} // CorpusExporterTest class

?>
