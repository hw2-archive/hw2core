<?php namespace Hw2;
S_Core::checkAccess();

class S_jWebApp extends \Hwj\JApplicationWeb {
    protected function doExecute() {
        ob_start();
        S_Document::renderHead();
        $option=\Hw2\S_Uri::getInput()->get(S_CoreDef::hw2ext);
        S_Document::renderComponent($option);
        $this->setBody(ob_get_contents());
        ob_clean();
    }
}

class S_WebObject extends S_Object {
    /* @var $jWeb \Hwj\JApplicationWeb */  
    private static $jWeb;
    /**
     * 
     * @return \Hwj\JApplicationWeb
     */
    public static function getJWeb() {
        if (!self::$jWeb) {
            // first "base" instance has been created at jplatform load
            self::$jWeb=S_jWebApp::getInstance("Hw2\S_jWebApp");
            //self::$jWeb->initialise();
        }
        return self::$jWeb;
    }
}

class S_ApplicationWeb extends S_WebObject {
    
    public static function checkAdmin() {
        return S_PApi::isAdmin();
    }
    
    public static function init() {
        // Fix magic quotes.
        @ini_set('magic_quotes_runtime', 0);
    }
    
    public static function render() {
        ob_start();
        $jApp=self::getJWeb();
        $jApp->execute();
        return ob_get_clean();
    }
    
    public static function display() { 
        echo self::render();      
    }
}

?>
