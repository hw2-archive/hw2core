<?php namespace Hw2;
define ("HW2_CORE_EXEC", 1);
defined("DS") or define('DS', DIRECTORY_SEPARATOR);
define("HW2PATH_CORE", dirname(__FILE__) );
define("HW2PATH_CORE_ORIGIN", realpath(HW2PATH_CORE.DS."share".DS."..".DS) ); // if hw2core is symlinked , it's the original path
define("HW2PATH_INCLUDES", HW2PATH_CORE_ORIGIN.DS."share".DS."src".DS."php".DS."includes" );

require_once HW2PATH_INCLUDES.DS."framework.php";

S_Core::init($argc,$argv);
?>
