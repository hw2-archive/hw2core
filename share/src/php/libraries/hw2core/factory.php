<?php namespace Hw2;
S_Core::checkAccess();

$host="localhost";
class S_Factory extends S_Object {
    const ver_latest="latest";
    const ver_1_5="1_5";
    
    private static $initialized=false;
    private static $platform;
    private static $conf = Array();

    /**
     * 
     * @param type $section
     * @param type $key
     * @param type $verbose if true and no key found then return an error, else return null
     * @return null
     */
    public static function getConf($section, $key = null, $verbose = true) {
        if (!key_exists($section, self::$conf) || ($key && !key_exists($key, self::$conf[$section])))
            if ($verbose)
                S_Exception::raise("invalid section or key: " . $section . " => " . $key , S_Exception_type::error());
            else
                return null;

        if (!$key)
            return self::$conf[$section];
        return self::$conf[$section][$key];
    }

    public static function addConf($section, $key, $value) {
        if (!$value)
            return;

        self::$conf[$section][$key] = $value;
    }

    public static function setConf($conf) {
        self::$conf = $conf;
    }
    
    public static function init() {
        if (!self::$initialized) {
                self::_build_conf();

                //add includes
                S_Loader::addPath("hw2_dev_dir", self::getConf(S_CfgSec::shared_paths, "dev_path"), S_PathType::dir, false,"",  S_SiteSide::both,"",false);
                self::jConfRefactor(\Hwj\JPATH_CONFIGURATION,"JConfigHwj");
                S_SyncConfRefactor::init();
                self::$initialized=true;
        }       
    }
    
    private static function _build_conf() {
        //LOAD ALL CONF FILES
        $s_user_ini=parse_ini_file(HW2PATH_SHARE_CONF.DS."user_conf".DS."info.cfg",true);
        $s_ini=parse_ini_file(HW2PATH_SHARE_CONF.DS."hw2core.cfg",true);
        $l_user_ini=parse_ini_file(HW2PATH_LOCAL_CONF.DS."user_conf".DS."info.cfg",true);
        $l_ini=parse_ini_file(HW2PATH_LOCAL_CONF.DS."hw2core.cfg",true);

        $conf=array_merge($s_user_ini,$s_ini,$l_user_ini,$l_ini);
        self::setConf($conf);
        //SET VARIABLES
        self::$platform=self::getConf(S_CfgSec::info,"platform");
        if (!in_array(self::$platform, S_PlatformList::toArray()))
            S_Exception::raise ("Invalid Platform ".self::$platform." selected in config", S_Exception_type::error ());
        //user section
        $uSection=$conf[S_CfgSec::dev]["workspace"]=="local" ? 'local' : 'remote';
        self::addConf(S_CfgSec::mysql, "db", self::getConf($uSection,"mysql_db"));
        self::addConf(S_CfgSec::mysql, "user", self::getConf($uSection,"mysql_user",false));
        self::addConf(S_CfgSec::mysql, "pass", self::getConf($uSection,"mysql_pass",false));
        self::addConf(S_CfgSec::mysql, "host", self::getConf($uSection,"mysql_host",false));

        //path section
        $pSection=$conf[S_CfgSec::dev]["workspace"]=="local" ? S_CfgSec::local_paths : S_CfgSec::remote_paths;
        $paths=array_merge($conf[$pSection],$conf[S_CfgSec::shared_paths]);
        
        $dev_path=$paths["workspace"].$paths["root"];
        self::addConf(S_CfgSec::shared_paths, "workspace_path", $paths["workspace"]);
        self::addConf(S_CfgSec::shared_paths, "dev_path", $dev_path);
        self::addConf(S_CfgSec::shared_paths, "apps", HW2PATH_CORE_ORIGIN.DS.$paths["apps"]);
        self::addConf(S_CfgSec::shared_paths, "database", HW2PATH_CORE.DS.$paths["database"]);
        self::addConf(S_CfgSec::shared_paths, "hw2j_latest", $dev_path.self::getConf(S_CfgSec::shared_paths,"hw2j_latest"));
        
        $bak=$paths["workspace"].self::getConf(S_CfgSec::shared_paths,"var_folder").self::getConf(S_CfgSec::shared_paths,"backup_folder").self::$platform.DS;
        self::addConf(S_CfgSec::shared_paths, "project_bak_folder", $bak);
    }
    
    /**
     *  Change configuration file using hw2conf ini values
     */
    static public function jConfRefactor($confPath,$class="JConfig") {
        //Set the configuration file path.
        $file = $confPath .DS.'configuration.php';
        include_once $file;
        $jconf = new $class;

        $prefix = self::getConf("info", "version") == "latest" ? "public" : "var";

        // conf_name, j_variable, hw2_variable
        $replace = Array(
            Array(S_Object::vname($jconf->host, $jconf), $jconf->host, self::getConf(S_CfgSec::mysql, "host")),
            Array(S_Object::vname($jconf->db, $jconf), $jconf->db, self::getConf(S_CfgSec::mysql, "db")),
            Array(S_Object::vname($jconf->user, $jconf), $jconf->user, self::getConf(S_CfgSec::mysql, "user")),
            Array(S_Object::vname($jconf->password, $jconf), $jconf->password, self::getConf(S_CfgSec::mysql, "pass")),
            Array(S_Object::vname($jconf->sitename, $jconf), $jconf->sitename, self::getConf(S_CfgSec::info, "name")),
            Array(S_Object::vname($jconf->log_path, $jconf), $jconf->log_path, HW2PATH_LOCAL_LOGS),
            Array(S_Object::vname($jconf->tmp_path, $jconf), $jconf->tmp_path, HW2PATH_LOCAL_TMP)
            
        );

        foreach ($replace as $key => $value) {
            if ($value[1] != $value[2])
                $data[$prefix . " \$" . $value[0] . " = '" . $value[1] . "';"] = $prefix . " \$" . $value[0] . " = '" . $value[2] . "';";
        }

        if ($data)
            S_FileSys::replaceInFile($file, $data);
    }
    
    /*
     * getters and setters
     */
        
    public static function getPlatform() {
        return self::$platform;
    }
    
    /**
     * get platform version defined in local conf file
     * @param string $compare_value version to compare
     * @return mixed if compare_value specified, it will return false in case of different version..else return the version
     */
    public static function getPVersion($compare_value="") {
        $version=self::getConf("info", "version");
        return $version==$compare_value || $compare_value=="" ? $version : false;
    }
    
    /**
     * 
     * @param type $compare_value comparing value for required column
     * @return Array array composed by "name" and "required" key
     */
    public static function getDBVersion($compare_value="") {
        $db=\Hwj\JFactory::getDbo();
        if (!$db->connected()) 
            return NULL;
        
        $db->setDebug(3);
        $query=$db->getQuery(true);
        $query->select("column_name")
                ->from("information_schema.columns")
                ->where(Array("table_name='hw2_db_version'","table_schema='".self::getConf(S_CfgSec::mysql, "db")."'","ordinal_position=2"));

        $db->setQuery($query);
        $version["required"]=$db->loadResult();

        $query=$db->getQuery(true);
        $query->select("version")
                ->from("hw2_db_version");
        $db->setQuery($query);
        $version["name"]=$db->loadResult();
        
        return $compare_value=="" || $version["require"]=="required_v".$compare_value  ? $version : false;
    }
    
    public static function checkDBVersion() {
        $version=self::getDBVersion();
        if ($version !== NULL && $version["required"]!="required_v".HW2_DB_VERSION)
            S_Exception::raise ("This hw2core require db ver.".HW2_DB_VERSION.
                " instead db ver.".str_replace ("required_v", "", $version["required"])." is installed", S_Exception_type::error(),false);
    }
}

final class S_SyncConfRefactor {
    private static $changed=false;
    
    private static function updateElement(\DOMNodeList $e,$value) {
        $value=  html_entity_decode($value);
        if ($e->item(0)->nodeValue != $value) {
            $e->item(0)->nodeValue = $value;
            self::$changed=true;
        }
    }
    
    public static function init($syncPath="") {
        $alias=S_Factory::getConf(S_CfgSec::info, "alias");
        if (empty($syncPath)) {
            // rename sync file if we change alias
            $suffix=".ffs_gui";
            $path=HW2PATH_LOCAL_CONF.DS;
            $files=glob($path."*".$suffix);
            if (!$files)
                return false;
            if (basename($files[0]) != $alias.$suffix )
                rename($files[0], $path.$alias.$suffix );
            $syncPath=$path.$alias.$suffix;
        }
        
        $doc = new \DOMDocument();
        if ($doc->load($syncPath)) {
            self::updateElement($doc->getElementsByTagName("CustomDeletionFolder"), S_Factory::getConf(S_CfgSec::sync, "trash"));
            self::updateElement($doc->getElementsByTagName("Exclude"), S_Factory::getConf(S_CfgSec::sync, "exclude"));
            
            $pairs=$doc->getElementsByTagName("Pair");
            self::updateElement($pairs->item(0)->getElementsByTagName("Left"), HW2PATH_PARENT);
            $right=S_Factory::getConf(S_CfgSec::shared_paths, "workspace_path")."hw2_remote".DS.$alias;
            self::updateElement($pairs->item(0)->getElementsByTagName("Right"), $right);
            
            if (self::$changed) {
                $doc->saveXML(); 
                $doc->save($syncPath);
            }
        }
    }
}

?>
