<?php namespace Hw2;
S_Core::checkAccess();

class S_Time {
    public static function daysToSec($days) {
        return $days*86400;
    }
    
}
?>
