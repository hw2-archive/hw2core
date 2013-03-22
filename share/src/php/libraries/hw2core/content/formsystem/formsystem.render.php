<?php namespace Hw2;
S_Core::checkAccess();

class S_FS_Renderer extends S_CT_Loader {

    public function renderFormFields(S_Node $node = null,$loadFromDb=true) {
        $html = "";

        if ($loadFromDb)
            $this->content->getLoader()->getValuesFromDb ();
        
        if (!$node) {
            $tree= $this->content->getItemsTree();
            $node= $tree->getFirst();
        }

        $html .= '<div class="hw2form_fields_edit">';
        $html .= self::renderFieldNode($node);
        $html.="</div>";
        return $html;
    }
    
    // recursive function
    private function renderFieldNode(S_Node $node) {
        /* @var $field S_FormField */
        $field=$node->getValue();
        if ($field instanceof S_FormField) {
            $isMultilang=$field instanceof S_CT_MultiLangItem;
            $html .= '<div class="hw2fs-edit-field">';
            $html .= $isMultilang ? '<table>' : '';
            $html.=  $field->renderOpen($this->content);
        }
        $childrens=$node->getChildren();
        if (!empty($childrens)) {
            foreach ($childrens as $child) {
                $childField=$child->getValue();
                $html .= $isMultilang ? '<tr><td><span title="' . $childField->title . '">' . $childField->lang . '</span></td><td>' : '';
                $html.=self::renderFieldNode ($child);
                $html .= $isMultilang ? '</td></tr>' : '';
            } 
        }
        if ($field instanceof S_FormField) {
            $html.=  $field->renderClose($this->content);
            $html .= self::renderFieldDescription($field);
            $html .= $isMultilang ? '</table>' : '';
            $html .= '</div>';
        }
        
        return $html;
    }

    /*
     * 
     * 
     *  HELPERS
     * 
     * 
     */

    public static function renderHiddenField($name, $value) {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . S_Html::stringHTMLSafe($value) . '">';
    }

    public static function renderTextBox($name, $value = '', $size = '', $maxlength = '', $tags = '') {
        $html = '';
        $class = ($tags) ? '' : 'class="inputbox"';

        $html .= '<input type="text" ' . $class . ' name="' . $name . '" id="' . $name . '"';
        $html .= ($value != '') ? ' value= "' . S_Html::stringHTMLSafe($value) . '"' : '';
        $html .= $size ? ' size= "' . $size . '"' : '';
        $html .= $maxlength ? ' maxlength= "' . $maxlength . '"' : '';
        $html .= $tags . '/>';

        return $html;
    }

    public static function renderFieldLabel(S_FormField $field) {
        $label = '';

        $labelText = $field->title;
        $label = '<label for="t' . $field->id . '">' . $labelText . '</label>';

        return $label;
    }

    public static function renderFieldDescription(S_FormField $field) {
        $fieldLabel = $field->title;
        $fieldDescription = $field->settings->get("description");

        if ($fieldDescription) {
            $fieldDescription = '&nbsp;' . \Hwj\JHtml::tooltip($fieldDescription, $fieldLabel);
        }

        return $fieldDescription;
    }

   
}

?>