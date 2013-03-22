<?php namespace Hw2;
S_Core::checkAccess();

/**
 *  extended variable implementation
 */
class S_Value {
    private $value;
    private $toMerge;
    
    public function __construct($value=null,$toMerge=false) {
        $this->value=$value;
        $this->toMerge=$toMerge;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function checkVal(S_Value $compare_val) {
        return $this===$compare_val;
    }
    
    public function toMerge() {
        return $this->toMerge==true;
    }
}

?>
