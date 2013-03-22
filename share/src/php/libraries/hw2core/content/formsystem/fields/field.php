<?php namespace Hw2;
S_Core::checkAccess();

class S_FormField extends S_CT_Item {
    //public $title; // formal title for render
    /** @var \Hwj\JRegistry */
    public $settings; // \Hwj\JRegistry which contains field settings ( for example width , maxletters etc.)
    public $isFront=true; // is visible in frontend editor [TO IMPLEMENT]
    public $render=true; // if false, won't be renderized at all [TO IMPLEMENT]

                
    function __construct($id,$title,$table,$tfield,Array $options=null,$lang="*") {
        $this->settings=new \Hwj\JRegistry;
        parent::__construct($id,$title,$table,$tfield,$options,$lang);
    }
    
    public function getRequestData() {
        $input=new \Hwj\JInput;
        return S_Html::unquoteData($input->getString($this->id, ''));
    }
    
    public function renderOpen(S_FS_Content $form) {
        return "";
    }
    
    public function renderClose(S_FS_Content $form) {
        return "";
    }

    public function setSettings($val) {
        $this->_set("settings",!$val instanceof \Hwj\JRegistry ? new \Hwj\JRegistry($val) : $val);
        return $this;
    }
}


?>
