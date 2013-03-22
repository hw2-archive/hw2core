<?php namespace Hw2;
S_Core::checkAccess();

final class S_Cli_SiteCreator extends S_Cli_Obj {
    public static function init() {
        if (!S_Factory::getConf("info","istrunk")) {
            self::myEcho("this is not trunk, we cannot create a branch!");
            return;
        }
        $prompt=self::prompt(true);
        
        // 1) get site name, alias , passwords
        $sName=$prompt->gets("Site Name: ");
        $sAlias=$prompt->gets("Site Alias: ");
        $sSiteUrl=$prompt->gets("Site url: ");
        $sMysqlDb=$prompt->gets("Mysql db: ");
        $sMysqlUser=$prompt->gets("Mysql user: ");
        $sMysqlPass=$prompt->gets("Mysql pass: ");
        
        $origin=HW2PATH_PARENT;
        $target=  S_Factory::getConf("shared_paths", "dev_path").S_Factory::getPlatform().DS."branches".DS.$sAlias;
        $hw2CoreTarget=$target.DS.HW2CORE_FOLDER_NAME;
        
        
        // 2) copy folder
        $prompt->loaderCmd(S_BashScr::create_branch,Array("ORIGIN"=>$origin,"TARGET"=>$target));
        
        $localConf=$hw2CoreTarget.DS."local".DS."conf".DS;
        // change configs
        
        // HW2CORE CONF
        $ini=new \Config_Lite();
        $ini->read($localConf."hw2core.cfg");
        $ini->set(S_CfgSec::info, "istrunk",0);
        $ini->set(S_CfgSec::info, "alias",$sAlias);
        $ini->set(S_CfgSec::info, "name",$sName);
        $ini->save();
        
        // USER CONF
        $ini->read($localConf.DS."user_conf".DS."info.cfg");
        //general
        
        $ini->set(S_CfgSec::general, "site_host",$sSiteUrl);
        //$ini->set(S_CfgSec::general, "ftp_user","");
        //$ini->set(S_CfgSec::general, "ftp_passwd","");
        
        //local
        $ini->set(S_CfgSec::local, "mysql_db",$sMysqlDb);
        $ini->set(S_CfgSec::local, "mysql_user",$sMysqlUser);
        $ini->set(S_CfgSec::local, "mysql_pass",$sMysqlPass);
        //remote
        $ini->set(S_CfgSec::remote, "mysql_db",$sMysqlDb);
        //$ini->set(S_CfgSec::remote, "mysql_user","");
        //$ini->set(S_CfgSec::remote, "mysql_pass","");
        $ini->save();
        $prompt->printf("INI conf changed");
        
        
        // move commands on new folder
        $prompt->setCorePath($hw2CoreTarget);
        
        // 3) recreate symlink
        $prompt->loaderCmd(S_BashScr::symlink);
        
        $option = array(); //prevent problems
 
        $option['driver']   = 'mysql';            // Database driver name
        $option['host']     = S_Factory::getConf(S_CfgSec::mysql, "host");    // Database host name
        $option['user']     = $sMysqlUser;       // User for database authentication
        $option['password'] = $sMysqlPass;   // Password for database authentication
        $db = & \Hwj\JDatabase::getInstance( $option ); 
        $db->setQuery("CREATE DATABASE ".$sMysqlDb);
        $db->execute();
        $prompt->printf("DB $sMysqlDb Created");
        
        // 4) import db
        $prompt->loaderCmd(S_BashScr::mysql_dump);
        
        // 5) create git structure
        //S_Cli_Git::init($sAlias);

        // 6) create remote user [?] and sync db and files
        //$prompt->loaderCmd(S_BashScr::dbsync,Array("CGIT_REMOTE"=>$sAlias));
        
        parent::init();
    }
} 

?>
