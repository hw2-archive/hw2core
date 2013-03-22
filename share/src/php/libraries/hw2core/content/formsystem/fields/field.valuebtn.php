<?php namespace Hw2;
S_Core::checkAccess();

class S_FS_ValueBtn {
    const value="_value";
    
    public static function _(S_FS_Content $form, $id, $title, $options = null, $table = HW2FS_TABLE_CONTENT, $tfield = HW2FS_FIELD_CONTENT) {
        $nc=new S_CT_NodeMgr($form); //node creator
        $node=$nc->_(S_FS_FormTag::cname(), $id.'_form', $title, false, $options, false, false);
        $node->setChild($nc->_(S_FS_HiddenField::cname(), $id. '_value', $title, false, $options, false, false));
        $node->setChild($nc->_(S_FS_SubmitButton::cname(), $id. '_btn', $title, false, $options, false, false));
        return $node;
    }
}
?>
