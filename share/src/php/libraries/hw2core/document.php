<?php namespace Hw2;
S_Core::checkAccess();
        
class S_Document extends S_Object {
    public static function renderHead($display=true) { 
        $r=\Hwj\JFactory::getDocument()->loadRenderer("head");
        
        if (!$display) {
            ob_start();
            echo $r->render();
            return ob_get_clean();
        } else {
            echo $r->render();
        }
    }
    
    public static function renderComponent($component,$display=true) {
        if (!$display) {
            ob_start();
            self::loadComponent($component);
            return ob_get_clean();
        } else
            self::loadComponent($component);
    }
    
    private static function loadComponent($component) {
        switch ($component) {
            // special components
            case "dbsync":
                if (self::checkAdmin())
                    \dbSync::init(S_Factory::getConf(S_CfgSec::shared_paths,"database"));
            break;
            // regular components
            default:
                $class="Hw2\S_Com_".$component;
                if (class_exists($class)) {
                    $class::init();
                } else if (HW2_PLATFORM_NAME == S_PlatformList::hw2core) {
                    echo "<center> ===== HW2 CORE ".HW2_CORE_VERSION." ===== </center>";
                }
            break;
        }
    }
    
    public static function renderModule() {
        
    }
}

?>
