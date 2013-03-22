<?php namespace Hw2;
S_Core::checkAccess(true);

/*
 * DEPRECATED CLASS
 */

// SHARED CONTROLLER
class S_Controller extends S_Object {
    private $curPageRoute;
    private $router;
    private $gnClass;
    private $plg;
    private $plg_latest;
    private $plg_1_5;
    private $cb_latest;


    function __construct() {
            
    }
    
    public function getCurPageRoute() {
        return $this->curPageRoute;
    }
    
    public function setCurPageRoute($route) {
        $this->curPageRoute = $route;
    }
    
    function getPlugin(&$subject, $config = array()) {
        if (!isset($this->plg))
            $this->plg = new plgSystemHw2($subject, $config);
        
        return $this->plg;  
    }
    
    function getPlgLatest(&$subject, $config = array()) {
        if (!isset($this->plg_latest))
            $this->plg_latest = new S_jLatestPlugin($subject, $config);
        
        return $this->plg_latest;
    }
    
    function getPlg15(&$subject, $config = array()) {
        if (!isset($this->plg_1_5))
            $this->plg_1_5 = new S_j15Plugin($subject, $config);
        
        return $this->plg_1_5;
    }
}

?>
