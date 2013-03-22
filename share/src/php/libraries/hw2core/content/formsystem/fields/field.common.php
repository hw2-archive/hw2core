<?php namespace Hw2;
S_Core::checkAccess();

class S_FS_SingleLineText extends S_FormField {
    public function renderOpen(S_FS_Content $form) {
        $html = '';
        $size = 80;
        $maxLength = 150;
        $attributes = '';
        $html .= "<div class=\"single_line_text\">";
        $html .= S_FS_Renderer::renderFieldLabel($this);
        $html .= S_FS_Renderer::renderTextBox($this->id, $this->getVal(), $size, $maxLength, $attributes);
        $html .= "</div>";
        return $html;
    }
}

class S_FS_NumericBox extends S_FormField {
    public function renderOpen(S_FS_Content $form) {
        $html = '';
        $size = 30;
        $maxLength = 10;
        $attributes = '';
        $html .= "<div class=\"hw2fs_numeric_box\">";
        $html .= S_FS_Renderer::renderFieldLabel($this);
        $html .= S_FS_Renderer::renderTextBox($this->id, $this->getVal(), $size, $maxLength, $attributes);
        $html .= "</div>";
        return $html;
    }
    
    public function setVal($val) {
        if (is_numeric($val))
            $this->val=$val;
    }
}

class S_FS_Password extends S_FormField {
    const salt="x3woSE.we123";
    
    public function renderOpen(S_FS_Content $form) {
        $html = '';
        $size = 30;
        $maxLength = 10;
        $attributes = '';
        $html .= "<div class=\"hw2fs_numeric_box\">";
        $html .= S_FS_Renderer::renderFieldLabel($this);
        $html .= "<input type = \"password\" name = \"".$this->id."\" value = \"".$this->getValDecoded()."\"".$this->title."\">";
        $html .= "</input>";
        $html .= "</div>";
        return $html;
    }
    
    public function getRequestData() {
        $input=new \Hwj\JInput;
        $val= S_Html::unquoteData($input->getString($this->id, ''));
        $password = base64_encode(gzcompress(base64_encode($val)));
        return $password;
    }
    
    public function getValDecoded() {
        $decoded= base64_decode(gzuncompress(base64_decode($this->val)));
        return $decoded;
    }
}

class S_FS_SubmitButton extends S_FormField {
    public function renderOpen(S_FS_Content $form) {
        $html = '';
        $html .= "<input type = \"submit\" name = \"".$this->id."\" value = \"".$this->title."\">";
        $html .= "</input>";
        return $html;
    }
}

class S_FS_FormTag extends S_FormField {
    public function renderOpen(S_FS_Content $form) {
        $html = "<form name='".$this->id."' action='' method='".$this->settings->get("form_method")."'>";
        return $html;
    }
    
    public function renderClose(S_FS_Content $form) {
        $html = '</form>';
        return $html;
    }
}

class S_FS_HiddenField extends S_FormField {
    public function renderOpen(S_FS_Content $form) {
        $html = '';
        $html .= S_FS_Renderer::renderHiddenField($this->id, $this->settings->get("default_value"));
        return $html;
    }
}

class S_CT_MultiLangItem extends S_FormField {
    
}


class S_FS_VoidField extends S_FormField {
    
}

?>
