<?php namespace Hw2;
S_Core::checkAccess();

class S_ClassRegister extends S_CustomTrait {
    
    public function __construct() {
        parent::__construct(false, true);
    } 
    
    /**
     * 
     * @param type $ownerClass
     * @return S_Register
     */
    public static function I($ownerClass) {
        return parent::I($ownerClass);
    }
    
    public function addCallBack($key,$class) {
        $this->_setVal($key, new S_Value($class,true));
    }
    
    /**
     * 
     * @param type $section 
     * @param type $type , type of methods to get: render etc..
     */
    public function getCallBacks($key) {
        $cbArray=Array();
        $vals=$this->_getObjVars();
        foreach ( $vals[$key] as $class) {
            $cbArray[]=$class;
        }
        return $cbArray;
    }
}
?>
