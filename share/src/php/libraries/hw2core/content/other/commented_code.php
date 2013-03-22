<?php

/* PSEUDO CODE FOR NESTED JSON FORMAT 
$path=Array();
//create parents path
while ($parent=$node->getParent() && $parent->getValue()!=S_Tree::head) {
    $path[]=$parent;
} 

// if parent path is not empty , this loop will put correct data in $fieldContent
for ($i=count($path)-1;$i==0;$i--) {
    $data=new \Hwj\JRegistry($fieldContent);
    $f=$this->form->getField($path[$i]->getUid());
    $fieldContent=$data->get($f->getId());
} */


    /*
    public function _storeMultiLineText($elementName, $field, $data) {
        $content = array();
        $value = $data[$elementName];
        $fieldId = $data[$elementName . '_fieldid'];
        $action = ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');

        $settings = new \Hwj\JRegistry();
        $settings->loadString($field->settings);

        if ((int) $settings->get('mlt_max_num_chars')) {
            if (function_exists('mb_substr_count') && function_exists('mb_substr')) {
                $numNewLines = mb_substr_count($value, "\r\n", 'UTF-8');
                $value = mb_substr($value, 0, (int) $settings->get('mlt_max_num_chars') + $numNewLines, 'UTF-8');
            } else {
                $numNewLines = substr_count($value, "\r\n");
                $value = substr($value, 0, (int) $settings->get('mlt_max_num_chars') + $numNewLines);
            }
        }

        $content[] = new HW2_FieldContent($fieldId, 'VALUE', $value, $action);

        return $content;
    }

    public function _storeMultiLineEditor($elementName, $field, $data) {
        $content = array();
        $value = $data[$elementName];
        $fieldId = $data[$elementName . '_fieldid'];
        $action = ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
        $content[] = new HW2_FieldContent($fieldId, 'VALUE', $value, $action);

        return $content;
    }

    public function _storeSingleSelectList($elementName, $field, $data) {
        $content = array();
        $value = $data[$elementName];
        $fieldId = $data[$elementName . '_fieldid'];
        $action = ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
        $content[] = new HW2_FieldContent($fieldId, 'VALUE', $value, $action);

        return $content;
    }

    public function prepareSubmittedData($fields) {
        $this->preparedData = array();

        foreach ($fields as $field) {
            $functionName = $field->fieldtypeid > HW2_FIELDTYPE_HW2START_VALUE ? '_prepare_hw2' : '_prepare' . $this->HW2_FIELD_FUNCTION_MAPPING[$field->fieldtypeid];
            $this->$functionName('t' . $field->id, $field, $this->preparedData);
        }
    }
    
    public function _prepareMultiLineText($elementName, $field, &$data) {
        $data[$elementName] = HtmlHelper::unquoteData(JRequest::getVar($elementName, '', 'post', 'string', JREQUEST_ALLOWRAW));
        $data[$elementName . '_fieldid'] = JRequest::getInt('hid' . $elementName, 0, 'post');
    }

    public function _prepareMultiLineEditor($elementName, $field, &$data) {
        $data[$elementName] = HtmlHelper::unquoteData(JRequest::getVar($elementName, '', 'post', 'string', JREQUEST_ALLOWRAW));
        $data[$elementName . '_fieldid'] = JRequest::getInt('hid' . $elementName, 0, 'post');
    }

    public function _prepareSingleSelectList($elementName, $field, &$data) {
        $data[$elementName] = HtmlHelper::unquoteData(JRequest::getVar($elementName, '', 'post', 'string', JREQUEST_ALLOWRAW));
        $data[$elementName . '_fieldid'] = JRequest::getInt('hid' . $elementName, 0, 'post');
    }
    */


 /*
      public function _renderMultiLineText($field, $fieldName, $fieldValues, $fieldDescription, $parms) {
      $html = '';
      $fieldHtml = '';
      $attribs = '';
      $maxNumChars = (int) $field->settings->get('mlt_max_num_chars');
      $value = self::_getFieldValue($fieldValues, 'VALUE');

      if (!$attribs) {
      $attribs = $parms[0];
      $attribs .= ' class="text_area"';
      }

      $fieldHtml .= ' ' . $attribs;

      if ($maxNumChars) {
      if (function_exists('mb_substr_count') && function_exists('mb_substr') && function_exists('mb_strlen')) {
      $numNewLines = mb_substr_count($value, "\r\n", 'UTF-8');
      $charsRemaining = $maxNumChars + $numNewLines - mb_strlen($value, 'UTF-8');
      $fieldValue = mb_substr($value, 0, $maxNumChars + $numNewLines, 'UTF-8');
      } else {
      $numNewLines = substr_count($value, "\r\n");
      $charsRemaining = $maxNumChars + $numNewLines - strlen($value);
      $fieldValue = substr($value, 0, $maxNumChars + $numNewLines);
      }

      if ($charsRemaining < 0) {
      $charsRemaining = 0;
      }

      $fieldHtml .= ' onKeyDown="F2C_limitTextArea(this.form.' . $fieldName . ',this.form.' . $fieldName . 'remLen,' . $maxNumChars . ');" onKeyUp="F2C_limitTextArea(this.form.' . $fieldName . ',this.form.' . $fieldName . 'remLen,' . $maxNumChars . ');"';
      }

      $html .= '<div class="f2c_field">';
      $html .= '<textarea name="' . $fieldName . '" id="' . $fieldName . '"' . $fieldHtml . '>' . $value . '</textarea>';

      if ($maxNumChars) {
      $html .= '<br/><input readonly type="text" name="' . $fieldName . 'remLen" size="6" maxlength="6" value="' . $charsRemaining . '"> ' . Jtext::_('COM_FORM2CONTENT_CHARACTERS_LEFT');
      }

      $html .= $fieldDescription;
      $html .= self::renderHiddenField('hid' . $fieldName, self::_getFieldContentId($fieldValues, 'VALUE'));
      $html .= '</div>';

      return $html;
      }

      public function _renderMultiLineEditor($field, $fieldName, $fieldValues, $fieldDescription, $parms) {
      $editor = & JFactory::getEditor();
      $value = self::_getFieldValue($fieldValues, 'VALUE');
      $html = '';
      $width = $parms[0];
      $height = $parms[1];
      $col = $parms[2];
      $row = $parms[3];

      $html .= '<div class="f2c_field">';
      $html .= $editor->display($fieldName, $value, $width, $height, $col, $row);
      $html .= $fieldDescription;
      $html .= self::renderHiddenField('hid' . $fieldName, self::_getFieldContentId($fieldValues, 'VALUE'));
      $html .= '</div>';

      return $html;
      }

      public function _renderSingleSelectList($field, $fieldName, $fieldValues, $fieldDescription, $parms) {
      $html = '';
      $fieldValue = self::_getFieldValue($fieldValues, 'VALUE');
      $listOptions = null;

      $html .= '<div class="f2c_field">';

      if ($field->settings->get('ssl_show_empty_choice_text')) {
      $listOptions[] = JHTMLSelect::option('', $field->settings->get('ssl_empty_choice_text'));
      }

      if (count((array) $field->settings->get('ssl_options'))) {
      foreach ((array) $field->settings->get('ssl_options') as $key => $value) {
      $listOptions[] = JHTMLSelect::option($key, $value);
      }
      }

      if ((int) $field->settings->get('ssl_display_mode') == 0) {
      $html .= JHTMLSelect::genericlist($listOptions, $fieldName, '', 'value', 'text', $fieldValue);
      } else {
      $html .= JHTMLSelect::radioList($listOptions, $fieldName, '', 'value', 'text', $fieldValue);
      }

      $html .= $fieldDescription;
      $html .= self::renderHiddenField('hid' . $fieldName, self::_getFieldContentId($fieldValues, 'VALUE'));
      $html .= '</div>';

      return $html;
      }

      public function _renderImage($field, $fieldName, $fieldValues, $fieldDescription, $parms) {
      $html = '';
      $imageValue = new \Hwj\JRegistry();
      $imageHelper = new F2C_Image();
      $uploadAttribs = 'class="inputbox"';
      $deleteAttribs = 'class="inputbox"';
      $widthAltText = $parms[0];
      $maxLengthAltText = $parms[1];
      $widthTitle = $parms[0];
      $maxLengthTitle = $parms[1];

      $imageValue->loadString(self::_getFieldValue($fieldValues, 'VALUE'));

      $html .= '<div class="f2c_field">';
      $html .= '<table><tr><td>&nbsp;</td><td>';
      $html .= '<input type="file" id="' . $fieldName . '_fileupload" name="' . $fieldName . '_fileupload" ' . $uploadAttribs . '>&nbsp;';
      $html .= '<input type="button" onclick="clearUpload(\'' . $fieldName . '_fileupload\');return false;" value="' . Jtext::_('COM_FORM2CONTENT_CLEAR_FIELD') . '" />&nbsp;';
      $html .= '<input type="checkbox" id="' . $fieldName . '_del" name="' . $fieldName . '_del" ' . $deleteAttribs . '>&nbsp;' . Jtext::_('COM_FORM2CONTENT_DELETE_IMAGE');

      $html .= F2C_Renderer::renderHiddenField($fieldName . '_filename', $imageValue->get('filename'));
      $html .= $fieldDescription;

      $html .= '</td></tr>';

      $html .= '<tr><td>' . Jtext::_('COM_FORM2CONTENT_ALT_TEXT') . ':</td>';
      $html .= '<td>' . self::_renderTextBox($fieldName . '_alt', $imageValue->get('alt'), $widthAltText, $maxLengthAltText, $field->settings->get('img_attributes_alt_text')) . '</td></tr>';

      $html .= '<tr><td>' . Jtext::_('COM_FORM2CONTENT_TITLE') . ':</td>';
      $html .= '<td>' . self::_renderTextBox($fieldName . '_title', $imageValue->get('title'), $widthTitle, $maxLengthTitle, $field->settings->get('img_attributes_title')) . '</td></tr>';

      $html .= '<tr><td valign="top">' . Jtext::_('COM_FORM2CONTENT_PREVIEW') . ':</td><td>';

      if ($imageValue->get('filename')) {
      $thumbSrc = S_jTools::pathCombine(F2C_Image::GetThumbnailsUrl($field->projectid, self::formId), $imageHelper->CreateThumbnailImageName($imageValue->get('filename'), $field->id));
      $html .= '<img id="' . $fieldName . '_preview" src="' . $thumbSrc . '" style="border: 1px solid #000000;">';
      }

      $html .= '</td></tr></table>';
      $html .= self::renderHiddenField('hid' . $fieldName, self::_getFieldContentId($fieldValues, 'VALUE'));
      $html .= '</div>';

      return $html;
      }

      public function _renderInfoText($field, $fieldName, $fieldValues, $fieldDescription, $parms) {
      $html = '';

      $html .= '<div class="hw2_field">';
      $html .= $field->settings->get('inf_text') . $fieldDescription;
      $html .= '</div>';

      return $html;
      }
     */

?>
