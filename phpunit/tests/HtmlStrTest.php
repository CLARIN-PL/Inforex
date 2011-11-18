<?php

mb_internal_encoding("UTF-8");

include("../../engine/include/lib_htmlstr.php");

class HtmlStrTest extends PHPUnit_Framework_TestCase
{
    public function testGetText()
    {
    	$text = file_get_contents("../data/report001.xml");
    	
    	$hs = new HtmlStr($text);
    	
        $this->assertEquals('LICIU', $hs->getText(0, 4));
        $this->assertEquals('LESZEK', $hs->getText(153, 158));
    }

    public function testInsertTag()
    {
    	$text = file_get_contents("../data/report002.xml");
    	
    	$hs = new HtmlStr($text);
		$hs->insertTag(0, "<an>", 7, "</an>");
        $this->assertEquals(file_get_contents("../data/report002a.xml"), $hs->getContent());

    	$hs = new HtmlStr($text);
		$hs->insertTag(0, "<an>", 14, "</an>");
        $this->assertEquals(file_get_contents("../data/report002b.xml"), $hs->getContent());

    	$hs = new HtmlStr($text);
		$hs->insertTag(0, "<an>", 22, "</an>");
        $this->assertEquals(file_get_contents("../data/report002c.xml"), $hs->getContent());

    	$hs = new HtmlStr($text);
		$hs->insertTag(0, "<an>", 7, "</an>");
    	$hs = new HtmlStr($hs->getContent());
		$hs->insertTag(0, "<an>", 7, "</an>");
        $this->assertEquals(file_get_contents("../data/report002d.xml"), $hs->getContent());
    }

    public function testInsertTag2()
    {
    	$text = file_get_contents("../data/report001.xml");
    	
    	$hs = new HtmlStr($text);
		$hs->insertTag(153, "<an>", 158, "</an>");    	    	
    }


}
?>