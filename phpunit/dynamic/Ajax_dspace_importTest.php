<?php

mb_internal_encoding("UTF-8");
class Ajax_dspace_importTest extends PHPUnit_Framework_TestCase
{

    /**
     * @before
     */
	protected function setEnv() {
		$this->inforex_url = 'http://localhost/inforex/index.php';
    	$this->zip_path = '/home/czuk/wlw_ccl.zip';
	}

	private function post($name, $email, $path){
		$data = array(
					'ajax' => 'dspace_import',
					'name' => $name, 
					'email' => $email, 
					'path' => $path);
		
        $json = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->inforex_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		try {
		    $json = json_decode(curl_exec($ch));
		} catch (Exception $ex) {
			var_dump($ex);
		}
        curl_close ($ch);
		return $json;
	}
	
    public function testPostPathIsMissing()
    {
		$json = $this->post("Korpus", "marcinczuk@gmail.com", "");
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error_msg, '"PATH_IS_MISSING"');
    }

    public function testPostUserEmailIsMissing()
    {
		$json = $this->post("Korpus", "", "paczka.zip");				
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error_msg, '"USER_EMAIL_IS_MISSING"');
    }

    public function testPostCorpusNameIsMissing()
    {
		$json = $this->post("", "marcinczuk@gmail.com", "paczka.zip");				
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error_msg, '"CORPUS_NAME_IS_MISSING"');
    }

    public function testPostUserNotFound()
    {
		$json = $this->post("Korpus", "mail@gmail.com", "paczka.zip");
		$this->assertTrue(isset($json->error));
		$this->assertEquals($json->error_msg, '"USER_NOT_FOUND: mail@gmail.com"');
    }

    public function testPostOk()
    {
		$json = $this->post("Korpus", "admin", $this->zip_path);
		$this->assertFalse(isset($json->error)?$json->error:false);
		$this->assertTrue(isset($json->result->redirect));
		$this->assertEquals(substr($json->result->redirect, 0, 7), 'http://');
		//echo "\nRedirect: ".$json->result->redirect."\n";
    }

}
?>
