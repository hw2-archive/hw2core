<?php namespace Hw2;
S_Core::checkAccess();

class S_Data extends S_Object {
    public function __toString() {
        return $this->toString(S_StringFormat::ini);
    }
    
    public function __construct($strict = false) {
        parent::__construct($strict, true);
    }
}
?>
