<?php

use engine\Config\Singleton as Singleton;

// klasy abstrakcyjnej nie da się testowac bezpośrednio, bo nie można
// utworzyć jej instancji. Musimy zrobić identyczną klasę rzeczywistą
class SingletonShadow extends Singleton\Singleton {};

class SingletonTest extends PHPUnit_Framework_TestCase {

    public function test_instanceOf() {

		$classname = "SingletonShadow";
		$this->obj = $classname::getInstance();
		$msg = "creating of ".$classname." class instance failed.";
		$this->assertTrue($this->obj instanceof Singleton\Singleton, $msg);

    } // test_instanceOf()

	public function test_reinstance() {

		$this->obj = SingletonShadow::getInstance();
		$magicToken = "ghghdsah";
		$this->obj->property=$magicToken;
		$localobj = SingletonShadow::getInstance();
		$msg = "Singleton class doesn't preserve static property. May be is not the same instance...";
		$this->assertSame($localobj->property,$magicToken,$msg);

	} // test_reinstance()

}
?>
