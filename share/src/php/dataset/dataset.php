<?php namespace Hw2;
S_Core::checkAccess();


class S_DS_Builder extends S_Object {
    public static function init() {
        if (!parent::initReg()) {
            S_Core::callGlobal(S_CronJob_List::cname(), "init");
            S_Core::callGlobal(S_Form_List::cname(), "init");
            S_Core::callGlobal(S_Acl_List::cname(), "init");
            S_Core::callGlobal(S_Conf_List::cname(), "init");
            
            if (S_Acl::checkCustomAcl("hw2ext_adminpanel"))
                S_QuickIcons::addCustomQuickIcon('hw2ext_adminpanel','index.php?hw2ext=adminpanel','icon-analytic.png','Hw2 Panel');
        }
    }
}
?>
