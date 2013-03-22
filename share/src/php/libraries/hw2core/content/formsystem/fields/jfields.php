<?php namespace Hw2;

class S_jFormField extends S_FormField {
    protected $JForm;
    public function __construct($id, $title, $table, $tfield, array $options = null, $lang = "*") {
        parent::__construct($id, $title, $table, $tfield, $options, $lang);
        $this->JForm = new \Hwj\JForm;
        $this->JForm->reset(true);
    }
    
    protected function _render($xmlstring) {
        $element=\simplexml_load_string($xmlstring);
        $this->JForm->setField($element);
        $this->JForm->setValue($this->id, null, $this->val);
        $html="";
        $html.=$this->JForm->getLabel($this->id);
        $html.=$this->JForm->getInput($this->id);
        return $html;
    }
}


class S_FS_jField_Text extends S_jFormField {
    public function renderOpen(S_FS_Content $form) {
        return $this->_render('<field name="'.$this->id.'" type="text" />');
    }
    
    public function getRequestData() {
        //return HtmlHelper::unquoteData(S_Uri::getInput()->getString($this->id, '', 'post'));
    }
}

class S_FS_jField_Media extends S_jFormField {
    public function __construct($id, $title, $table, $tfield, array $options = null, $lang = "*") {
        parent::__construct($id, $title, $table, $tfield, $options, $lang);
    }
    
    public function renderOpen(S_FS_Content $form) {
        return $this->_render('<field name="'.$this->id.'" type="media" />');
    }
    
    public function getRequestData() {
        //return HtmlHelper::unquoteData(S_Uri::getInput()->getString($this->id, '', 'post'));
    }
}
?>
