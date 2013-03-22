<?php namespace Hw2;
S_Core::checkAccess();
//TO IMPLEMENT
class S_Com_Adminpanel extends S_Object {
    public static function init() {
        if (!S_PApi::isAdmin())
            return false;
        
        $name=S_CT_Item::normalizeId(S_FS_Config::save);
        $action=S_Uri::getInput()->get($name, false, "post");
        if ($action=="save") {
            S_Form::storeFormFields(S_FS_Actions::save(),S_LtConf::def, S_CT_Sec::jconfig());
        }
        
        ?>
            <div id="Hw2AdminPanel">
                <?php S_Form::renderFormFields(S_LtConf::def,S_CT_Sec::jconfig()); //[hw2] ?>
            </div>
        <?php
    }
    
}



?>
