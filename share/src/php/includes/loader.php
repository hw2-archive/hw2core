<?php namespace Hw2; 
// Check to ensure this file is within the rest of the framework
defined('HW2_CORE_EXEC') or die();

use Hw2\S_Paths as SP;


class S_LoadInfo {    
    public $type;
    public $autoLoad;
    public $incType=null;
    public $namespace;
    
    function __construct($type, $autoLoad=true, $incType="", $namespace="Hw2") {
        $this->type = $type;
        $this->autoLoad = $autoLoad;
        $this->incType=$incType;
        $this->namespace=$namespace;
    }
}
        
class S_Loader {    
    const req="require";
    const inc="include";
    const req_once="require_once";
    const inc_once="include_once";
    
    private static $_includedFiles=Array(); // used to keep trace of already included to avoid multiple inclusions
    private static $hw2_incl= Array(); 
    private static $_appLoaded=false;
    protected static $incType = Array(
        self::req,
        self::inc,
        self::req_once,
        self::inc_once
    );

    public static function JLoad($class) {
        if (class_exists("JLoader")) {
            $loader=new \JLoader();
            return $loader->load($class);
        }
    }
    
    public static function loadJPlatform() {
        S_Loader::addPath("s_jplatform_loader", Array(SP::key('s_jplatform'), 'loader.php'), S_PathType::php, true, "require_once");
        // Botstrap the CMS libraries.
        require_once \Hwj\JPATH_LIBRARIES . DS . 'cms.php';

        $conf = \Hwj\JFactory::getConfig(\Hwj\JPATH_CONFIGURATION . DS . 'configuration.php', 'PHP', 'Hwj');
        \Hwj\JFactory::getApplication("site", $conf->toArray(), "Hwj\J");

        $db = \Hwj\JFactory::getDbo();
        // try to connect db, mainly for CIndex
        try {
            $db->connect();
        } catch (\Exception $e) {
            
        }
    }

    public static function loadFramework() { 
        //spl_autoload_register(Array("Hw2\S_Core","autoloader"));

        //php dirs
        S_Loader::addPath("s_hw2_libraries",Array(SP::key('s_php'),'libraries','hw2core'),S_PathType::dir);
        S_Loader::addPath("s_3rd_libraries",Array(SP::key('s_php'),'libraries','3rd_party'),S_PathType::dir,true,"",S_SiteSide::both,"");
        S_Loader::addPath("s_components",Array(SP::key('s_php'),'components'),S_PathType::dir);
        S_Loader::addPath("s_modules",Array(SP::key('s_php'),'modules'),S_PathType::dir);
        S_Loader::addPath("s_plugins",Array(SP::key('s_php'),'plugins'),S_PathType::dir);
        S_Loader::addPath("s_dataset",Array(SP::key('s_php'),'dataset'),S_PathType::dir);
        
        S_Uri::init();
        S_Core::instantiateApp();
        S_Factory::init();
        S_Factory::checkDBVersion(); // check db version
        
        //applications
        S_Loader::addPath("s_app_dbsync",Array(SP::key('s_apps'),'hw2dbsync','dbsync.php'),S_PathType::php,true,"",S_SiteSide::both,"",false);
        
        switch (S_Factory::getPlatform()) {
                case S_PlatformList::hw2j:
                    // define if not defined, as in CLI execution
                    defined(JPATH_CONFIGURATION) or define ("JPATH_CONFIGURATION",SP::I()->get(SP::HW2PATH_PARENT));
                    
                    S_Factory::jConfRefactor(JPATH_CONFIGURATION); // rebuild also platform conf
                    S_Loader::addPath("s_hw2j_plugin_handler",HW2PATH_SHARE.DS.'src'.DS.'hw2j'.DS.'pluginsystem.handler.php',S_PathType::php);
                    if (S_Factory::getPVersion(S_Factory::ver_1_5))
                        spl_autoload_register(array('Hw2\S_Loader', 'JLoad'),true,true);
                break;
                default:
                break;
        }       
        
        self::_loadPlugins();
    }
    
    public static function loadApplication(){    
        //media
        S_Loader::addPath("s_css",Array(SP::key("s_src"),'css'),S_PathType::dir,false);
        S_Loader::addPath("s_js",Array(SP::key("s_src"),'javascript'),S_PathType::dir,false);
        S_Loader::addPath("s_m_img",Array(SP::key(SP::HW2PATH_SHARE),'media','images'),S_PathType::dir,false);
        
        // include platform specific loaders
        switch (S_Factory::getPlatform()) {
                case S_PlatformList::hw2j:
                    // register loads function of previous frameworks 
                    S_Loader::addPath("s_hw2j_loader", HW2PATH_SHARE.DS.'src'.DS.'hw2j'.DS.'loader.php', S_PathType::php,true,"require_once"); 
                break;
                default:
                break;
        }
        
        // after loaded class, init dataset, compile and run
        S_DS_Builder::init();
        S_CssMgr::init();
    }    
    
    public static function loadMedia() {
        S_Loader::addPath("s_js_hw2tools",Array(SP::key("s_js"),'hw2_tools.js'),S_PathType::js,true,"",  S_SiteSide::both);
        
        switch (S_Factory::getPlatform()) {
                case S_PlatformList::hw2j:
                    S_jLoader::loadMedia();
                break;
                default:
                break;
        }
    }
    
    private static function _loadPlugins() {
        S_Plg_System::load(S_AppType::base(), "system");
    }
    
    public static function loadFile($key) {
        $path=S_Paths::I()->get($key);
        $info=$path->info;
        if (!$info || !$info instanceof S_LoadInfo)
            return false;
        
        switch ($path->type) {
            case S_PathType::css:
                $inc=!empty($info->incType) ? $info->incType : S_CssMgr::ref;
                S_CssMgr::inc($path, null, $inc );
            break;
            case S_PathType::js:
                $document = &\Hwj\JFactory::getDocument();
                $document->addScript($path->getUrl());
            break;
            case S_PathType::dir:
                if ($path->isUrl)
                    break;
                S_CIndex::scanPath($path->path,$info->namespace);
            break;
            case S_PathType::php:
                if ($path->isUrl)
                    break;
                
                $incType=$info->incType;
                if (!empty($incType)) {
                   if (!self::includePhpFile($path->path, $incType))
                       trigger_error ("cannot ".$incType." file ".$path->path,E_USER_ERROR);
                } else {
                    S_CIndex::scanPath($path->path,$info->namespace);
                }
            break;
            default :
                ;
            break;
        }
    }
    
    public static function loadExtension($name,$type,$args) {
        switch ($type) {
            case "plugin":
                $class="Plg_";
            break;
            case "module":
                $class="Mod_";
            break;
            case "component":
                $class="Com_";
            break;  
        }
        $class.=ucfirst($name);
        var_dump($class);
        return S_Core::callGlobal("Hw2\\".$class, "load",$args);
    } 
    
    public static function initApp() {
        if (!self::$_appLoaded) {
            S_Core::callGlobal(get_class(), "loadApplication");
            
            // display only if we are on core platform
            // instead use platform-specific methods
            if (S_Factory::getPlatform()==S_PlatformList::hw2core)
                S_ApplicationWeb::display(); 
            
            self::$_appLoaded=true;
        }
    }
    
        
    private static function addIncluded($path) {
        self::$_includedFiles[$path]=  basename($path);
    }
    
    public static function isIncluded($path) {
        return self::$_includedFiles[$path] ? true : false;
    }
    
    /**
     * 
     * @param type $name
     * @param type $path
     * @param type $destPath it's the path used to compile a php-css file
     *  that could be relative ( also empty string "" accepted) or absolute
     * @param type $autoLoad
     * @param type $side
     * @param type $verbose
     */
    public static function addCss($name,$path,$destPath=NULL,$incType="",$autoLoad=true,$side=2,$verbose=true) {    
        $info=new S_LoadInfo(S_PathType::css, $autoLoad,$incType);
        
        if (!is_null($destPath)) {
            $src=SP::I()->build($name, $path, S_PathType::css, false,"",null,true,true);
            $dest=S_CssMgr::compile($src, $destPath);
            self::addInclude($name, $dest, $info, $side, false, $verbose);
        } else
            $path=self::addInclude($name, $path, $info, $side, false, $verbose);
    }
    
    public static function addUrl($name,$url,$type,$autoLoad=true,$side=2,$verbose=true) {
        $info=new S_LoadInfo($type, $autoLoad);
        self::addInclude($name, $url, $info, $side, true, $verbose);
    }
    
    public static function addPath($name,$path,$type,$autoLoad=true,$incType="",$side=2,$namespace="Hw2",$verbose=true) {
        $info=new S_LoadInfo($type, $autoLoad, $incType, $namespace);
        self::addInclude($name, $path, $info, $side, false, $verbose);
    }

    /**
     * 
     * @param type $name
     * @param type $path
     * @param S_LoadInfo $loadInfo contains info as autoload,type,namespace etc.
     * @param type $side
     * @param type $isUrl
     * @param type $verbose
     * @return type
     */
    private static function addInclude($name,$path,S_LoadInfo $loadInfo,$side=2,$isUrl=false,$verbose=true) {
        if ($path instanceof S_PathInfo) {
            $path->info=$loadInfo;
            $path=SP::I()->set ($name, $path);
        } else {
            $path=$isUrl ? 
            SP::I()->setUrl($name, $path, $loadInfo->type, "", $loadInfo, true, true, $verbose)
                :
            SP::I()->setPath($name, $path, $loadInfo->type, "", $loadInfo, true, true, $verbose);
        }
        
        if ($path===false && $verbose) {
            trigger_error("Error when setting path: ".$name,E_USER_ERROR);
            die();
        }

        if ($loadInfo->autoLoad) {
            if ($side==S_SiteSide::both || ($side==S_Core::isBackend()))
                self::loadFile($name);
        }
    }
    
    private static function includePhpFile($path,$incType) {
        if (in_array($incType, self::$incType)) {
            self::addIncluded($path);
            eval($incType." '".$path."';");
            return true;
        }
        
        return false;
    }
    
    /*
     *  getters and setters
     */
    public static function getIncPath($name) {
        return S_Paths::I()->get($name)->path;
    }
    
    
    /**
     * 
     * @param int $type
     * @return Array
     */
    public static function getIncByType($type=0) {
        $result=Array();
        $paths=S_Paths::I()->getAllPaths();
        foreach ($paths as $key => $val) {
            if ($val->type == $type || $type==0) {
                $result[$key]=$val;
            }
        }
        
        return $result;
    }
    
    // should be latest autoloader registered
    public static function autoloader($class) {
        /*if (!class_exists($class)) {
            $includes = array_merge(
                    self::getIncByType(self::php),
                    self::getIncByType(self::dir)
            );
            //self::initIndex(true);
            foreach ($includes as $inc) {
                //if (empty($inc["incType"]))
                    //self::scanPath($inc["path"],true);
            }
            //self::saveIndex();
        }*/
    }
    
    
}

?>
