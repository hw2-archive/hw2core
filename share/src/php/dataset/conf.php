<?php namespace Hw2;
S_Core::checkAccess();

class S_Conf_List extends S_Object {
    public static function init() {
        // ~23 hours is google analytic standard refresh
        S_Factory::addConf(S_CfgSec::hw2core, "gcounter_delay", 86000); 
    }
}

?>