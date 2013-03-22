<?php namespace Hw2;
S_Core::checkAccess();

final class S_CT_Tables {
    const content=HW2FS_TABLE_CONTENT;
}

final class S_CT_Sec extends S_TypeDef {
    static function jcategory() { return parent::_(1); }
    static function jarticle() { return parent::_(2); }
    static function jconfig() { return parent::_(3); }
    static function coreElement() { return parent::_(4); }
    static function f2c_article() { return parent::_(100); }
}
?>
