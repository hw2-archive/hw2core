<?php namespace Hw2;
S_Core::checkAccess();

use Hw2\S_Paths as SP;

S_Loader::addCss("s_css_layout", Array(SP::key("s_components"),'cms','com_backup','view','styles','layout.css.php'),'com_backup', S_CssMgr::ref,false);
S_Loader::addPath("s_css_transdmin",  Array(SP::key("s_css"), 'com_backup','transdmin.css'), S_PathType::css);
//S_Loader::addPath("s_js_jquery", S_Loader::getIncPath("s_js") . 'jquery_1_2_6.js', S_PathType::js);
S_Loader::addPath("s_js_jnice",  Array(SP::key("s_js"), 'jnice.js'), S_PathType::js);
?>
<div id="com_backup">
    <div class="wrapper">
        <!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
        <h1><span>Backup & Restore</span></h1>

        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <ul class="mainNav">
            <li><a href="<?php echo $query . "&action=manage" ?>" class="active">DASHBOARD</a></li> <!-- Use the "active" class for the active menu item  -->
            <li><a href="<?php echo $query . "&action=backup" ?>">BACKUP</a></li>
            <li><a href="<?php echo $query . "&action=restore" ?>">RESTORE</a></li>
        </ul>
        <!-- // #end mainNav 
        <script type="text/javascript">
            // cron job
            $.get('<?php echo $query . "&action=backup"; ?>');
        </script>-->
    </div>
<!-- // #wrapper -->
<?php
$input = S_Uri::getInput();
$action = $input->get("action");

switch ($action) {
    case "backup":
        require_once $curDir . DS . 'view' . DS . 'pages' . DS . 'backup.php';
        break;
    case "restore":
        require_once $curDir . DS . 'view' . DS . 'pages' . DS . 'restore.php';
        break;
    case "delete":
        require_once $curDir . DS . 'controller' . DS . 'delete.php';
        break;
    default:
        require_once $curDir . DS . 'view' . DS . 'pages' . DS . 'manage.php';
        break;
}
?>

</div>