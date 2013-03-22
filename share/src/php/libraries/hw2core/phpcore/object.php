<?php namespace Hw2;

/**
 *  to get an instantiated object you can use this method inside your class:
 *     
  public static function getInstance($arg1, $arg2) {
  return parent::getInstance(func_get_args());
  }
 */
Abstract class S_Object {

    private $_data = array();
    private static $_instances = array();
    private static $_init = array();
    private $_strict=false; // enable strict mode to avoid undefined var declaration/
    private $_useArray=false;
    private $_reflection;
    private $_protected=true;
    
    /**
     * 
     * @param type $strict disable for avoid unexcepted initializations
     * @param type $useArray
     * @param type $enableProtected if $useArray isn't enabled, you can choose to include protected variable for get/set functions
     */
    function __construct($strict=false,$useArray=false,$enableProtected=true) {
        $this->_strict=$strict;
        $this->_useArray=$useArray;
        $this->_reflection = new \ReflectionObject($this);
        $this->_protected=$enableProtected;
        $this->toString();
    }
    
    function __destruct() {
    }
    
    /* OVERLOADING METHODS */
    public function __get($name) {
        return $this->_getVal($name);
    }
    
    public function __set($name, $value) {
        return $this->_setVal($name, $value);
    }
    
    public function __isset($name) {
        ;
    }
    
    public function __unset($name) {
        ;
    }
    
    /**
     * generate getters and setters automatically
     * if we are calling a get/set function that doesn't exists
     * it will check that suffix is == a defined variable
     * 
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments) {
        $prefix=S_String::substr($name, 0,3);
        $var=  lcfirst(S_String::substr($name, 3));
        switch ($prefix) {
            case "set":
                return $this->_setVal($var, $arguments[0]);
            break;
            case "get":
                return $this->_getVal($var);
            break;
        }
        S_Exception::raise("\"".$name."\" function doesn't exist",  S_Exception_type::error());
    }
    
    public static function __callStatic($name, $arguments) {
        S_Exception::raise("\"".$name."\" function doesn't exist",  S_Exception_type::error());
    }
    
    /* disabled to avoid accidental calls
    public function __toString() {
        
        //return $this->toString(S_StringFormat::ini);
    } */
    
    public function toString($format=S_StringFormat::ini) {
        $vars=$this->_getObjVars();
        $string="";
        switch ($format) {
            case S_StringFormat::ini :
                foreach ($vars as $var => $value) {
                    $string.=$var."=".$value." \n ";
                }
            break;
            case S_StringFormat::json :
                // to implement
            break;
        }
        
        return $string;
    }
    
    /**
     * 
     * @return Array of vars
     */
    public function _getObjVars() {
        if ($this->_useArray)
            return $this->_data;
        else {
            $vars=Array();
            foreach ($this as $var=>$value) {
                if ($this->_checkVar($var))
                    $vars[$var]=$value;
            }
            return $vars;
        }
            
    }
    
    /**
     * model for: options and setters to handle unlimited constructor parameters
     * create variables and sets value
     * ex:
     * setValue(Array("var"=>"value))
     * setValue(Array($var1,$var2))
     * 
     * @return S_Object 
     */
    public function _setObjVars(Array &$args=null,$scope=false) {
        if (is_array($args)) // avoid warnings
            foreach ($args as $key => &$var) {
                $name = is_int($key) ? self::vname($var,  $scope) : $key;
                $this->_setVal($name, $var);
            }
        return $this; // make possible to create cascade setters
    }
    
    public function _setVal_R(&$var,$scope=false) {
        $name=self::vname($var,  $scope);
        $this->_setVal($name, $var);
        return $this;
    }
    
    public function _setVal($name,$value) {
        if ($this->_strict && !$this->_checkVar($name))
            S_Exception::raise("cannot create \"".$name."\" variable: strict mode enabled",  S_Exception_type::error());
        
        $f="set".ucfirst($name);
        if (method_exists($this,$f))
            $this->$f($value); // ex: setId($value); method to modify value before save
        else
            $this->_set($name,$value);
        return $this;
    }
    
    public function _unsetData($name) {
        if ($this->_strict && !$this->_checkVar($name))
            S_Exception::raise("cannot access \"".$name."\" variable", S_Exception_type::error());
        
        if ($this->_useArray)
            unset($this->_data[$name]);
        else
            unset($this->$name);
    }
    
    public function _getVal($name) {
        if ($this->_strict && !$this->_checkVar($name))
            S_Exception::raise("cannot access \"".$name."\" variable", S_Exception_type::error());
        
        return $this->_useArray ? $this->_data[$name] : $this->$name;
    }
    
   
    protected function _set($name,$value) {
        if ($this->_useArray) {
            if ($value instanceof S_Value) {
                if ($value->toMerge())
                    $this->_data[$name][]=$value->getValue ();
                else
                    $this->_data[$name]=$value->getValue ();
            } else
                $this->_data[$name]=$value;
        } else {
            if ($value instanceof S_Value) {
                if ($value->toMerge())
                    if(is_array($this->$name))
                        // if it's already an array, then just add it
                        $this->{$name}[]=$value->getValue ();
                    else
                        // else create the array
                        $this->$name=Array($this->$name,$value->getValue ());
                else
                    $this->$name=$value->getValue ();
            } else
                $this->$name=$value;
        }
    }
    
    /**
     * use when strict mode and useArray are enabled
     */
    private function createDataStruct() {
        $args=func_get_args();
        foreach ($args as $name)
            if (is_string ($name))
                $this->_data[$name]=null; // init with null value
    }

    private function _checkVar($var) {
        //$search= $this->_useArray ? $this->_data : get_class_vars(get_called_class());
        $filter=$this->_protected ? \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED : \ReflectionProperty::IS_PUBLIC;
        $properties=$this->_reflection->getProperties($filter);
        foreach ($properties as $p)
            if ($var==$p->name)
                return true;
            
        return false;
    }

    protected static function createInstance($name, Array $args,$namespace="hw2") {
        $name=$namespace."\\".$name;
        $s = empty($args) ? $name : $args;
        $signature = md5(serialize($s));

        if (empty(self::$_instances[$signature])) {
            try {
                if (!empty($args)) {
                    $r = new \ReflectionClass($name);
                    self::$_instances[$signature] = $r->newInstanceArgs($args);
                } else
                    self::$_instances[$signature] = new $name;
            } catch (RuntimeException $e) {
                die('error: cannot create the instance');
                return null;
            }
        }

        return self::$_instances[$signature];
    }

    /**
     * 
     * @param Array $arr pass the array of arguments, you can use func_get_args in extended class
     * @return type
     */
    public static function getInstance(Array $args=Array()) {
        return self::createInstance(self::cname(), $args,null); // namespace included in get_called_class
    }
    
    /**
     * Alternative getInstance using arg list instead array
     * @param array $args
     * @return type
     */
    public static function I() {
        return self::getInstance(func_get_args());
    }
    
    /**
     * this method give an instant access to class contructor 
     * in this way we can use php5.4 way to directly call methods after 
     * class contructor using this syntax:  class::C($args)->method();
     * @param array $args
     * @return \Hw2\S_Object
     */
    public static function Construct(Array $args=Array()) {
        $name=self::cname();
        $r = new \ReflectionClass($name);
        $c = $r->newInstanceArgs($args);
        return $c; 
    }
    
    /**
     * Alternative Construct using arg list instead array
     * @return \Hw2\S_Object
     */
    public static function C() {
        return self::Construct(func_get_args());
    }
    
    /**
     *  this function can be used to init static content of the class
     */
    public static function init() {
        
    }
    
    /**
     * Registry used to store already initialized classes
     * @param array $args
     * @return boolean
     */
    protected static function initReg(Array $args=null) {
        $signature = get_called_class().'_'.md5(serialize($args));
        if (array_key_exists($signature, self::$_init) && self::$_init[$signature]=="init")
            return true;
        
        self::$_init[$signature]="init";
        return false;
    }
    
    public static function vname(&$iVar, &$aDefinedVars = false, $bShowAllRef = false) {
        if (!$aDefinedVars)
            $vars = $GLOBALS;
        else if (is_object($aDefinedVars))
            $vars=get_object_vars($aDefinedVars);
        else
            $vars=$aDefinedVars;

        $iVarSave = $iVar;

        if ($bShowAllRef) {
            foreach ($aDefinedVars as $k => $v)
                $aDefinedVars_0[$k] = $v;

            $iVar = !$iVar;
            $aDiffKeys = array_keys(array_diff_assoc($aDefinedVars_0, $aDefinedVars));
        } else {
            $iVar = "unique".rand()."param";
            foreach ($aDefinedVars as $key => $val)
                if ($val === $iVar) {
                    $aDiffKeys = $key;
                    break;
                }
        }
        $iVar = $iVarSave;

        return $aDiffKeys;
    }
    
    /**
     * 
     * @return string Name of called class
     */
    public static function cname() {
        return get_called_class();
    }

}


?>
