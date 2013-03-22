<?php namespace Hwj;

require_once __DIR__.DS."defines.php";

//TODO: fix paths
my_defined('HW2_CORE_EXEC') or die();

my_define_d("_JEXEC", 1);
my_define_d('JPATH_ROOT', \Hw2\S_Paths::I()->get('s_jplatform').DS.'cms');
my_define_d('JPATH_BASE', JPATH_ROOT);
my_define_d('JPATH_SITE', JPATH_ROOT);
my_define_d('JPATH_CONFIGURATION', HW2PATH_LOCAL_CONF . DS . "user_conf");
//extra
my_define_d('JPATH_ADMINISTRATOR', JPATH_ROOT . DS . 'administrator');
my_define_d('JPATH_LIBRARIES'    , JPATH_ROOT . DS . 'libraries');
my_define_d('JPATH_PLUGINS'      , JPATH_BASE . DS . 'plugins');
my_define_d('JPATH_INSTALLATION' , JPATH_ROOT . DS . 'installation');
my_define_d('JPATH_THEMES'       , JPATH_BASE . DS . 'templates');
my_define_d('JPATH_CACHE'        , JPATH_BASE . DS . 'cache');
my_define_d('JPATH_MANIFESTS'    , JPATH_ADMINISTRATOR . '/manifests');

\Hw2\S_Loader::addPath("s_jimport", JPATH_ROOT . DS . 'libraries' . DS . 'import.legacy.php', \Hw2\S_PathType::php, true, "require_once");
\Hw2\S_Loader::addPath("s_jimport", JPATH_ROOT . DS . 'libraries' . DS . 'cms.php', \Hw2\S_PathType::php, true, "require_once");

JFormHelper::addFieldPath(JPATH_ROOT . DS . 'libraries' . DS . 'cms'.DS.'form'.DS.'field');
?>
