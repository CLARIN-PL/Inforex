<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class CorpusExporterTest extends PHPUnit_Framework_TestCase
{

    private $virtualDir = null;

    protected function setUp() {

        $this->virtualDir = vfsStream::setup('root',null,[]);

    } // setUp()

    public function test_exportToCcl_createOutput()
    {
        $output_folder = $this->virtualDir->url(); 
        $selectors_description = array();
        $extractors_description = array(); // array of strings 
        $lists_description = array();

        $table =    array(
                        '1' => 'jeden'
                    );

        $mockCorpusExporter = $this->getMockBuilder(CorpusExporter::class)
            -> disableArgumentCloning()
            -> setMethods(array(
                'parse_extractor',
                'parse_lists',
                'getSubcorporaList',
                'export_document',
                'writeConsoleMessage'
                )) -> getMock();
        // przygotowuje listę wszystkich podkorpusów w postaci id=>nazwa
        // i ładuje do zmiennej $subcorpora
        $mockCorpusExporter -> expects($this->once())
            -> method('getSubcorporaList')
            ->will($this->returnValue($table));
        $mockCorpusExporter -> expects($this->never())
            -> method('parse_extractor');
        $mockCorpusExporter -> expects($this->never())
            -> method('parse_lists');
        $mockCorpusExporter -> expects($this->never())
            -> method('export_document');
        // write console msgs
        $mockCorpusExporter -> expects($this->at(1))
            -> method('writeConsoleMessage')
            -> with("Liczba dokumentów do eksportu: 0\n");
        $mockCorpusExporter -> expects($this->at(2))
            -> method('writeConsoleMessage')
            -> with("\n");

        $mockCorpusExporter->exportToCcl($output_folder,$selectors_description,$extractors_description,$lists_description);

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
