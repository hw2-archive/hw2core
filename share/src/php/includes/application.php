<?php namespace Hw2;

defined('HW2_CORE_EXEC') or die('Restricted access');

class S_Core {    
    private static $initialized=false;
    private static $_isBackend=false; // to use only internally
    private static $_isRoot=false;
    private static $_uri;
    
    /**
     *  the list of file must be created in order of inclusion priority
     */
    public static function init($argc,$argv) {         
        if (!self::$initialized) {             
            self::$initialized = true;
            if (S_Core::isCli()) {
                S_ApplicationCli::init($argc,$argv);
            } else {
                S_ApplicationWeb::init();
            }
        }        
    }
    
    /**
     * Call homonymous function from share and local environment 
     * @param type $class object or class name ( non-static / static )
     * @param type $method name of function to call
     * @param type $static is it static or not
     * @param array $params array of function parameter
     * @param type $sharePriority specify if we should call share or local before
     * 
     * @return Array the return values of callbacks in array elements 
     */
    public static function callGlobal($class,$method,Array $params=null,$sharePriority=true) {
        $static = is_string($class); // if we pass a string as class, we use static way 
        
        $clParts = explode('\\', (is_string($class) ? $class : get_class($class)));
        $namespace="";
        // if class includes namespace
        if (count($clParts)>1) {
            // get last element ( class )
            $class=$clParts[count($clParts) - 1];
            // remove latest element of array ( class ) and build the namespace string
            $namespace=  implode("\\", array_slice($clParts,0,-1)); 
        }
        
        $prefixes=Array("share"=>"S_", "local"=>"L_");
        // it's the name of the class with environment (S_/L_) prefix removed
        $globName=  strpos($class, $prefixes["share"])===0 || strpos($class, $prefixes["local"])===0?
                str_replace($prefixes, Array("",""), $class) : $class;
        
        if (!$sharePriority)
            array_reverse ($prefixes);
        
        $res=null;
        foreach ($prefixes as $prefix) {
            $cl=$namespace."\\".$prefix.$globName;
            if (!class_exists($cl)) {
                if ($prefix=="L_") // local method could also not be declared
                    continue;
                trigger_error ("class ".$cl." doesn't exists",E_USER_ERROR);
                die();
            }
            
            $callback=$static? $cl."::".$method : Array($cl,$method);
            $res[$prefix]=call_user_func($callback,$params);
            if ($res[$prefix]===false) {
                trigger_error (( $static ? "static" : "non-static")."method ".$method." doesn't exists",E_USER_ERROR);
                die();
            }
        }
        
        return $res;
    }
    
    public static function instantiateApp() {
        if (S_Core::isCli()) {
            S_ApplicationCli::getJCli();
        } else {
            S_ApplicationWeb::getJWeb();
        }
    }
     
    public static function isBackend() {
        return !IS_SITE;
    }
    
    /**
     *  check if the file is included inside the platform
     * @param bool $platform if defined, check platform access too ( hw2core not considered as platform in this case )
     * @param int $side ( 0 site, 1 backend, 2 both ) 
     */
    public static function checkAccess($platform = false, $side = 2) {
        if (!defined('HW2_CORE_EXEC') || (
                ( ( $platform && self::isCore() ) ||
                (!self::isBackend() && $side == S_SiteSide::admin )
                // when in root mode , we can access everywhere ( if core is running of course ) 
                ) && !self::isRoot() )) 
        {
            trigger_error('Restricted access',E_USER_ERROR);
            die();
        }
    }
    
    public static function setRoot($bool) {
        self::$_isRoot=$bool;
    }

    public static function isRoot() {
        return self::$_isRoot==true;
    }
    
    public static function isCore($checkParent=false) {
        return HW2_PLATFORM_NAME == S_PlatformList::hw2core && (!$checkParent || !self::checkParent());
    }

    public static function isCli() {
        return php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']);
    }    
    
    public static function checkParent() {
        $file=realpath(S_Paths::I()->get(S_Paths::HW2PATH_CORE).DS."..".DS."index.php");
        if (file_exists($file)) {
            $source=file_get_contents($file);
            // joomla case
            if (strstr($source, "'_JEXEC'")!==false) {
                return true;
            }
        }
        
        return false;
    }
}

?>

