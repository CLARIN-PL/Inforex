<?php

mb_internal_encoding("UTF-8");

class Ajax_dspace_importTest extends PHPUnit_Framework_TestCase
{
	
	private function post($name, $email, $path){
		$url = 'http://localhost/inforex/index.php';
		$data = array(
					'ajax' => 'dspace_import',
					'name' => $name, 
					'email' => $email, 
					'path' => $path);
		
		$r = new HttpRequest($url, HttpRequest::METH_POST);
		$r->addPostFields($data);
		try {
		    $json = json_decode($r->send()->getBody());
		} catch (HttpException $ex) {
			var_dump($ex);
		}
		return $json;
	}
	
    public function testPostPathIsMissing()
    {
		$json = $this->post("Korpus", "marcinczuk@gmail.com", "");				
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error, 'PATH_IS_MISSING');
    }

    public function testPostUserEmailIsMissing()
    {
		$json = $this->post("Korpus", "", "paczka.zip");				
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error, 'USER_EMAIL_IS_MISSING');
    }

    public function testPostCorpusNameIsMissing()
    {
		$json = $this->post("", "marcinczuk@gmail.com", "paczka.zip");				
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error, 'CORPUS_NAME_IS_MISSING');
    }

    public function testPostUserNotFound()
    {
		$json = $this->post("Korpus", "mail@gmail.com", "paczka.zip");
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error, 'USER_NOT_FOUND');
    }

    public function testPostOk()
    {
		$json = $this->post("Korpus", "marcinczuk@gmail.com", "paczka.zip");
		$this->assertFalse(isset($json->error));
		$this->assertTrue(isset($json->redirect));
		$this->assertEquals(substr($json->redirect, 0, 7), 'http://');
		echo "\nRedirect: ".$json->redirect."\n";
    }

}
?>
