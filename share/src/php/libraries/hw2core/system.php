<?php namespace Hw2;
S_Core::checkAccess();

S_ShutdownScheduler::init();

class S_ShutdownScheduler {
    private static $callbacks=array(); // array to store user callbacks
    
    public static function init() {
        register_shutdown_function('Hw2\S_ShutdownScheduler::callRegisteredShutdown');
    }
    
    /**
     * 
     * @param string|array $callback string or array
     * @param type $atBegin
     * @return boolean
     */
    public static function registerShutdownEvent($callback,$atBegin=false) { 
        if (!is_array($callback))
            $callback=Array($callback);
        
        if (empty($callback)) {
            trigger_error('No callback passed to '.__FUNCTION__.' method', E_USER_ERROR);
            return false;
        }
        if (!is_callable($callback[0])) {
            trigger_error('Invalid callback passed to the '.__FUNCTION__.' method', E_USER_ERROR);
            return false;
        }
        if ($atBegin)
            array_unshift (self::$callbacks, $callback);
        else
            self::$callbacks[] = $callback;
        
        return true;
    }
    
    public static function callRegisteredShutdown() {
        foreach (self::$callbacks as $arguments) {
            $callback = array_shift($arguments);
            call_user_func_array($callback, $arguments);
        }
    }
}

?>
