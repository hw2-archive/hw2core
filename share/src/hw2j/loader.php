<?php namespace Hw2;
// Check to ensure this file is within the rest of the framework
S_Core::checkAccess(true);

use Hw2\S_Paths as SP;

class S_jLoader {
    
    function __construct() {        
            // before files with special initialization
            S_Loader::addPath("s_hw2j_defines",HW2PATH_SHARE.DS.'src'.DS.'hw2j'.DS.'defines.php', S_PathType::php, true ,"require_once");
            switch (S_Factory::getPVersion()) {
                case S_Factory::ver_latest:
                    $v_folder="latest";
                break;
                case S_Factory::ver_1_5:
                    $v_folder="1_5";
                    //
                break;
            }
            S_Loader::addPath("s_hw2j_version_defines",HW2PATH_SHARE.DS.'src'.DS.'hw2j'.DS.$v_folder.DS.'defines.php', S_PathType::php, true ,"require_once");
            // then directory search
            S_Loader::addPath("s_hwj_dir", HW2PATH_SHARE.DS.'src'.DS.'hw2j', S_PathType::dir);
            
            plgSystemHw2::register("system", "Hw2System");
    }
    
    
    public static function loadMedia() {
        S_Loader::addPath("s_hw2j_css_template", Array(SP::key("s_css"),'hw2j_template.css'),S_PathType::css);  
        S_Loader::addPath("s_hw2j_js_jtools", Array(SP::key("s_js"),'hw2j_tools.js'), S_PathType::js);
    } 
    
}
// init
new S_jLoader();

?>
