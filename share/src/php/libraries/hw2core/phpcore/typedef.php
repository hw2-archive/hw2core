<?php namespace Hw2;
S_Core::checkAccess();

/**
 * yet another emulation of typedef or enum
 */
abstract class S_TypeDef extends S_Value {
    private static $_instances = array();

    /**
     * used to pass a value and check if present in enum/typedef
     * @param type $arg
     * @return null
     */
    public static function getObj($arg) {
        $class = get_called_class();
        $signClass = md5(serialize($class));
        
        foreach (self::$_instances[$signClass] as $func => $value )
                                if ($value->getValue()===$args)
                                    return $value;
                                
        return null;
    }
    
    /**
     * [TODO] FIX value auto assign, for now enter manually values of each function
     * @param null $args
     * @param type $duplicates
     * @return null
     */
    protected static function _($args=-1,$duplicates=false) {
        $ex = new \Exception();
        $trace = $ex->getTrace();
        $class = get_called_class();
        $calling = $trace[1]["class"];
        
        if ($class!=$calling)
            die("syntax error in ".$class."typedef");
        
        $funcName=$trace[1]["function"];
        $signClass = md5(serialize($class));
        $signFunc = md5(serialize($funcName));    

        if (empty(self::$_instances[$signClass][$signFunc])) {
                    if ($args>-1) {
                        if (!$duplicates && array_key_exists($signClass, self::$_instances)) {
                            foreach (self::$_instances[$signClass] as $func => $value )
                                if ($value->getValue()===$args)
                                    die("duplicates not permitted for:".$class."->".$funcName);
                        }
                    } else
                        $args=$funcName; // use function name as identifier
                    
                    try {
                        self::$_instances[$signClass][$signFunc] = new $class($args);
                    } catch (\RuntimeException $e) {
                        die('error: cannot create the instance');
                        return null;
                    }
        } 
                
        return self::$_instances[$signClass][$signFunc];
    }
}

?>
