<?php

final class HtmlStr2Test extends PHPUnit_Framework_TestCase {

    const testContent = 'a';

    public function testCanBeCreatedFromValidData()
    {
        $a = self::testContent;
        // $recognize_tags default setting
        $this->assertInstanceOf(
            "HtmlStr2",
            new HtmlStr2( $a ) 
        );
        // $recognize_tags True
        $this->assertInstanceOf(
            "HtmlStr2",
            new HtmlStr2( $a, True )
        );
        // $recognize_tags False
        $this->assertInstanceOf(
            "HtmlStr2",
            new HtmlStr2( $a, False )
        );
        
    } 

    public function testReturnedContentMatched() 
    {
        $a = self::testContent;
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                self::testContent,
                (new HtmlStr2( $a,$recognize_tags ))->getContent()
            );
        }
    }

    // new public funcionality tests

    private function demultiplexeResults($dualFormResult) 
    {
        // if $dualFormResult is array, contains results 
        //  for $recognize_tags = True, and False 
        // else multiple one value to array
        if(is_array($dualFormResult)){
            $arrayResults = array(
                True => $dualFormResult[0],
                False => $dualFormResult[1]
            );
        } else {
            $arrayResults = array(True=>$dualFormResult,False=>$dualFormResult);
        }
        return $arrayResults;
    }

    private function insertTag_test($content,$expectedResult,$from=0, $tag_begin="<tag>", $to=0, $tag_end="</tag>"){
        $expectedResults=$this->demultiplexeResults($expectedResult);
        foreach(array(True,False) as $recognize_tags) {
            foreach(array(True,False) as $force_insert) {
                $h = new HtmlStr2( $content,$recognize_tags );
                $h->insertTag($from, $tag_begin, $to, $tag_end, $force_insert);
                $this->assertEquals(
                    $expectedResults[$recognize_tags],
                    $h->getContent()
                );
            }
        }
    }

    private function getContent_test($content,$expectedResult) {
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResult,
                (new HtmlStr2( $content,$recognize_tags ))->getContent()
            );
        }        
    }

    private function getText_test($content,$expectedResult,$from=0,$to=null) {
        if($to===null){
            $to = mb_strlen($content,"UTF-8")-1;
        }
        $expectedResults=$this->demultiplexeResults($expectedResult);
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResults[$recognize_tags],
                (new HtmlStr2( $content,$recognize_tags ))->getText($from,$to)
            );
        }
    }

    private function getTextAlign_testSimple($content,$expectedResult,$options,$from=0, $to=null){
        if($to===null){
            $to = mb_strlen($content,"UTF-8")-1;
        }
        // $options must be associative array with cells for:
        // 'align_left':bool, 'aligh_right':bool, 'keep_tags':bool
        $align_left = $options["align_left"];
        $align_right = $options["align_right"];
        $keep_tags = $options["keep_tags"];
        $expectedResults=$this->demultiplexeResults($expectedResult);
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResults[$recognize_tags],
                (new HtmlStr2( $content,$recognize_tags ))->getTextAlign($from,$to,$align_left,$align_right,$keep_tags)
            );
        }
    }

    private function getTextAlign_test($content,$expectedResult,$from=0, $to=null){
        if($to===null){
            $to = mb_strlen($content,"UTF-8")-1;
        }
        $expectedResults=$this->demultiplexeResults($expectedResult);
        foreach(array(True,False) as $recognize_tags) {
            foreach(array(True,False) as $align_left) {
                foreach(array(True,False) as $align_right) {
                    foreach(array(True,False) as $keep_tags) {
                        $this->assertEquals(
                            $expectedResults[$recognize_tags],
                            (new HtmlStr2( $content,$recognize_tags ))->getTextAlign($from,$to,$align_left,$align_right,$keep_tags)
                        );
                    }
                }
            }
        } 
    }

    private function getSentencePos_test($content,$expectedResult,$pos_in_sentence=0){
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResult,
                (new HtmlStr2( $content,$recognize_tags ))->getSentencePos($pos_in_sentence)
            );
        }
    }

    private function getCharNumberBetweenPositions_test($content,$expectedResult,$pos1=0, $pos2=null){
        if($pos2===null){
            $pos2 = mb_strlen($content,"UTF-8")-1;
        }
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResult,
                (new HtmlStr2( $content,$recognize_tags ))->getCharNumberBetweenPositions($pos1,$pos2)
            );
        } 
    }

    private function isSpaceAfter_test($content,$expectedResult,$pos=0){
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResult,
                (new HtmlStr2( $content,$recognize_tags ))->isSpaceAfter($pos)
            );
        }
    }

    private function rawToVisIndex_test($content,$expectedResult,$rawIndex=0){
        $expectedResults=$this->demultiplexeResults($expectedResult);
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $expectedResults[$recognize_tags],
                (new HtmlStr2( $content,$recognize_tags ))->rawToVisIndex($rawIndex)
            );
        }
    }

    private function rawToVisIndexEmptyException_test($content,$rawIndex=0) {
        //  for empty content generate exception Undefined Offset
        // because rawToVisIndex() method doesn't validate parameter as
        // proper index existing in array. Just get data form this cell...
        $expectedResult = 0; // any for detect Exception
        foreach(array(True,False) as $recognize_tags) {
            try {
                (new HtmlStr2( $content,$recognize_tags ))->rawToVisIndex(0);
            } catch(Exception $e) {
                $this->assertEquals(
                    "Undefined offset: 0",
                    $e->getMessage()
                );
            }
        }
    }

    public function testEmptyDataHtml()
    {
        $a = "";
        // function insertTag($from, $tag_begin, $to, $tag_end, $force_insert=FALSE)
        $this->insertTag_test($a,"</tag><tag>");
        // getContent()
        $this->getContent_test($a,"");
        // getText($from,$to)
        $this->getText_test($a,"");
        // getTextAlign($from, $to, $align_left, $align_right, $keep_tags=false)
        $this->getTextAlign_test($a,"");
        // getSentencePos($pos_in_sentence)
        $this->getSentencePos_test($a,array(-1,-1));
        // getCharNumberBetweenPositions($pos1, $pos2)
        $this->getCharNumberBetweenPositions_test($a,0);
        // function isSpaceAfter($pos)
        $this->isSpaceAfter_test($a,False);
        // rawToVisIndex($rawIndex)
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneSpaceHtml()
    {
        $a = " ";
        $this->insertTag_test($a,"</tag> <tag>");
        $this->getContent_test($a," ");
        $this->getText_test($a,"",0,-1);
        $this->getTextAlign_test($a,"",0,-1);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,0,0,-1);
        $this->isSpaceAfter_test($a,False);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneAsciiCharHtml()
    {
        $a = "s";
        $this->insertTag_test($a,"</tag><tag>".$a);
        $this->getContent_test($a,$a);
        $this->getText_test($a,$a);
        $this->getTextAlign_test($a,$a);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,1);
        $this->isSpaceAfter_test($a,True);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneUTF8CharHtml()
    {
        $a = "ż";
        $this->insertTag_test($a,"</tag><tag>".$a);
        $this->getContent_test($a,$a);
        $this->getText_test($a,$a);
        $this->getTextAlign_test($a,$a);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,1);
        $this->isSpaceAfter_test($a,True);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneLexicalyConvertedCharHtml()
    {
        $a = json_decode('"\u00ad"'); // converted to hyphen
        $this->insertTag_test($a,"</tag><tag>"."-");
        $this->getContent_test($a,"-");
        $this->getText_test($a,"-");
        $this->getTextAlign_test($a,"-");
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,1);
        $this->isSpaceAfter_test($a,True);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneAmpersandCharHtml()
    {
        $a = "&";
        $this->insertTag_test($a,"</tag><tag>".$a);
        $this->getContent_test($a,$a);
        $this->getText_test($a,$a);
        $this->getTextAlign_test($a,$a);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,1);
        $this->isSpaceAfter_test($a,True);
        $this->rawToVisIndexEmptyException_test($a); 
    }

    public function testOneNamedEntityHtml()
    {
        $a = "&gt;";
        $this->insertTag_test($a,"</tag><tag>".$a);
        $this->getContent_test($a,$a);
        $this->getText_test($a,$a,0,0);
        $this->getTextAlign_test($a,$a,0,0);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,4,0,0);
        $this->isSpaceAfter_test($a,True);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneNumericEntityHtml()
    {
        $a = "&#777;";
        $this->insertTag_test($a,"</tag><tag>".$a);
        $this->getContent_test($a,$a);
        $this->getText_test($a,$a,0,0);
        $this->getTextAlign_test($a,$a,0,0);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,6,0,0);
        $this->isSpaceAfter_test($a,True);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testAllLexicalyConvertedCharsHtml()
    {
        $a =  json_decode('"\u200a"')        // HAIR SPACE
              .json_decode('"\u200b"')      // ZERO WIDTH SPACE
              .json_decode('"\u200d"')
              .json_decode('"\u00a0"')      // NO-BREAK SPACE
              .json_decode('"\u00ad"')      // SOFT HYPHEN
              .json_decode('"\uf02d"')      // SOFT HYPHEN
              .json_decode('"\ufeff"');     // ZERO WIDTH NO-BREAK SPACE
        $this->insertTag_test($a,"</tag>    <tag>-- ");
        $this->getContent_test($a,"    -- ");
        $this->getText_test($a,"--",0,1);
        $this->getTextAlign_test($a,"--",0,1);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,1,0,0);
        $this->isSpaceAfter_test($a,False);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testOneTagHtml()
    {
        $a = "<tag>";
        $this->insertTag_test($a,"</tag><tag>".$a);
        $this->getContent_test($a,$a);
        $this->getText_test($a,'',0,-1);
        $this->getTextAlign_test($a,'',0,-1);
        $this->getSentencePos_test($a,array(-1,-1));
        $this->getCharNumberBetweenPositions_test($a,0,0,-1);
        $this->isSpaceAfter_test($a,False);
        $this->rawToVisIndexEmptyException_test($a);
    }

    public function testSimpleHtml()
    {
        $a = "
<!DOCTYPE html>
<html>
<head>
    <title>Tytuł</title>
</head>
<body>
    encja: &gt; numeryczna: &#1023; polski UTF: ąć
</body>
</html>
            ";
        $this->insertTag_test($a,array(
            preg_replace('/Tytuł/','<r>Tytuł</r>',$a),
            preg_replace('/<!DOC/','<r><!DOC</r>',$a)
            ),0,'<r>',5,'</r>');
        $this->getContent_test($a,$a);
        $this->getText_test($a,array('encj','TYPE'),5,8);
        $this->getTextAlign_testSimple($a,array('encj','TYPE'),
            array('align_left'=>False,"align_right"=>False,"keep_tags"=>False),
            5,8);
        $this->getTextAlign_testSimple($a,array('encj','TYPE'),
            array('align_left'=>False,"align_right"=>False,"keep_tags"=>True),
            5,8);
        $this->getTextAlign_testSimple($a,array('encja:','TYPE'),
            array('align_left'=>False,"align_right"=>True,"keep_tags"=>False),
            5,8);
        $this->getTextAlign_testSimple($a,array('encja:','TYPE'),
            array('align_left'=>False,"align_right"=>True,"keep_tags"=>True),
            5,8);
        $this->getTextAlign_testSimple($a,array('encj','<!DOCTYPE'),
            array('align_left'=>True,"align_right"=>False,"keep_tags"=>False),
            5,8);
        $this->getTextAlign_testSimple($a,array('encj','<!DOCTYPE'),
            array('align_left'=>True,"align_right"=>False,"keep_tags"=>True),
            5,8);
        $this->getTextAlign_testSimple($a,array('encja:','<!DOCTYPE'),
            array('align_left'=>True,"align_right"=>True,"keep_tags"=>False),
            5,8);
        $this->getTextAlign_testSimple($a,array('encja:','<!DOCTYPE'),
            array('align_left'=>True,"align_right"=>True,"keep_tags"=>True),
            5,8);
        $this->getSentencePos_test($a,array(-1,-1),28);
        $this->getCharNumberBetweenPositions_test($a,4,5,8);
        $this->isSpaceAfter_test($a,False,5);
        $this->rawToVisIndex_test($a,array(8,62),78);
 
    }


    // old behaviour tests

    private function dumpState(HtmlStr2 $h) {
        $originalCharsCount = mb_strlen($h->getContent(), "UTF-8");
        $rawToVisIndex = array();
        for($i=0;$i<$originalCharsCount;$i++) {
            try {
                $rawToVisIndex[$i] = $h->rawToVisIndex($i);
            } catch(Exception $e) {
                // ignore errors. Any service for unproper index table
                // is not implemented in HtmlStr2->rawToVisIndex() :o(
            }
        }
        return array( $h->chars, $h->tags, $rawToVisIndex );
    }

    private function equalsInOut($in,$outChars,$outTags = null) {
        $outArray = array(
            False => $outChars,   // for $recognize_tags = False
            True  => $outTags ? $outTags : $outChars
        );
        foreach(array(True,False) as $recognize_tags) {
            $this->assertEquals(
                $outArray[$recognize_tags],
                $this->dumpState(new HtmlStr2( $in, $recognize_tags ))
            );
        }
    } // equalsInOut()

    public function testEmptyData()
    {
        $a = "";
        $expectedState = array( array(), 
                                array(
                                    array(
                                        new HtmlChar(''),
                                        new HtmlChar('')
                                    )
                                ),
                                array() 
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneSpace()
    {
        $a = " ";
        $expectedState = array( array(),
                                array(
                                    array(
                                        new HtmlChar(''),
                                        new HtmlChar($a),
                                        new HtmlChar('')
                                    )
                                ),
                                array(null)
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneAsciiChar()
    {
        $a = "s";
        $expectedState = array( array(
                                    new HtmlChar($a)
                                ),
                                array(
                                    array(
                                        new HtmlChar('')
                                    ),
                                    array(
                                        new HtmlChar('')
                                    )
                                ),
                                array( 0 => 0 )
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneUTF8Char()
    {
        $a = "ż";
        $expectedState = array( array(
                                    new HtmlChar($a)
                                ),
                                array(
                                    array(
                                        new HtmlChar('')
                                    ),
                                    array(
                                        new HtmlChar('')
                                    )
                                ),
                                array( 0 => 0 )
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneLexicalyConvertedChar()
    {
        $a = json_decode('"\u00ad"'); // converted to hyphen 
        $expectedState = array( array(
                                    new HtmlChar("-")
                                ),
                                array(
                                    array(
                                        new HtmlChar('')
                                    ),
                                    array(
                                        new HtmlChar('')
                                    )
                                ),
                                array( 0 => 0 )
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneAmpersandChar()
    {
        $a = "&"; 
        $expectedState = array( array(
                                    new HtmlChar($a)
                                ),
                                array(
                                    array(
                                        new HtmlChar('')
                                    ),
                                    array(
                                        new HtmlChar('')
                                    )
                                ),
                                array( 0 => 0 )
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneNamedEntity()
    {
        $a = "&gt;"; 
        $expectedState = array( array(
                                    new HtmlChar($a)
                                ),
                                array(
                                    array(
                                        new HtmlChar('')
                                    ),
                                    array(
                                        new HtmlChar('')
                                    )
                                ),
                                array( 0 => 0,null,null,null )
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testOneNumericEntity()
    {
        $a = "&#777;";
        $expectedState = array( array(
                                    new HtmlChar($a)
                                ),
                                array(
                                    array(
                                        new HtmlChar('')
                                    ),
                                    array(
                                        new HtmlChar('')
                                    )
                                ),
                                array( 0 => 0, null, null, null, null, null )
                                );
        $this->equalsInOut($a,$expectedState);
    }

    public function testAllLexicalyConvertedChars()
    {
        $a =  json_decode('"\u200a"')        // HAIR SPACE
              .json_decode('"\u200b"')      // ZERO WIDTH SPACE
              .json_decode('"\u200d"')
              .json_decode('"\u00a0"')      // NO-BREAK SPACE
              .json_decode('"\u00ad"')      // SOFT HYPHEN
              .json_decode('"\uf02d"')      // SOFT HYPHEN
              .json_decode('"\ufeff"');     // ZERO WIDTH NO-BREAK SPACE
        $expectedState = array( array(
                                    new HtmlChar('-'),
                                    new HtmlChar('-')
                                ),
                                array(
                                    array(
                                        new HtmlChar(''),
                                        new HtmlChar(' '),
                                        new HtmlChar(' '),
                                        new HtmlChar(' '),
                                        new HtmlChar(' '),
                                    ),
                                    array(),
                                    array(
                                        new HtmlChar(' '),
                                        new HtmlChar('')
                                    )
                                ),
                                array(
                                    4 => 0,
                                    5 => 1,
                                    0 => null,
                                    1 => null,
                                    2 => null,
                                    3 => null,
                                    6 => null
                                )
                        );
        $this->equalsInOut($a,$expectedState);
    }
 
    public function testOneTag()
    {
        $a = "<tag>";
        $expectedForChars = array(  array(
                                        new HtmlChar('<'),
                                        new HtmlChar('t'),
                                        new HtmlChar('a'),
                                        new HtmlChar('g'),
                                        new HtmlChar('>')
                                    ),
                                    array(
                                        array(
                                            new HtmlChar('')
                                        ),
                                        array(),
                                        array(),
                                        array(),
                                        array(),
                                        array(
                                            new HtmlChar('')
                                        )
                                    ),
                                    array(
                                        0 => 0, 
                                        1 => 1,
                                        2 => 2,
                                        3 => 3,
                                        4 => 4 
                                    )
                            );
        $expectedForTags  = array(  array(
                                    ),
                                    array(
                                        array(
                                            new HtmlChar(''),
                                            new XmlTagPointer(
                                                new HtmlTag("tag",IHtmlTag::HTML_TAG_OPEN,$a)
                                            ),
                                            new HtmlChar('')
                                        )
                                    ),
                                    array(
                                        null,null,null,null,null
                                    )
                            );
        $this->equalsInOut($a,$expectedForChars,$expectedForTags);
    }

}

?>
