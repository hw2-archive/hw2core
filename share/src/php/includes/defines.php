<?php namespace Hw2;

defined('HW2_CORE_EXEC') or die('Restricted access');

// PHP ini CONF
ini_set('display_errors', 1);
//error constants at:
//http://www.php.net/manual/en/errorfunc.constants.php
//error_reporting(E_ALL & ~(E_STRICT|E_NOTICE) );

ini_set('error_reporting',E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING));

ini_set('xdebug.remote_host','localhost');
ini_set('xdebug.remote_port',9000);
ini_set('xdebug.remote_handler','dbgp');
ini_set('xdebug.max_nesting_level',1000);

Abstract class S_ConstDefines {
    public static function toArray() {
        $class = new \ReflectionClass(get_called_class());
        $consts = $class->getConstants();
        return $consts;
    }
}

class S_CoreDef extends S_ConstDefines {
    const hw2ext = "hw2ext";
}

class S_PlatformList extends S_ConstDefines {
    const hw2j = "hw2j";
    const hw2core= "hw2core";
}

class S_StringFormat extends S_ConstDefines {
    const ini = 1;
    const json = 2;
}

// routing type
/*class Hw2RType {
    const def=0;
    const geo=1;
    const user=2;
}*/

final class S_SiteSide {
    const site=0; //
    const admin=1;
    const both=2;
}

final class S_CfgSec {
    const hw2core = "hw2core"; //internal
    const info = "info";
    const sync = "sync";
    const dev = "dev";
    const mysql = "mysql";
    const general = "general";
    const ftp = "ftp";
    const ssh = "ssh";
    const local = "local";
    const remote = "remote";
    const shared_paths = "shared_paths";
    const local_paths = "local_paths";
    const remote_paths = "remote_paths";
}

// version defines
define ("HW2_CORE_VERSION", "3.2.7");
define ("HW2_DB_VERSION", 1);

/*
 *  path definitions
 */
// check the platform path
if (defined('_JEXEC')) { 
    define("IS_SITE",JPATH_ROOT==JPATH_BASE);
    define("HW2_PLATFORM_NAME",S_PlatformList::hw2j);
} /*else if ( another platform ) */
else {
    define("IS_SITE",true);
    //calling core from an app, the platform path is the dev path
    define("HW2_PLATFORM_NAME",S_PlatformList::hw2core);
}

?>
