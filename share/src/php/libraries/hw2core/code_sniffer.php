<?php namespace Hw2;
S_Core::checkAccess();

/** 
 *  this class is used to be injected inside other classes to retrieve
 *  private, protected or temporary data
 */
class S_CodeSniffer extends S_Object {
    private static $_instance;
    
    public function __construct() {
        parent::__construct(false, true);
    }
    
    /**
     * 
     * @return S_CodeSniffer
     */
    private static function _getInstance() {
        if (!self::$_instance)
            self::$_instance = parent::getInstance ();

        return self::$_instance;
    }
    
    public static function setObjVars(array &$args = null) {
        self::_getInstance()->_setObjVars($args);
    }
    
    public static function unsetData($name) {
        self::_getInstance()->_unsetData($name);
    }
    
    public static function getVal($name,$unset=false) {
        $val= self::_getInstance()->_getVal($name);
        if ($unset)
            self::_getInstance ()->unsetData ($name);
        return $val;
    }
}
?>
