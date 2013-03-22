<?php namespace Hw2;
defined('HW2_CORE_EXEC') or die('Restricted access');

class S_PathKey {
    public $key;
    function __construct($key) {
        $this->key = $key;
    }

}

final class S_PathType {
    // the int value is only an index
    const def=0; // default
    const css=1;
    const php=2;
    const js=3;
    const dir=4; // directory
    //const url_dir=8;
}

class S_PathInfo {
    public $key;
    private $path;
    /**
     *
     * @var mixed ,  
     */
    public $parts;
    public $type;
    public $isUrl;
    public $global_var;
    public $info; // used for any kind of data to store with path
    public $isRealPath;
    
    public function __construct($key,$path, Array $parts, $type=0,$global_var="",
                                    $isUrl=false, $info=null,$isRealPath=false) {
        $this->key=$key;
        $this->path=$path;
        $this->parts=$parts;
        $this->type=$type; // type of file/path
        $this->isUrl=$isUrl; // is it an url or localfile
        $this->global_var=$global_var;
        $this->info=$info;
        $this->isRealPath=$isRealPath;
    }
    
    public function __toString() {
        return $this->get();
    }
    
    public function __get($name) {
        switch ($name) {
            case "path":
                return $this->get();
            break;
        }
    }
    
    public function get($short=false) {
        return $this->isUrl ? $this->getUrl($short) : $this->getPath($short);
    }
    
    public function getUrl($pathonly=false) {
        if ($this->isUrl) {
            //if the path is already defined as an url just return it
            $result= $this->path;
        } else {
            // else create from filepath.
            // first check if it's not symlinked path and replace
            // else check for symlink original path
            // then replace all directory separators with correct one ( needed if we are on windows)
            $search=Array(
                    S_Paths::I()->get(S_Paths::HW2PATH_PARENT), 
                    S_Paths::I()->get(S_Paths::HW2PATH_CORE_ORIGIN),
                    DS
                );
            $replace=Array(
                    S_Paths::I()->get(S_Paths::HW2PATH_PARENT_URL),
                    S_Paths::I()->get(S_Paths::HW2PATH_CORE_URL),
                    "/"
                );
            $result= str_replace($search,$replace,$this->path);
        }
        
        if ($pathonly) {
            $result= str_replace(S_Uri::getUriPrefix(),"",$result);
        }
        
        return $result;
    }
    
    public function getPath($relative=false) {
        // if it isn't url, then just get the path
        // else replace the url part
        if (!$this->isUrl) 
            $result= $this->path;
        else {
            // remove url base path
            $return = str_replace (
                Array( 
                    S_Paths::I()->get(S_Paths::HW2PATH_PARENT_URL),
                    "/"
                ),
                Array(
                    ".".DS,
                    DS
                ),
                $this->path
            );
            // then get the absolute position of ./path
            $result= realpath($return);
        }
        
        // if relative, remove parent OR core_origin path
        if ($relative) { 
            // first find core origin and replace with symlink path
            // then cut the corrected path
            $search=Array(
                    S_Paths::I()->get(S_Paths::HW2PATH_CORE_ORIGIN),
                    S_Paths::I()->get(S_Paths::HW2PATH_PARENT).DS
                );
            $replace=Array(
                    S_Paths::I()->get(S_Paths::HW2PATH_CORE),
                    ""
                );
            $result=str_replace($search,$replace,$result);
        }
        
        return $result;
    }
    
    public function isLocal() {
        foreach ($this->parts as $part) {
            if (self::partToPath ($part,$this->isUrl) == $this->get(S_Paths::HW2PATH_LOCAL));
        }
            
    }
    
    public static function partToPath($part,$isUrl,$rebuild=false) {
        if ($part instanceof S_PathKey) {
            if ($part->isUrl!=$isUrl) {
                trigger_error ("Part ".$part->key." has not url flag =".$isUrl,E_USER_ERROR);
                die();
            }
            return S_Paths::I()->get($part->key, $rebuild);
        } else if (is_string($part)) {
            return $part;
        } else {
            trigger_error ("invalid path type of ".$part,E_USER_ERROR);
            die();
        }
    }
}

final class S_Paths {
    const HW2PATH_CORE="HW2PATH_CORE";
    const HW2PATH_CORE_ORIGIN="HW2PATH_CORE_ORIGIN";
    const HW2PATH_SHARE="HW2PATH_SHARE";
    const HW2PATH_LOCAL="HW2PATH_LOCAL";
    const HW2PATH_LOCAL_CONF="HW2PATH_LOCAL_CONF";
    const HW2PATH_SHARE_CONF="HW2PATH_SHARE_CONF";

    // parent path of hw2core, normally it is relative to parent platform
    const HW2PATH_PARENT="HW2PATH_PARENT";

    const HW2PATH_LOCAL_TMP="HW2PATH_LOCAL_TMP";
    const HW2PATH_LOCAL_LOGS="HW2PATH_LOCAL_LOGS";
    
    //urls
    const HW2PATH_CORE_URL="HW2PATH_CORE_URL";
    const HW2PATH_PARENT_URL="HW2PATH_PARENT_URL";
    
    
    protected $paths;
    private static $_instance;
    

    public function __construct($paths=null) {
        $this->paths=$paths;
    }
        
    public function setCorePath($corePath) {
        $this->setPath(self::HW2PATH_CORE, $corePath,S_PathType::dir,self::HW2PATH_CORE);
    }
    
    public function initialise($corePath) {
        $this->setCorePath($corePath);
        $this->setPath(self::HW2PATH_CORE_ORIGIN, HW2PATH_CORE_ORIGIN,S_PathType::dir,self::HW2PATH_CORE_ORIGIN);
        
        $path=Array(self::key(self::HW2PATH_CORE), S_Core::isCore(true) ? null : '../');
        $this->setPath(self::HW2PATH_PARENT,$path,S_PathType::dir,self::HW2PATH_PARENT);
        // LOCAL PATHS
        $this->setPath(self::HW2PATH_LOCAL,Array(self::key(self::HW2PATH_CORE),'local'),S_PathType::dir,self::HW2PATH_LOCAL);
        $this->setPath(self::HW2PATH_LOCAL_CONF,Array(self::key(self::HW2PATH_LOCAL),'conf'),S_PathType::dir,self::HW2PATH_LOCAL_CONF);
        $this->setPath(self::HW2PATH_LOCAL_TMP,Array(self::key(self::HW2PATH_LOCAL),'tmp'),S_PathType::dir,self::HW2PATH_LOCAL_TMP);
        $this->setPath(self::HW2PATH_LOCAL_LOGS,Array(self::key(self::HW2PATH_LOCAL),'logs'),S_PathType::dir,self::HW2PATH_LOCAL_LOGS);
        // SHARED PATHS
        $this->setPath(self::HW2PATH_SHARE,Array(self::key(self::HW2PATH_CORE_ORIGIN),'share'),S_PathType::dir,self::HW2PATH_SHARE);
        $this->setPath(self::HW2PATH_SHARE_CONF,Array(self::key(self::HW2PATH_SHARE),'conf'),S_PathType::dir,self::HW2PATH_SHARE_CONF);
        
        $this->setPath("s_src", Array(self::key(self::HW2PATH_SHARE),'src'), S_PathType::dir);
        
        $this->setPath("s_apps",Array(self::key("s_src"),'apps'),S_PathType::dir);
        $this->setPath("s_php", Array(self::key("s_src"),'php'), S_PathType::dir);
        $this->setPath("s_jplatform", Array(self::key("s_src"),'jplatform'), S_PathType::dir);
    }
    
    /**
     * 
     * @param type $key
     * @param type $path
     * @param type $type
     * @param type $isUrl
     * @param type $global_var not suggested since paths could be dynamic
     * @param type $info
     * @param type $rebuild
     * @return \Hw2\S_PathInfo
     */
    public function build($key,$path,$type,$isUrl,$global_var="", $info=null,$useRealPath=false,$fileExists=false,$rebuild=false) {
        $tPath="";
        if (is_array($path)) {
            $ds=$isUrl ? "/" : DS;
            $num=count($path);
            for ($i=0;$i<$num;$i++) {
                if (empty($path[$i]))
                    continue;

                $tPath.=S_PathInfo::partToPath($path[$i], $isUrl,$rebuild);
                $tPath.=$i+1!=$num && substr($path[$i], -1) != $ds ? $ds : null;  
            }
        } else if (is_string($path)) {
            $tPath=$path;
            $path=Array($path);
        } else {
            trigger_error ("invalid path of ".$path,E_USER_ERROR);
            exit();
        }
        

        // only for path checks
        if (!$isUrl){
            if ( $fileExists && !file_exists($tPath)) {
                trigger_error ("File doesn't exists on path: ".$tPath,E_USER_ERROR);
                return false;
            }

            // special case of parent path or when we ask for real path
            // resolving symlink and reference giving the real absolute path
            // improve performance and avoid symlink usage when including
            if ( $key==self::HW2PATH_PARENT || $useRealPath ) { 
             $tmp=realpath ($tPath);
             if ($tmp!==false)
                 $tPath=$tmp;
            }
        }
        
        if (!empty($global_var))
            defined($global_var) or define ($global_var, $tPath);
        
        return new S_PathInfo($key, $tPath, $path, $type, $global_var, $isUrl, $info,$useRealPath);
    }
    
    public function getAllPaths() {
        return $this->paths;
    }
    
    /**
     * 
     * @param type $key
     * @param type $type
     * @return \Hw2\S_PathInfo
     */
    public function get($key,$rebuild=false) {
        $path=$this->paths[$key];
        if ($rebuild) {
           $path=$this->build ($key, $path->parts, $path->type, $path->isUrl, $path->global_var);
        }
        return $path;
    }
    
    /**
     * create a path
     * @param type $key
     * @param type $path
     * @param type $info
     * @param type $global_var
     * @param type $type
     * @param type $isUrl
     * @return \Hw2\S_PathInfo
     */
    public function setPath($key,$path,$type=0,$global_var="", $info=null,$useRealPath=false,$uniqueKey=false,$fileExists=false) {
        return $this->set($key,$this->build($key, $path, $type,false,$global_var,$info,$useRealPath, $fileExists , false),$uniqueKey);
    }
    
    public function setUrl($key,$path,$type=0,$global_var="", $info=null,$useRealPath=false,$uniqueKey=false,$fileExists=false) {
        return $this->set($key,$this->build($key, $path, $type,true,$global_var,$info,$useRealPath,$fileExists, false),$uniqueKey);
    }
    
    public function set($key,S_PathInfo $path,$uniqueKey=false) {
        if (!$uniqueKey || !array_key_exists($this->paths,Array($key))) {
            $this->paths[$key]=$path;
            return $this->paths[$key];
        } else
            return false;
    }
    
    
    
    public function rebuildPaths() {
        /* @var $path S_PathInfo */
        foreach ($this->paths as $key => $path ) {
            $this->paths[$key]=$this->build ($path->key, $path->parts, $path->type, $path->isUrl, $path->global_var, $path->info,$path->isRealPath,false, true);
        }
    }
    
    /**
     * We use singleton method instead static class to enable 
     * Path switching in runtime
     * @param type $corePath path that will be used as root of others
     * @param S_Paths $copyFrom copy paths from another source rebuilding with new corepath ( if instance already exists, this won't work ) 
     * @return S_Paths
     */
    public static function I($corePath=HW2PATH_CORE,S_Paths $copyFrom=null) {
        if (!self::$_instance[$corePath]) {
            if (isset($copyFrom)) { 
                // if it's a new instance and copyFrom defined
                // set new corepath and rebuild all paths based on it
                $copy=new self($copyFrom->getAllPaths()); 
                $copy->setCorePath($corePath);
                $copy->rebuildPaths(); 
                self::$_instance[$corePath] = $copy;
            } else {
                self::$_instance[$corePath] = new self();
                self::$_instance[$corePath]->initialise($corePath);
            }
        }
        
        return self::$_instance[$corePath];
    }
    
    /**
     * create a key object
     * @param type $key
     * @return \Hw2\S_PathKey
     */
    public static function key($key) {
        return new S_PathKey($key);
    }
}
?>
