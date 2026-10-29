<?php

use engine\Config\Singleton as Singleton;

// klasy abstrakcyjnej nie da się testowac bezpośrednio, bo nie można
// utworzyć jej instancji. Musimy zrobić identyczną klasę rzeczywistą
class SingletonShadow extends Singleton\Singleton {};

class SingletonTest extends PHPUnit_Framework_TestCase {

    public function test_instanceOf() {

		$classname = "SingletonShadow";
		$obj = $classname::getInstance();
		$msg = "creating of ".$classname." class instance failed.";
		$this->assertTrue($obj instanceof Singleton\Singleton, $msg);

    } // test_instanceOf()

    public function test_reinstance() {
        $this->assertSame(SingletonShadow::getInstance(), SingletonShadow::getInstance());
    }

}
?>
