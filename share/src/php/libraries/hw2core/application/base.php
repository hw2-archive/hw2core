<?php namespace Hw2;
S_Core::checkAccess();

class S_AppType extends S_TypeDef {
    public static function base() { return parent::_(); }
    public static function cli() { return parent::_(); }
    public static function web() { return parent::_(); }
}



?>
