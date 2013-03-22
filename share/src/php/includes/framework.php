<?php namespace Hw2;

defined('HW2_CORE_EXEC') or die('Restricted access');

require_once "defines.php";

require_once "application.php";
require_once "paths.php";

use Hw2\S_Paths as SP;

$SP=SP::I(); // init 

define("HW2CORE_FOLDER_NAME", basename(HW2PATH_CORE));

require_once "class.index.php";
require_once "loader.php";
if (file_exists(HW2PATH_LOCAL.DS.'loader.php'))
    require_once HW2PATH_LOCAL.DS."loader.php";

//init JPlatform
S_Loader::loadJPlatform();

// init index
S_CIndex::initIndex();
S_CIndex::regClasses(); // load cached paths

S_Core::callGlobal("Hw2\S_Loader", "loadFramework");

?>
