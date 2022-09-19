<?php 

final class HtmlParser2Test extends PHPUnit_Framework_TestCase {

    const testTagName = 'TAG';
    const testTagType = HTML_TAG_SELF_CLOSE;
    const testTagStr  = '<TAG attr="attr"/>';

    const testContent = 'a';

    public function testCanBeCreatedFromValidData()
    {
        $a = self::testContent;
        $this->assertInstanceOf(
            "HtmlParser2",
            new HtmlParser2( $a ) 
        );
    } 

    public function testCanReturnArray()
    {
        $a = self::testContent;
        $recognizeTags = True;
        $this->assertTrue(is_array((new HtmlParser2( $a ))->getObjects(False)));
        $this->assertTrue(is_array((new HtmlParser2( $a ))->getObjects(True)));
    }

    public function testCanReturnArrayOfHtmlChar()
    {
        $a = self::testContent;
        $o = (new HtmlParser2( $a ))->getObjects(False);
        for($i=0;$i<count($o);$i++){
            $this->assertInstanceOf("HtmlChar",$o[$i]);
        }
    }

    public function testCanReturnObjects()
    {
        $a = self::testContent;
        $o = new HtmlParser2( $a );
        if(HtmlParser2::parsedByBuggyParser())
            $expectedResults = array('',$a,'');
        else
            $expectedResults = array($a);
        $o = $o->getObjects(False);
        for($i=0;$i<count($expectedResults);$i++){
            $this->assertEquals($expectedResults[$i],$o[$i]->toString());
        }
    }

    public function testCanReturnString()
    {
        $a = 'a<tag attr="attr">&#2234;ut&lt;</tag>';
        foreach( array(True,False) as $recognizeTags) {
            $str = '';
            $o = (new HtmlParser2( $a ))->getObjects($recognizeTags);
            foreach($o as $obj){
                $str .= $obj->toString();
            }
            $this->assertEquals($a,$str);
        } // $recognizetags true & false
    }


}

?>
