<?php namespace Hw2;
S_Core::checkAccess();

define("HW2FS_CONF_ID",-1);
define("HW2FS_TABLE_CONTENT","hw2_content");
define("HW2FS_FIELD_CONTENT","content");

S_Enum::D(Array(
    'HW2_DEFAULT_THUMBNAIL_WIDTH',
    'HW2_DEFAULT_THUMBNAIL_HEIGHT'
));

class S_FS_Actions extends S_TypeDef {
    public static function save() { return parent::_(1); }
    public static function delete() { return parent::_(2); }
}


class S_Form extends S_Object {  


    public static function clReg() { return S_ClassRegister::I(self); }
    
    public static function renderFormFields($state, S_CT_Sec $section, $refid=HW2FS_CONF_ID,$lang="*") {
        $callBacks=self::getCallBacks($section);
        if (!empty($callBacks)) {
                foreach ($callBacks as $clName) {
                    /* @var $class S_FS_Content */
                    $class=new $clName($refid,$lang);
                    if(!$class->checkState($state))
                        continue;
                    
                    $class->renderForm();
                }
            ?>
            <input type="hidden" name="hw2formtype" value="<?php echo $section->getValue() ?>" />
            <?php
        }
    }
    
    public static function storeFormFields(S_FS_Actions $action, $state, S_CT_Sec $section,$refid=HW2FS_CONF_ID,$lang="*",Hw2CtType $type=null) {
        $cb=self::getCallBacks($section);
        foreach ($cb as $clName) {
            /* @var $class Hw2Form_Struc */
            $class=new $clName($refid,$lang);
            if(!$class->checkState($state))
                continue;
            
            $class->storeForm($action);
        }
    }
    
    /**
     * 
     * @param type $state
     * @param Hw2CtType $type
     * @param S_CT_Sec $section
     * @param type $refid
     * @param type $lang
     * @return Array
     */
    public static function getValuesFromDb($state,Hw2CtType $type,S_CT_Sec $section=null, $refid=HW2FS_CONF_ID,$lang="*") {
        $cb=self::getCallBacks($section);
        $values=Array();
        foreach ($cb as $clName) {
            /* @var $class Hw2Form_Struc */
            $class=new $clName;
            if(!$class->checkState($state))
                continue;
            
            if (!$type)
                $type=$class->getCtype ();
            
            $values=array_merge($values,$class->getLoader()->getValuesFromDb($section, $class->getItems(), $type, $refid, $lang));
        }
        
        return $values;
    }
    
    public static function getContent(S_FormField $field,$contentList) {
        if ($field->multilang)
                return $contentList[$field->id."_".S_PApi::getLangTag()]->content; 
        
        return $contentList[$field->id]->content;
    }
    
    /** 
     * 
     * @param type $section
     * @param type $classSuffix define suffix Hw2Form_X class
     */
    public static function addCallBack(S_CT_Sec $section,$class) {
        self::clReg()->addCallBack($section->getValue(),$class);
    }
    
    /**
     * 
     * @param type $section 
     * @param type $type , type of methods to get: render etc..
     */
    public static function getCallBacks(S_CT_Sec $section) {
        return self::clReg()->getCallBacks($section->getValue());
    }
}

class S_FS_Content extends S_CT_Container {
    
    public static function getLayout() { return ; }
    
    /**
     *  get post parameters to store inside hw2 forms table
     *  @return \Hwj\JRegistry
     */
    function getParams() {
        
    }
    
    /**
     * 
     * @param type $state used to execute functions with only particular cases
     * @return boolean
     */
    function checkState($state) {
        // return true also if both are null
        return $this->getLayout() == $state;
    }

    /**
     * renderize form fields
     * @return html
     */
    function renderForm(S_Node $firstNode = null) {
        $renderer = new S_FS_Renderer($this);
        echo $renderer->renderFormFields($firstNode);
    }
    
    /**
     * store custom fields inside layout-related tables
     * @param array $fields actually not implemented, all fields must be stored
     * @param type $lang
     */
    function storeForm(S_FS_Actions $action,Array $fields = null) {
        if (!$fields)
            $fields = $this->getItems();
        
        $storage = new S_FS_Storage($this);
        $storage->store($action,$fields);
    }
}

?>
