<?php namespace Hw2;
S_Core::checkAccess(true);

class S_1_5_jAcl {
    private static function _addPlatformAcl(&$Acl,$option, $gid, $name, $task) {
        if (!$name && !$task) {
            if ($gid == GID_MOD)
                $Acl->addACL( $option, 'manage', 'users', 'manager' );
            if ($gid <= GID_ADMIN)
                $Acl->addACL( $option, 'manage', 'users', 'administrator' );
            if ($gid <= GID_SADMIN)
                $Acl->addACL( $option, 'manage', 'users', 'super administrator' );
        }
    }

    public static function initCustomAcl(&$Acl)
    {
        $acl=S_Acl::getCustomAcl();
        foreach ($acl as &$value) {
            self::_addPlatformAcl($Acl,$value[0], $value[1], $value[2], $value[3]);
        }
    }
}
?>
