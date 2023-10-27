<?php

mb_internal_encoding("UTF-8");
//use org\bovigo\vfs\vfsStream; // for vfsStream

class CclWriterTest extends PHPUnit_Framework_TestCase {

    private $virtualDir = null;
    private $fileName = null;

    private function xml2Dom($xml) {
        // converts plain xml text to compressed DOM array without
        // meaningless white chars. Will be used to compare xml export results.
        $xml = preg_replace("/\n\s*/","",$xml); // all beutyfier go out
        $dom = simplexml_load_string($xml,"SimpleXMLElement");
        if($dom===false){ // string is not "dom"-able
            return "String data are not in proper XML format !!!";
        } else {
            return $dom;
        }
    } // xml2Dom

    private function assertXmls($expectedXml,$resultXml) {
        // compare and assert two xml export results
        $this->assertEquals($this->xml2Dom($expectedXml),
                            $this->xml2Dom($resultXml));

    } // assertXmls()

    private function getXmlElement($xmlStr,$elementName){
        // return xml export DOM element
        return $this->xml2Dom($xmlStr)->{$elementName};
    } // getXmlElement()
    
    private function getXmlAttribute($xmlElement,$attributeName){
        // return attribute of SimpleXmlElement
        return $xmlElement->attributes()->{$attributeName};
    } // getXmlAttribute()

    private function getXmlElementAttribute($xmlStr,$elementName,$attributeName) {
        // return xml export DOM element atributte
        $element = $this->getXmlElement($xmlStr,$elementName);
        $attribute = $this->getXmlAttribute($element,$attributeName);
        return $attribute;
    } // getXmlElementAttribute()

	private function getExpectedXmlProlog() {

		return 
        '<?xml version="1.0" encoding="UTF-8"?>'."\n".
        '<!DOCTYPE chunkList SYSTEM "ccl.dtd">'."\n";

	} // getExpectedXmlProlog()

    private function getExpectedEmptyChunkList() {

        return $this->getExpectedXmlProlog()
                .'<chunkList>'."\n".'</chunkList>'."\n";

    } // getExpectedEmptyChunkList()

    private function getExpectedEmptyRelationList() {

        return $this->getExpectedXmlProlog()
                .'<relations>'."\n".'</relations>'."\n";

    } // getExpectedEmptyRelationList()

        // mocked data for simple tests

    private function createMockLexeme($disamb,$base,$ctag){

        $mockLexeme = $this->getMockBuilder(CclLexeme::class)
            -> setMethods(['getDisamb','getBase','getCtag'])
            -> getMock();
        $mockLexeme->method('getDisamb')->will($this->returnValue($disamb));
        $mockLexeme->method('getBase')->will($this->returnValue($base));
        $mockLexeme->method('getCtag')->will($this->returnValue($ctag));
        return $mockLexeme;

    } // createMockLexeme()

    private function createMockToken($orth,$ns=false,$prop=null,$lexemes=array(),$channels=array()){
        // 'from', 'to' property aren't used in XML export
        $mockToken = $this->getMockBuilder(CclToken::class)
            -> setMethods(['getOrth','getChannels','getLexemes'])
            -> getMock();
        //$mockToken->prop = array($tokenPropKey=>$tokenPropValue);
        $mockToken->prop = $prop; // should be array($key=>$val,...)
        $mockToken->ns = $ns;
        $mockToken->method('getOrth')->will($this->returnValue($orth));
        $mockToken->method('getChannels')->will($this->returnValue($channels));
        $mockToken->method('getLexemes')->will($this->returnValue($lexemes));
        return $mockToken;

    } // createMockToken()

    private function createMockSentence($id,$tokens=array()) {

        $mockSentence = $this->getMockBuilder(CclSentence::class)
            -> setMethods(['getId','getTokens'])
            -> getMock();
        $mockSentence->method('getId')->will($this->returnValue($id));
        $mockSentence->method('getTokens')->will($this->returnValue($tokens));
        return $mockSentence;

    } // createMockSentence()

    private function createMockChunk($id,$type,$sentences=array()) {

        $mockChunk = $this->getMockBuilder(CclChunk::class)
            -> setMethods(['getId','getType','getSentences'])
            -> getMock();
        $mockChunk->method('getId')->will($this->returnValue($id));
        $mockChunk->method('getType')->will($this->returnValue($type));
        $mockChunk->method('getSentences')->will($this->returnValue($sentences));
        return $mockChunk;

    } // createMockChunk()

    private function createMockCclRelation($name,$set,$fromSentence,$fromType,$fromChannel,$toSentence,$toType,$toChannel) {

        $mockRelation = $this->getMockBuilder(CclRelation::class)
            -> setMethods(array('getName','getSet','getFromSentence',
                                'getFromType','getFromChannel',
                                'getToSentence','getToType','getToChannel'))
            -> getMock();
        $mockRelation->method('getName')->will($this->returnValue($name));
        $mockRelation->method('getSet')->will($this->returnValue($set));
        $mockRelation->method('getFromSentence')->will($this->returnValue($fromSentence));
        $mockRelation->method('getFromType')->will($this->returnValue($fromType));
        $mockRelation->method('getFromChannel')->will($this->returnValue($fromChannel));
        $mockRelation->method('getToSentence')->will($this->returnValue($toSentence));
        $mockRelation->method('getToType')->will($this->returnValue($toType));
        $mockRelation->method('getToChannel')->will($this->returnValue($toChannel));
        return $mockRelation;

    } // createMockCclRelation()

    private function createMockCclDocument($chunks=array(),$relations=array()) {

        $mockCclDocument = $this->getMockBuilder(CclDocument::class)
            // wszystkie pozostałe metody są oryginalne
            -> setMethods(array('getChunks','getRelations'))
            -> getMock();
        $mockCclDocument->method('getChunks')->will($this->returnValue($chunks));
        $mockCclDocument->method('getRelations')->will($this->returnValue($relations));
        return $mockCclDocument;

    } // createMockCclDocument()

    private function generateFullCclData() {

		$mockChunk = $this->createMockChunk("1","TYP",array(
			$this->createMockSentence("1",array(
            	$this->createMockToken("To"),
            	$this->createMockToken("jest"),
            	$this->createMockToken("duże"),
            	$this->createMockToken("okno"),
            	$this->createMockToken(".",true)  // true sets ns in Token
            	)),
			$this->createMockSentence("2",array(
            	$this->createMockToken("Bardzo"),
            	$this->createMockToken("duże"),
            	$this->createMockToken(".",true) // true sets ns in Token    
            	))
			));
		$relations = array(
				$this->createMockCclRelation(
					'relName','relSet',
					'fromSentence','fromType','fromChannel',
					'toSentence','toType','toChannel') 
			);
        return $this->createMockCclDocument(
							array($mockChunk),
							$relations
						);

    } // generateFullCclData()

    private function generateExpectedXMLForFullCclData() {

        $expectedXml = $this->getExpectedXmlProlog()
            .'<chunkList>'."\n"
            .' <chunk id="1" type="TYP">'."\n"
            .'  <sentence id="1">'."\n"
            .'   <tok>'."\n"
            .'    <orth>To</orth>'."\n"
            .'   </tok>'."\n"
            .'   <tok>'."\n"
            .'    <orth>jest</orth>'."\n"
            .'   </tok>'."\n"
            .'   <tok>'."\n"
            .'    <orth>duże</orth>'."\n"
            .'   </tok>'."\n"
            .'   <tok>'."\n"
            .'    <orth>okno</orth>'."\n"
            .'   </tok>'."\n"
            .'   <tok>'."\n"
            .'    <orth>.</orth>'."\n"
            .'   </tok>'."\n"
			.'   <ns/>'."\n"
            .'  </sentence>'."\n"
            .'  <sentence id="2">'."\n"
            .'   <tok>'."\n"
            .'    <orth>Bardzo</orth>'."\n"
            .'   </tok>'."\n"
            .'   <tok>'."\n"
            .'    <orth>duże</orth>'."\n"
            .'   </tok>'."\n"
            .'   <tok>'."\n"
            .'    <orth>.</orth>'."\n"
            .'   </tok>'."\n"
			.'   <ns/>'."\n"
            .'  </sentence>'."\n"
            .' </chunk>'."\n"
			.' <relations>'."\n"
			.'  <rel name="relname" set="relSet">'."\n"
			.'    <from sent="fromSentence" chan="fromType">fromChannel</from>'."\n"
			.'    <to sent="toSentence" chan="toType">toChannel</to>'."\n"
			.'   </rel>'."\n"
			.' </relations>'."\n"
            .'</chunkList>'."\n";
        ;
        return $expectedXml;

    } // generateExpectedXMLForFullCclData() 

    protected function setUp() {

        $this->virtualDir = org\bovigo\vfs\vfsStream::setup('root',null,[]);
        $this->fileName = $this->virtualDir->url()."/test.txt";

    } // setUp()

    public function testOutputTextIsWrittenToFile() {

        $text = "jnduie773nd n";

        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','writeTextToFile');
        $privateMethod->setAccessible(True);
        $privateMethod->invoke(new CclWriter(),$this->fileName,$text);
        $result = file_get_contents($this->fileName);
        $this->assertEquals($text,$result);

    } // testOutputTextIsWrittenToFile()

    // static function write($ccl, $filename, $mode){...}
 
    // private function makeXmlData($ccl,$mode) {...}

    public function testCleanCclMakesOnlyXmlSkeleton() {

        // empty $ccl data generates PHP Fatal Error
        /*
        $ccl = null;
        $mode = CclWriter::$CCL;
        CclWriter::write($ccl, $this->filename, $mode);
        */

        $ccl = new CclDocument();
 
        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);
        
        $mode = CclWriter::$CCL;
        $expectedXml = $this->getExpectedEmptyChunkList();
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals($expectedXml,$result);

        $mode = CclWriter::$CCLREL;
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals($expectedXml,$result);

        $mode = CclWriter::$REL;
        $expectedXml = $this->getExpectedEmptyRelationList();
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals($expectedXml,$result);

    } // testCleanCclMakesOnlyXmlSkeleton()

    public function testCclWithEmptyChunkMakesOnlyXmlSkeletonWithAttributes() {

        $chunkId = '15';
        $chunkType = 'ChType';
        $mockChunk = $this->createMockChunk($chunkId,$chunkType);
        $ccl = $this->createMockCclDocument(array($mockChunk));

        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);

        $mode = CclWriter::$CCL;
        /*
        $expectedXml = $this->getExpectedXmlProlog()
            .'<chunkList>'."\n"
            .' <chunk id="'.$chunkId.'" type="'.$chunkType.'">'."\n"
            .' </chunk>'."\n"
            .'</chunkList>'."\n";
        */
        $expectedXml = $this->getExpectedXmlProlog().'<chunkList><chunk id="'.$chunkId.'" type="'.$chunkType.'"></chunk></chunkList>';
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertXmls($expectedXml,$result);
        $this->assertEquals($this->getXmlElement('<chunkList><chunk id="'.$chunkId.'" type="'.$chunkType.'"></chunk></chunkList>','chunk'),$this->getXmlElement($result,'chunk'));
        $this->assertEquals($this->getXmlElement($expectedXml,'chunk'),$this->getXmlElement($result,'chunk'));
        // element <chunk> is empty & has attributes properly set
        $this->assertEquals('',$this->getXmlElement($result,'chunk'));
        $this->assertEquals($chunkId,$this->getXmlElementAttribute($result,'chunk','id'));
        $this->assertEquals($chunkType,$this->getXmlElementAttribute($result,'chunk','type'));

        $mode = CclWriter::$CCLREL;
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertXmls($expectedXml,$result);
        // element <chunk> is empty & has attributes properly set
        $this->assertEquals('',$this->getXmlElement($result,'chunk'));
        $this->assertEquals($chunkId,$this->getXmlElementAttribute($result,'chunk','id'));
        $this->assertEquals($chunkType,$this->getXmlElementAttribute($result,'chunk','type'));

        $mode = CclWriter::$REL;
        $expectedXml = $this->getExpectedEmptyRelationList();
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals($expectedXml,$result);

    } // testCclWithEmptyChunkMakesOnlyXmlSkeletonWithAttributes()

    public function testCclWithEmptySentenceMakesSentenceSkeletonWithId() {

        $chunkId = '15';
        $chunkType = 'ChType';
		$sentenceId = '234';
        $mockSentence = $this->createMockSentence($sentenceId);
        $mockChunk = $this->createMockChunk($chunkId,$chunkType,array($mockSentence));
        $ccl = $this->createMockCclDocument(array($mockChunk));

        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);

        $mode = CclWriter::$CCL;
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        // element <chunk> has one sentence
        $this->assertEquals(1,count($this->getXmlElement($result,'chunk')->children()));
        $this->assertEquals('sentence',$this->getXmlElement($result,'chunk')->children()[0]->getName());
        // element <sentence> is empty & has attributes properly set
        $sentence = $this->getXmlElement($result,'chunk')->{'sentence'};
        $this->assertEquals('',$sentence);
        $this->assertEquals($sentenceId,$this->getXmlAttribute($sentence,'id'));

        $mode = CclWriter::$CCLREL;
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        // element <chunk> has one sentence
        $this->assertEquals(1,count($this->getXmlElement($result,'chunk')->children()));
        $this->assertEquals('sentence',$this->getXmlElement($result,'chunk')->children()[0]->getName());
        // element <sentence> is empty & has attributes properly set
        $sentence = $this->getXmlElement($result,'chunk')->{'sentence'};
        $this->assertEquals('',$sentence);
        $this->assertEquals($sentenceId,$this->getXmlAttribute($sentence,'id'));

        $mode = CclWriter::$REL;
        $expectedXml = $this->getExpectedEmptyRelationList();
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals($expectedXml,$result);

 	} // testCclWithEmptySentenceMakesSentenceSkeletonWithId()

    public function testCclInCclModeExportsAllTokensFields() {

		$mode = CclWriter::$CCL;
        $chunkId = '15';
        $chunkType = 'ChType';
        $sentenceId = '234';
		$tokenOrth	= 'token orth';
		$tokenPropKey  = 'token prop key';
        $tokenPropValue  = 'token prop value';   
        $tokenProp = array($tokenPropKey=>$tokenPropValue);
		$lexDisamb = true;
		$lexBase = 'base';
		$lexCtag = 'ctag';
		$channelType = 'chType';
		$channelNumber = 'chNumber';
		$tokenChannel = array($channelType=>$channelNumber);
        $mockLexeme = $this->createMockLexeme($lexDisamb,$lexBase,$lexCtag);
        $mockToken = $this->createMockToken($tokenOrth,false,$tokenProp,array($mockLexeme),$tokenChannel);
        $mockSentence = $this->createMockSentence($sentenceId,array($mockToken));
        $mockChunk = $this->createMockChunk($chunkId,$chunkType,array($mockSentence));
        $ccl = $this->createMockCclDocument(array($mockChunk));
        
        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);
 
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        // element <sentence> has one token element
        //  and one ns selftag <ns/> if property ns inside token is set
        $this->assertEquals(1,count($this->getXmlElement($result,'chunk')->{'sentence'}->children()));
        $this->assertEquals('tok',$this->getXmlElement($result,'chunk')->{'sentence'}->children()[0]->getName());
        // element <tok> has elements orth and array prop set
        $tok = $this->getXmlElement($result,'chunk')->{'sentence'}->{'tok'};
        $this->assertEquals('',$tok);
		// orth <orth>token orth</orth>
        $this->assertEquals($tokenOrth,$tok->{'orth'});
		// prop <prop key="token prop key">token prop value</prop>
        $prop = $tok->{'prop'};
        $this->assertEquals($tokenPropKey,$this->getXmlAttribute($prop,'key'));
        $this->assertEquals($tokenPropValue,$prop);
		// lexeme <lex disamb="1"><base>base</base><ctag>ctag</ctag></lex>
		$lexeme = $tok->{'lex'};
		$this->assertEquals('',$lex);
		$this->assertEquals('1',$this->getXmlAttribute($lexeme,'disamb'));
		$this->assertEquals($lexBase,$lexeme->{'base'});
		$this->assertEquals($lexCtag,$lexeme->{'ctag'});
		// channel <ann chan="chType">chNumber</ann>
		$this->assertEquals($channelType,$this->getXmlAttribute($tok->{'ann'},'chan'));
		$this->assertEquals($channelNumber,$tok->{'ann'});

        // if token->ns is true extra element after token is set
        $mockToken->ns = true;
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals(2,count($this->getXmlElement($result,'chunk')->{'sentence'}->children()));
        $this->assertEquals('ns',$this->getXmlElement($result,'chunk')->{'sentence'}->children()[1]->getName());

	} // testCclInCclModeExportsAllTokensFields()

    public function testCclInCclrelModeExportsAllTokensAndRelationsFields() {

        $mode = CclWriter::$CCLREL;
        $chunkId = '15';
        $chunkType = 'ChType';
        $sentenceId = '234';
        $tokenOrth  = 'token orth';
        $tokenPropKey  = 'token prop key';
        $tokenPropValue  = 'token prop value';
        $tokenProp = array($tokenPropKey=>$tokenPropValue);
        $lexDisamb = true;
        $lexBase = 'base';
        $lexCtag = 'ctag';
        $channelType = 'chType';
        $channelNumber = 'chNumber';
        $tokenChannel = array($channelType=>$channelNumber);
        $relName = 'relation name';
        $relSet = 'relation Set';
        $relFromSentence = 'from sentence';
        $relFromType = 'from type';
        $relFromChannel = 'from channel';
        $relToSentence = 'to sentence';
        $relToType = 'to type';
        $relToChannel = 'to channel';
        $mockLexeme = $this->createMockLexeme($lexDisamb,$lexBase,$lexCtag);
        $mockToken = $this->createMockToken($tokenOrth,false,$tokenProp,array($mockLexeme),$tokenChannel);
        $mockSentence = $this->createMockSentence($sentenceId,array($mockToken));
        $mockChunk = $this->createMockChunk($chunkId,$chunkType,array($mockSentence));
        $mockRelation = $this->createMockCclRelation($relName,$relSet,$relFromSentence,$relFromType,$relFromChannel,$relToSentence,$relToType,$relToChannel);
        $ccl = $this->createMockCclDocument(array($mockChunk),array($mockRelation));

        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);

        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        // element <sentence> has one token element
        //  and one ns selftag <ns/> if property ns inside token is set
        $this->assertEquals(1,count($this->getXmlElement($result,'chunk')->{'sentence'}->children()));
        $this->assertEquals('tok',$this->getXmlElement($result,'chunk')->{'sentence'}->children()[0]->getName());
        // element <tok> has elements orth and array prop set
        $tok = $this->getXmlElement($result,'chunk')->{'sentence'}->{'tok'};
        $this->assertEquals('',$tok);
        // orth <orth>token orth</orth>
        $this->assertEquals($tokenOrth,$tok->{'orth'});
        // prop <prop key="token prop key">token prop value</prop>
        $prop = $tok->{'prop'};
        $this->assertEquals($tokenPropKey,$this->getXmlAttribute($prop,'key'));
        $this->assertEquals($tokenPropValue,$prop);
        // lexeme <lex disamb="1"><base>base</base><ctag>ctag</ctag></lex>
        $lexeme = $tok->{'lex'};
        $this->assertEquals('',$lex);
        $this->assertEquals('1',$this->getXmlAttribute($lexeme,'disamb'));
        $this->assertEquals($lexBase,$lexeme->{'base'});
        $this->assertEquals($lexCtag,$lexeme->{'ctag'});
        // channel <ann chan="chType">chNumber</ann>
        $this->assertEquals($channelType,$this->getXmlAttribute($tok->{'ann'},'chan'));
        $this->assertEquals($channelNumber,$tok->{'ann'});
        // relations data
        // element <relations> has one rel element
        $this->assertEquals(1,count($this->getXmlElement($result,'relations')));
        // <rel> has two attributes and two children
        $rel = $this->getXmlElement($result,'relations')->{'rel'};
        $this->assertEquals($relName,$this->getXmlAttribute($rel,'name'));
        $this->assertEquals($relSet,$this->getXmlAttribute($rel,'set'));
        $this->assertEquals(2,count($rel->children()));
        $this->assertEquals('from',$rel->children()[0]->getName());
        $this->assertEquals('to',$rel->children()[1]->getName());
        // <from> has 2 attributes and value
        $from = $rel->{'from'};
        $this->assertEquals($relFromSentence,$this->getXmlAttribute($from,'sent'));
        $this->assertEquals($relFromType,$this->getXmlAttribute($from,'chan'));
        $this->assertEquals($relFromChannel,$from);
        // <to> has 2 attributes and value
        $to = $rel->{'to'};
        $this->assertEquals($relToSentence,$this->getXmlAttribute($to,'sent'));
        $this->assertEquals($relToType,$this->getXmlAttribute($to,'chan'));
        $this->assertEquals($relToChannel,$to);


        // if token->ns is true extra element after token is set
        $mockToken->ns = true;
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals(2,count($this->getXmlElement($result,'chunk')->{'sentence'}->children()));
        $this->assertEquals('ns',$this->getXmlElement($result,'chunk')->{'sentence'}->children()[1]->getName());
 
 	} // testCclInCclrelModeExportsAllTokensAndRelationsFields() 

    public function testCclInRelModeExportsAllRelationsFields() {

        $mode = CclWriter::$REL;

        $relName = 'relation name';
        $relSet = 'relation Set';
        $relFromSentence = 'from sentence';
        $relFromType = 'from type';
        $relFromChannel = 'from channel';
        $relToSentence = 'to sentence';
        $relToType = 'to type';
        $relToChannel = 'to channel';
        $mockRelation = $this->createMockCclRelation($relName,$relSet,$relFromSentence,$relFromType,$relFromChannel,$relToSentence,$relToType,$relToChannel);
        $ccl = $this->createMockCclDocument(array(),array($mockRelation));

        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);

        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
		// <relations>
		// <rel name="relation name" set="relation Set">
		//   <from sent="from sentence" chan="from type">from channel</from>
		//   <to sent="to sentence" chan="to type">to channel</to>
		//  </rel>
		// </relations>
		// element <relations> has one rel element
        $this->assertEquals(1,count($this->getXmlElement($result,'rel')));
		// <rel> has two attributes and two children
		$rel = $this->getXmlElement($result,'rel');
		$this->assertEquals($relName,$this->getXmlAttribute($rel,'name'));
		$this->assertEquals($relSet,$this->getXmlAttribute($rel,'set'));
		$this->assertEquals(2,count($rel->children()));
        $this->assertEquals('from',$rel->children()[0]->getName());
		$this->assertEquals('to',$rel->children()[1]->getName());
		// <from> has 2 attributes and value
		$from = $rel->{'from'};
		$this->assertEquals($relFromSentence,$this->getXmlAttribute($from,'sent'));
		$this->assertEquals($relFromType,$this->getXmlAttribute($from,'chan'));
		$this->assertEquals($relFromChannel,$from);
        // <to> has 2 attributes and value
        $to = $rel->{'to'};
        $this->assertEquals($relToSentence,$this->getXmlAttribute($to,'sent'));
        $this->assertEquals($relToType,$this->getXmlAttribute($to,'chan'));
        $this->assertEquals($relToChannel,$to);
 
	} // testCclInRelModeExportsAllRelationsFields()

    public function testFullCclDataProperlyFormatsAsXml() {

        $ccl = $this->generateFullCclData();

        // private method need reflection to tests
        $privateMethod = new ReflectionMethod('CclWriter','makeXmlData');
        $privateMethod->setAccessible(True);

        $mode = CclWriter::$CCLREL;  // for $CCLREL are both sections
        $expectedXml = $this->generateExpectedXMLForFullCclData();
        $result = $privateMethod->invoke(new CclWriter(),$ccl,$mode);
        $this->assertEquals($expectedXml,$result);

    } // testFullCclDataProperlyFormatsAsXml()

} // CclWriterTest class

?>
