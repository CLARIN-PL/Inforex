<?php

use engine\Config\Singleton as Singleton;

class ConfigTest extends PHPUnit_Framework_TestCase {

    public function test_instanceOf() {

		$obj = Config::Cfg();
		$classname = "Config";	
		$msg = "creating of ".$classname." class instance failed.";
		$this->assertInstanceOf($classname, $obj, $msg);

    } // test_instanceOf()

	public function test_reinstance() {

		$this->obj = Config::Cfg();
		$magicToken = "ghghdsah";
		$this->obj->property=$magicToken;
		$localobj = Config::Cfg();
		$msg = "Config class doesn't preserve static property. May be is not the same instance...";
		$this->assertSame($localobj->property,$magicToken,$msg);

	} // test_reinstance()

	public function test_get() {

		$expectedValue = "gpw";
		$result = Config::Cfg()->get_sid();
		$msg = "Cannot read sid property from config";
		$this->assertSame($expectedValue,$result,$msg);

		// odczyt parametru o wartości null
		$expectedValue = null;
        $result = Config::Cfg()->get_federationLoginUrl();
        $msg = "Cannot read federationLoginUrl as null from config";
        $this->assertSame($expectedValue,$result,$msg);

		// odczyt nieistniejącego parametru
		$propertyName = "jakasnieistniejacanazwaparametru";
		try {
			$commandName = "get_".$propertyName;
			$result = Config::Cfg()->$commandName();
			// tu dojdzie jeśli nie zrzuci wyjątku
			$this->fail("Didn't generate exception on read unexistent property ".$propertyName."from Config");
		} catch ( ConfigException $e ) {
			$expectedMessage = "Parameter '".$propertyName."' not defined in the configuration file.";
			$msg = "Unproper message for exception in Config get_".$propertyName."()";
			$this->assertSame($e->getMessage(),$expectedMessage,$msg);
		}

	} // test_get()

	public function test_put() {
	
		$expectedValue = "cośtam";
		$propertyName = "takanazwacojejjeszczeniema";
		$commandName = "put_".$propertyName;
		Config::Cfg()->$commandName($expectedValue);
		$commandName = "get_".$propertyName;
		$result = Config::Cfg()->$commandName();
        $msg = "Cannot write ".$propertyName." property to config";
        $this->assertSame($expectedValue,$result,$msg);

		// test zapisu parametru o wartości null
        $expectedValue = null;
        $propertyName = "zmiennaowartoscinull";
        $commandName = "put_".$propertyName;
        Config::Cfg()->$commandName($expectedValue);
        $commandName = "get_".$propertyName;
        $result = Config::Cfg()->$commandName();
        $msg = "Cannot write ".$propertyName." property as null to config";
        $this->assertSame($expectedValue,$result,$msg);

	} // test_put()

    public function test_update() {

        $expectedValue = "cośtam";
        $propertyName = "takanazwacojejjeszczeniema";
        $commandName = "put_".$propertyName;
        Config::Cfg()->$commandName("firstvalue");
		// teraz aktualizacja wartości istniejącego parametru
		Config::Cfg()->$commandName($expectedValue);
		// i odczyt tego co tam jest aktualnie
        $commandName = "get_".$propertyName;
        $result = Config::Cfg()->$commandName();
        $msg = "Cannot write ".$propertyName." property from config";
        $this->assertSame($expectedValue,$result,$msg);

    } // test_update()

	public function test_loadConfigFromFile() {

        $virtualDir = org\bovigo\vfs\vfsStream::setup('root',null,[]);
		$expectedValue = "cośtam";
        $propertyName = "takanazwacojejjeszczeniema";
        $commandName = "get_".$propertyName;

		// najpierw wg. starego stylu configa
		// generate test file
		$fileName = $virtualDir->url()."/oldConfigTest.php";
		$configLine = "<?php ".'$config'."->".$propertyName." = '".$expectedValue."'; ?>";
		file_put_contents($fileName,$configLine);
		
		// loadOldLocalConfig jest funkcja prywatną klasy Config
		// aby sprowokować jej wykonanie nalezy ustawić zmienną
		// konfiguracyjną localConfigFilename, na nazwę pliku,
		// i wtedy klasa Config próbuje załadować ten plik zaraz po
		// ustawieniu zmiennej, najpierw wg. starego stylu, a jeśli
		// to skończy się wyjątkiem, to według nowego:
		Config::Cfg()->put_localConfigFilename($fileName);
		// sprawdzamy, czy parametr został dodany prawidłowo
		$result = Config::Cfg()->$commandName();
        $msg = "Error reading old config file ".$fileName;
        $this->assertSame($expectedValue,$result,$msg);

		// teraz wg. nowej synataktyki pliku
		$expectedValue = "cośinnego";
		$configLine = "<?php ".'$config'."->put_".$propertyName."('".$expectedValue."'); ?>";
        file_put_contents($fileName,$configLine);

		// wymusza przeładowanie pliku configa
		Config::Cfg()->put_localConfigFilename($fileName);
        // sprawdzamy, czy parametr został dodany prawidłowo
        $result = Config::Cfg()->$commandName();
        $msg = "Error reading old config file ".$fileName;
        $this->assertSame($expectedValue,$result,$msg);

		// skasowanie pliku lokalnego configa po teście
		//unlink($fileName);    // przy wirtualnym filesystemie niepotrzebne

	} // test_loadConfigFromFile()

	public function test_dumpConfigSets() {

		$result=Config::Cfg()->dumpConfigSets();
		//var_dump($result);
		$msg = "dumpConfigSets doesn't return array";
		$this->assertTrue(is_array($result),$msg);

	} // test_dumpConfigSets()

}
?>
