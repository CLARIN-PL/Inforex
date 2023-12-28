<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class XmlFactoryTest extends PHPUnit_Framework_TestCase
{
    private $xmlFactory = null;

    protected function setUp() {

        $this->xmlFactory = new XmlFactory();

    } // setUp()

// public function exportToXmlAndRelxml($filePathWithoutExt,&$ccl,&$annotations,&$relations,&$lemmas,&$attributes) 

    public function testExporttoxmlandrelxmlCallSetdatatoexportMethod() {

        $filePathWithoutExt = '';
        $annotations = array('anotacje');
        $relations = array('relacje');
        $lemmas = array('lematy');
        $attributes = array('atrybuty');

        $mockCcl = $this->getMockBuilder(CclExportDocument::class)
            -> disableArgumentCloning()
            -> disableOriginalConstructor()
            -> setMethods(array('setCclProperties'))
            -> getMock();
        // call setCclProperties with params            
        $mockCcl -> expects($this->once())
            ->method('setCclProperties')
            ->with($annotations,$relations,$lemmas,$attributes);
            // returns modified $ccl by reference
           
        $this->xmlFactory->exportToXmlAndRelxml($filePathWithoutExt,$mockCcl,$annotations,$relations,$lemmas,$attributes);

    } // testExporttoxmlandrelxmlCallSetdatatoexportMethod()

} // XmlFactoryTest class

?>
