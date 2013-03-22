<?php namespace Hw2;
S_Core::checkAccess();

class S_Acl_List extends S_Object {
    public static function init() {
        S_Acl::addCustomAcl('hw2ext_backup',GID_SADMIN,null,null);
        S_Acl::addCustomAcl('hw2ext_adminpanel',GID_SADMIN,null,null);
    }
}

?>
