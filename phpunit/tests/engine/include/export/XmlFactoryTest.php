<?php

use org\bovigo\vfs\vfsStream; // for vfsStream
mb_internal_encoding("UTF-8");

class XmlFactoryTest extends PHPUnit_Framework_TestCase
{

// public function exportToXmlAndRelxml($filePathWithoutExt,&$ccl,&$annotations,&$relations,&$lemmas,&$attributes) 

    public function testExporttoxmlandrelxmlCallSetdatatoexportMethod() {

        $filePathWithoutExt = '';
        $ccl = new CclDocument();
        $annotations = array();
        $relations = array();
        $lemmas = array();
        $attributes = array();

        $mockXmlFactory = $this->getMockBuilder(XmlFactory::class)
            -> disableArgumentCloning()
            -> setMethods(array('setDataToExport'))
            -> getMock();
        // call setDataToExport with params            
        $mockXmlFactory -> expects($this->once())
            ->method('setDataToExport')
            ->with($ccl,$annotations,$relations,$lemmas,$attributes);
            // returns modified $ccl by reference
           
        $mockXmlFactory->exportToXmlAndRelxml($filePathWithoutExt,$ccl,$annotations,$relations,$lemmas,$attributes);

    } // testExporttoxmlandrelxmlCallSetdatatoexportMethod()

// protected function setDataToExport(&$ccl,&$annotations,&$relations,&$lemmas,&$attributes) 


    public function testSetdatatoexportPutsLemmaIntoCcl() {

        $annotations = array();
        $relations = array();
        $lemmas = array(
                array($lemma1key => $lemma1data)
            );
        $attributes = array();

        $token = new CclToken();
        $token->from = $annotation_from; $token->to = $annotation_to;
        $sentence = new CclSentence();
        $sentence->addToken($token);
        $chunk = new CclChunk();
        $chunk->addSentence($sentence);
        $ccl = new CclDocument();
        $ccl->addChunk($chunk);
        $ccl->addToken($token);
        //var_dump($ccl->tokens);
        
        $xf = new XmlFactory();    
        $protectedMethod = TestAccessTools::createAccessToProtectedMethodOfClassObject($xf,'setDataToExport');
        $protectedMethod->invokeArgs($xf,array(&$ccl,&$annotations,&$relations,&$lemmas,&$attributes));

        // returns modified $ccl by reference
        // no errors
        $expectedErrors = array();
        //var_dump($ccl->errors);
        //$this->assertEquals($expectedErrors,$ccl->errors);

        //var_dump($ccl->char2token);
        //var_dump($ccl->tokens);


    } // 



} // XmlFactoryTest class

?>
