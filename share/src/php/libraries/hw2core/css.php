<?php namespace Hw2;
S_Core::checkAccess();

class S_CssMgr {
    const inc="include";
    const ref="ref";
    
    public static function init() {
        $name=S_CT_Item::normalizeId(S_FS_Config::css_rebuild.S_FS_ValueBtn::value);
        $rebuildTime=S_Uri::getInput()->get($name, false, "post");
        if ($rebuildTime == "rebuild_css") {
            S_CT_Core::I()->setContentVal (S_CT_Core::last_css_rebuild, time(), true);  
        }
    }
    
    /**
     * Include 
     * @param type $src
     * @param type $dest 
     * @param string $type can be "ref" or "include"
     */
    public static function inc(S_PathInfo $src,$dest=null,$type=self::ref) {
        if (!$dest) {
            switch ($type) {
                case self::inc:
                    $content=self::getContent($src->getPath());
                    \Hwj\JFactory::getDocument ()->addStyleDeclaration($content);
                break;
                case self::ref:
                    \Hwj\JFactory::getDocument ()->addStyleSheet($src->getUrl());
                break;
                default:
                    S_Exception::raise("Css wrong inclusion type: ".$type, S_Exception_type::error());
                break;
            }
        } else {
            $dest=self::compile($src,$dest);
            \Hwj\JFactory::getDocument ()->addStyleSheet($dest->getUrl()); 
        }           
    }
    
    public static function compile($src,$dest) {
        if ($src instanceof S_PathInfo) {
            if (!S_FileSys::isAbsolute($dest)) {
                
                /* @var $cssPath \Hw2\S_PathInfo */
                $dest=  S_Paths::I()->build($src->key, Array(
                    S_Paths::key($src->isLocal() ? "l_css" : "s_css"),
                    $dest,
                    pathinfo($src->getPath(),PATHINFO_FILENAME) // it will remove php extension and remains css
                ), S_PathType::css, false);
            }
        }
            
        $timeSrc=filemtime($src);
        $timeDest=filemtime($dest);
        $lastCompile=  S_CT_Core::I()->getContentVal(S_CT_Core::last_css_rebuild);
        // smaller implies is older
        if($timeSrc>=$timeDest || $lastCompile>=$timeDest) {
            $content=self::getContent($src);

            self::write(self::cleanContent($content), $dest);
        }
        
        return $dest;
    }
    
    public static function write($content,$dest) {
        if (file_put_contents($dest, $content)===false)
            S_Exception::raise ("Compiling css failed on ".$dest, S_Exception_type::error());
    }
    
    private static function getContent($src) {
        ob_start();
        include_once($src);
        return ob_get_clean();
    }
    
    private static function cleanContent($content) {
        // we could optionally use style tag to fix ide autocompletition
        // the compiler will remove it
        $replace=Array(
            '<style type="text/css">',
            "</style>"
            );
        $content=  str_ireplace($replace, "", $content);
        return $content;
    }
    
}
?>
