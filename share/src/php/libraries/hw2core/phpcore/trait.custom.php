<?php namespace Hw2;
S_Core::checkAccess();

abstract class S_CustomTrait extends S_Object {
    public static function I($ownerClass) {
        return parent::I($ownerClass);
    }
}
?>
