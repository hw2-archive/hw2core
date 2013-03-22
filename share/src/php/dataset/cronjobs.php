<?php namespace Hw2;
S_Core::checkAccess();

class S_CronJob_List extends S_Object {
    public static function init() {
        S_CronJobs::addCronJob(S_Com_Backup::cname());
        S_CronJobs::addCronJob(S_Gcounter::cname());
    }
}

?>
