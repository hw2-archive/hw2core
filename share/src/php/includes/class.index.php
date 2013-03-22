<?php namespace Hw2;

defined('HW2_CORE_EXEC') or die('Restricted access');

class S_FTools {
   public static function rglob($pattern='*', $flags = 0, $path='')
   {
       $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
       $files=glob($path.$pattern, $flags);
       foreach ($paths as $path) { $files=array_merge($files,self::rglob($pattern, $flags, $path)); }
       return $files;
   }
    
    public static function file_get_php_classes($filepath) {
        $php_code = file_get_contents($filepath);

        if (!empty($php_code) && strpos($php_code, "/*[HW2-OB]*/")!==false) { // if has been obfuscated
            $php_code = self::getObfuscated($php_code);
        }
        
        $classes = self::get_php_classes($php_code);
        return $classes;
    }

    private static function get_php_classes($php_code) {
        $classes = array();
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                    && $tokens[$i - 1][0] == T_WHITESPACE
                    && $tokens[$i][0] == T_STRING) {

                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }
        return $classes;
    }
    
    public static function getObfuscated($php_code) {
        $GLOBALS["hw2_no_eval"]=1; // flag to avoid internal evalutation   
        eval("?>".$php_code);
        $GLOBALS["hw2_no_eval"]=0;
        return $GLOBALS["hw2_eval_str"];
    }
}


class S_CIndex {
    const table="hw2_class_index"; 
    
    private static $alert=false;
    private static $_paths=Array();
    private static $_isChanged=false;
    
    public static function getIndex() {
        return self::$_paths;
    }
    
    private static function unsetPath($class) {
        unset(self::$_paths[$class]);
        self::$_isChanged = true;
    }
    
    private static function setPath($class,$path) {
        // save class in lowercase to better search
        // since we cannot define different class 
        // with same insensitive name
        $class=  strtolower($class); 
        self::$_paths[$class]=$path;
        self::$_isChanged = true;
    }
    
    public static function getPath($class) {
        return self::$_paths[$class];
    }
    
    public static function saveIndex() {
        if (self::$_isChanged) {
            self::delIndex();
            $db = \Hwj\JFactory::getDbo();
            if (!$db->connected())
                return false;
            $query=$db->getQuery(true);
            $query->insert(self::table);
            $query->columns(Array('class','path'));
            foreach (self::$_paths as $class=>$path) {
                $query->values("\"".$db->escape($class)."\",\"".$db->escape($path)."\"");
            }
            $db->setQuery($query);
            $db->execute();
        }
    }
    
    private static function delIndex() {
        $db = \Hwj\JFactory::getDbo();
        if (!$db->connected())
            return false;
        $db->truncateTable(self::table);
    }
    
    public static function initIndex($clean=false) {
        register_shutdown_function('Hw2\S_CIndex::handleShutdown');

        $db = \Hwj\JFactory::getDbo();
        if (!$db->connected())
            return false;
        $query=$db->getQuery(true);
        $query->select('*')
                ->from(self::table);
        $db->setQuery($query);
        self::$_paths=$db->loadAssocList("class","path");
    }
    
    public static function regClasses() {
        foreach (self::$_paths as $class => $path) {
            if (!file_exists($path)) {
                self::unsetPath($class);
            }
            else
                \Hwj\JLoader::register ($class, $path);
        }
        self::saveIndex();
    }
    
    public static function scanPath($path,$namespace="Hw2") {
        $list=Array();
        if (is_dir($path)) {
            //get all files with a .php extension.
            if (in_array($path,self::getIndex()))
                return;
            self::setPath($path, $path);
            $phplist = S_FTools::rglob("*.php",0,$path);
            foreach($phplist as $php){ $list[] = "$php"; }
        } else {
            if (!file_exists($path))
                return;
            $list[]=$path;
        }
            
        foreach ($list as $p) {
            if (S_Loader::isIncluded($p) || in_array($p,self::getIndex())) // if we already added it, then skip
                    continue;
            
            if (!self::$alert) {
                echo "ALERT: reading from file to rebuild index\n";
                self::$alert=true;
            }
            $classes=S_FTools::file_get_php_classes($p);
            $plan_files=Array();
            if (empty($classes)) {
                //$plan_files[]=$p; // files with defines only or global var/functions
            } else {
                foreach ($classes as $class) {
                    if (!empty($namespace)) 
                        $class=$namespace."\\".$class;
                    self::setPath($class, $p);
                    \Hwj\JLoader::register ($class, $p);
                }
            }
            
            foreach ($plan_files as $file)
                require_once $file;
        }
        self::saveIndex();
    }
    
    public static function handleShutdown() {
        $error = error_get_last();
        if ($error['type'] === E_ERROR || $error['type'] === E_USER_ERROR) {      
            self::delIndex();
        }
    }
}

?>
