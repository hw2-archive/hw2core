<?php namespace Hw2;
S_Core::checkAccess(true);

class S_LtType extends S_TypeDef {
    public static function stat() { return parent::_(1); }
    public static function dynamic() { return parent::_(2); }
}

class S_LtCat {
    const listlt="list";
    const bloglt="blog";
    const menult="menulayout";
}

class S_LtArt {
    const def="default";
    const menuitem="menuitem";
    const menuitem1="menuitem1";
}

class S_LtConf {
    const def="hw2conf";
}

?>
