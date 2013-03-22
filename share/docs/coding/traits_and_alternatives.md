As of PHP 5.4.0, PHP implements a method of code reuse called Traits.
http://www.php.net/manual/it/language.oop5.traits.php

example:

trait foo {
    function getReturnType() { /*1*/ }
    function getReturnDescription() { /*2*/ }
}

class bar extends ReflectionMethod {
    use foo;
    /* ... */
}

----------------------

in hw2core an alternative to traits to create horizontal composition of behavior, compatible with old php version, 
is simply using methods in this way:

class S_ClassRegister extends S_CustomTrait {
    function addReg() { }
}

class Bar {
    // similar to USE keyword but you've to call clReg function to access trait methods
    // this will also avoid conficts
    function clReg() { return S_ClassRegister::I($this); }

    function foo() {
        $this->clReg()->addReg();
    }
}
