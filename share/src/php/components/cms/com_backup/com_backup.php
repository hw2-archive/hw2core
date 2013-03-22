<?php namespace Hw2; 
S_Core::checkAccess();
//http://campstamba.com

class S_Com_Backup extends S_Object {    
    public static function run($action) {
        include("config.php");
        switch ($action) {
            case "backup":
                require_once $curDir.DS.'controller'.DS.'backup.php';
            break;
            case "restore":
                require_once $curDir.DS.'controller'.DS.'restore.php';
            break;
        }
    }
    
    public static function init() {
        include("config.php");
        if (S_Acl::checkCustomAcl("hw2ext_backup",null))
            require_once $curDir.DS.'view'.DS.'view.html.php';
    }
    
    public static function cronJob() {
        if (S_FS_Config::I()->getItemVal(S_FS_Config::bak_enabled)==1) {
            $delay=S_FS_Config::I()->getItemVal(S_FS_Config::bak_delay);
            $coreCT=S_CT_Core::I();
            $latestBackup=$coreCT->getContentVal(S_CT_Core::last_bak_time);
            if ( ( time() - $latestBackup ) >= $delay) {
                sleep(3);
                $coreCT->setContentVal(S_CT_Core::last_bak_time, time(), true);
                S_Com_Backup::run("backup");
            }
        }
    }
}
        
?>