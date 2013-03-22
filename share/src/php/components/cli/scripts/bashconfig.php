<?php namespace Hw2;
S_Core::checkAccess();

final class S_Cli_BashConfig extends S_Cli_Obj {
     private $conf="";
    
     public function setConf($key,$value,$disableVar=0,$overwrite=0) {
         $this->conf.="hw2_setConf '$key' '$value' $disableVar $overwrite \n";
     }
    
     public function getOutput() {
        $this->setConf("MYSQL_DB", S_Factory::getConf(S_CfgSec::mysql, "db")); //mysql user
        $this->setConf("MYSQL_USER", S_Factory::getConf(S_CfgSec::mysql, "user")); //mysql user
        $this->setConf("MYSQL_PASS", S_Factory::getConf(S_CfgSec::mysql, "pass")); //mysql pass
        $this->setConf("MYSQL_HOST", S_Factory::getConf(S_CfgSec::mysql, "host")); //mysql host
        /*
        * SHARED CONFIG
        */
        // GENERAL
        $this->setConf("WORKSPACE", S_Factory::getConf(S_CfgSec::dev,"workspace")); // dev workspace
        
        // PATHS
        $this->setConf("HW2PATH_WORKSPACE_LOCAL", S_Factory::getConf(S_CfgSec::local_paths,"workspace")); // ${HW2_CONF['HW2PATH_WORKSPACE_LOCAL']}$root"
        $this->setConf("HW2PATH_DEV_LOCAL", S_Factory::getConf(S_CfgSec::local_paths,"workspace").S_Factory::getConf(S_CfgSec::local_paths,"root"));
        $this->setConf("HW2PATH_WORKSPACE_REMOTE", S_Factory::getConf(S_CfgSec::remote_paths,"workspace")); 
        $this->setConf("HW2PATH_DEV_REMOTE", S_Factory::getConf(S_CfgSec::remote_paths,"workspace").S_Factory::getConf(S_CfgSec::remote_paths,"root")); //"${HW2_CONF['HW2PATH_WORKSPACE_REMOTE']}$root"
        $this->setConf("HW2PATH_WORKSPACE_CURRENT", S_Factory::getConf(S_CfgSec::shared_paths, "workspace_path"));
        $this->setConf("HW2PATH_DEV_CURRENT", S_Factory::getConf(S_CfgSec::shared_paths, "dev_path")); // "${HW2_CONF['HW2PATH_WORKSPACE_CURRENT']}$root"
        $this->setConf("HW2PATH_VAR", S_Factory::getConf(S_CfgSec::shared_paths,"var_folder")); // "${HW2_CONF['HW2PATH_WORKSPACE_CURRENT']}$var_folder"
        $this->setConf("HW2PATH_BACKUP", S_Factory::getConf(S_CfgSec::shared_paths, "project_bak_folder")); // "${HW2_CONF['HW2PATH_VAR']}$backup_folder"
        $this->setConf("DB_DIR", S_Factory::getConf(S_CfgSec::shared_paths, "database")); // 
        $this->setConf("HW2PATH_APPS", S_Paths::I()->get('s_apps').DS); 
        /*
        * LOCAL CONFIG
        */
        $this->setConf("ALIAS", S_Factory::getConf(S_CfgSec::info,"alias"));
        $this->setConf("PLATFORM", S_Factory::getConf(S_CfgSec::info,"platform"));
        $this->setConf("ISTRUNK", S_Factory::getConf(S_CfgSec::info,"istrunk"));
        $this->setConf("VERSION", S_Factory::getConf(S_CfgSec::info,"version"));
        $this->setConf("SITE_HOST", S_Factory::getConf(S_CfgSec::general,"site_host"));
        $this->setConf("SITE_URL", S_Factory::getConf(S_CfgSec::general,"site_url"));
        $this->setConf("FTP_HOST", S_Factory::getConf(S_CfgSec::ftp,"host"));
        $this->setConf("FTP_USER", S_Factory::getConf(S_CfgSec::ftp,"user"));
        $this->setConf("FTP_PASSWD", S_Factory::getConf(S_CfgSec::ftp,"passwd"));
        
        // remote sync
        $this->setConf("USE_FTP", S_Factory::getConf(S_CfgSec::general,"use_ftp"));
        $this->setConf("R_PATH", S_Factory::getConf(S_CfgSec::general,"remote_path"));
        $this->setConf("R_OPTIONS", S_Factory::getConf(S_CfgSec::general,"remote_options"));
        $sec=S_Factory::getConf(S_CfgSec::general,"use_ftp") == 1 ? S_CfgSec::ftp : S_CfgSec::ssh;
        $this->setConf("R_HOST", S_Factory::getConf($sec,"host"));
        $this->setConf("R_USER", S_Factory::getConf($sec,"user"));
        $this->setConf("R_PASS", S_Factory::getConf($sec,"passwd"));
        
        // db sync
        $this->setConf("DS_URL", S_Factory::getConf(S_CfgSec::general, "site_url")."administrator/index.php?hw2ext=dbsync&");
        
        $result="function load_conf() {\n";
        $result.=$this->conf;
        $result.="}\n";
        return $result;
    }
}
?>
