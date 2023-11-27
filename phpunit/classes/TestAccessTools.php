<?php

class TestAccessTools {

    static function createAccessToProtectedMethodOfClassObject($classObject,$method) {
        // reflection for access to private method
        $protectedMethod = new ReflectionMethod($classObject,$method);
        $protectedMethod->setAccessible(True);
        return $protectedMethod;

    } // createAccessToProtectedMethodOfClassObject()

} // TestAccessTools()

?>
