<?php namespace Hw2;
S_Core::checkAccess();


class S_FS_ImageNode {
    public static function _(S_FS_Content $form, $id, $title, $multilang=false, $options = null, $table = HW2FS_TABLE_CONTENT, $tfield = HW2FS_FIELD_CONTENT) {
        $nc=new S_CT_NodeMgr($form); //node creator
        $node=$nc->_(S_FS_Image::cname(), $id, $title, $multilang, $options, $table, $tfield);
        $node->setChild($nc->_(S_FS_ImageMethod::cname(), $id. '_method', $title, $multilang, null, $table, $tfield));
        $node->setChild($nc->_(S_FS_ImageDel::cname(), $id. '_del', $title, $multilang, null, $table, $tfield));
        $node->setChild($nc->_(S_FS_ImagePath::cname(), $id. '_fileupload', $title, $multilang, null, $table, $tfield));
        $node->setChild($nc->_(S_FS_ImageFilename::cname(), $id. '_filename', $title, $multilang, null, $table, $tfield));
        $node->setChild($nc->_(S_FS_ImageAlt::cname(), $id. '_alt', $title, $multilang, null, $table, $tfield));
        $node->setChild($nc->_(S_FS_ImageTitle::cname(), $id. '_title', $title, $multilang, null, $table, $tfield));
        return $node;
    }
}

class S_FS_Image extends S_FormField {
    public function store(S_FS_Content $form,Array $preparedData) {
        $content = array();
        $imageContent = new \Hwj\JRegistry();
        $imageHelper = new S_FS_Image();
        $imageCurrent = new \Hwj\JRegistry();
        $value = '';

        // initialize with empty values
        $imageCurrent->set('filename', '');
        $imageCurrent->set('width', '');
        $imageCurrent->set('height', '');
        $imageCurrent->set('widthThumbnail', '');
        $imageCurrent->set('heightThumbnail', '');

        if ($fieldId) {
            $this->db->setQuery('SELECT * FROM #__f2c_fieldcontent WHERE id=' . (int) $fieldId);
            $fieldContent = $this->db->loadObject();

            if ($fieldContent->content) {
                $imageCurrent->loadString($fieldContent->content);
            }
        }

        if ($data[$elementName . '_del']) {
            $imageHelper->Delete($field->projectid, $this->formId, $data[$elementName . '_filename']);
        } else {
            switch ($data[$elementName . '_method']) {
                case 'upload':
                    $uploadfile = $data[$elementName . '_fileupload'];

                    if ($uploadfile['size']) {
                        // delete current image, if there is one
                        $imageHelper->delete($field->projectid, $this->formId, $imageCurrent->get('filename'));

                        // Store the uploaded image
                        $settings = new \Hwj\JRegistry();

                        $settings->loadString($field->settings);

                        $maxImageWidth = 10000;
                        $maxImageHeight = 10000;
                        $uploadFileName = $uploadfile['name'];
                        $imagePath = S_jTools::pathCombine(HW2_Image::GetImagesRootPath(), 'p' . $field->projectid);
                        $imagePath = S_jTools::pathCombine($imagePath, 'f' . $this->formId);
                        $thumbsPath = S_jTools::pathCombine($imagePath, 'thumbs');
                        $imageFileName = HW2_Image::CreateFullImageName($uploadFileName, $field->id);
                        $imageFileLocation = S_jTools::pathCombine($imagePath, $imageFileName);
                        $imageFileLocationTmp = S_jTools::pathCombine($imagePath, '~' . $imageFileName);
                        $thumbnailLocation = S_jTools::pathCombine($thumbsPath, HW2_Image::CreateThumbnailImageName($uploadFileName, $field->id));
                        $maxImageWidth = $settings->get('img_max_width', 10000);
                        $maxImageHeight = $settings->get('img_max_height', 10000);

                        if (!JFolder::exists($thumbsPath))
                            JFolder::create($thumbsPath);

                        if (JFile::upload($uploadfile['tmp_name'], $imageFileLocationTmp)) {
                            $imageContent->set('filename', $imageFileName);

                            // resize image
                            if (!ImageHelper::ResizeImage($imageFileLocationTmp, $imageFileLocation, $maxImageWidth, $maxImageHeight, S_jConfig::getConf('jpeg_quality', 75))) {
                                JError::raiseError(401, JText::_('ERROR_IMAGE_RESIZE_FAILED'));
                                return false;
                            }

                            $imageContent->set('width', $maxImageWidth);
                            $imageContent->set('height', $maxImageHeight);

                            $defaultThumbnailWidth = S_jConfig::getConf('default_thumbnail_width', HW2_DEFAULT_THUMBNAIL_WIDTH);
                            $defaultThumbnailHeight = S_jConfig::getConf('default_thumbnail_height', HW2_DEFAULT_THUMBNAIL_HEIGHT);
                            $thumbnailWidth = $settings->get('img_thumb_width', $defaultThumbnailWidth);
                            $thumbnailHeight = $settings->get('img_thumb_height', $defaultThumbnailHeight);

                            // create thumbnail image
                            if (!ImageHelper::ResizeImage($imageFileLocationTmp, $thumbnailLocation, $thumbnailWidth, $thumbnailHeight, S_jConfig::getConf('jpeg_quality', 75))) {
                                JError::raiseError(401, JText::_('ERROR_IMAGE_RESIZE_FAILED'));
                                return false;
                            }

                            $imageContent->set('widthThumbnail', $thumbnailWidth);
                            $imageContent->set('heightThumbnail', $thumbnailHeight);

                            JFile::delete($imageFileLocationTmp);
                        }
                    } else {
                        // no file was uploaded, check if there was a previous file
                        $imageContent = $imageCurrent;
                    }
                    break;

                case 'copy':
                    $srcImage = $data[$elementName . '_location'];
                    $srcThumb = $data[$elementName . '_thumblocation'];
                    $filename = $data[$elementName . '_filename'];
                    $imagePath = S_jTools::pathCombine(HW2_Image::GetImagesRootPath(), 'p' . $field->projectid);
                    $imagePath = S_jTools::pathCombine($imagePath, 'f' . $this->formId);
                    $thumbsPath = S_jTools::pathCombine($imagePath, 'thumbs');
                    $imageFileName = HW2_Image::CreateFullImageName($filename, $field->id);
                    $imageFileLocation = S_jTools::pathCombine($imagePath, $imageFileName);
                    $thumbnailLocation = S_jTools::pathCombine($thumbsPath, HW2_Image::CreateThumbnailImageName($filename, $field->id));

                    JFolder::create($thumbsPath);
                    JFile::copy($srcImage, $imageFileLocation);
                    JFile::copy($srcThumb, $thumbnailLocation);

                    list($width, $height, $type, $attr) = getimagesize($imageFileLocation);
                    list($widthThumb, $heightThumb, $typeThumb, $attrThumb) = getimagesize($thumbnailLocation);

                    $imageContent->set('filename', $filename);
                    $imageContent->set('width', $width);
                    $imageContent->set('height', $height);
                    $imageContent->set('widthThumbnail', $widthThumb);
                    $imageContent->set('heightThumbnail', $heightThumb);
                    break;
            }

            $imageContent->set('alt', $data[$elementName . '_alt']);
            $imageContent->set('title', $data[$elementName . '_title']);
        }

        $value = $imageContent->__toString();
        $action = ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
        $content[] = new HW2_FieldContent($fieldId, 'VALUE', $value, $action);

        return $content;
    }
    
    public function renderOpen() {
        $html = '';
        return;
        $imageValue = $contents;
        $imageHelper = new S_FS_Image();
        $uploadAttribs = 'class="inputbox"';
        $deleteAttribs = 'class="inputbox"';
        $widthAltText = 10;
        $maxLengthAltText = 30;
        $widthTitle = 11;
        $maxLengthTitle = 20;

        if (empty($imageValue))
            $imageValue = self::_getImage($field->id, $section, $type, $refid, $lang);

        $html .= self::renderFieldLabel($field);
        $html .= '</br><table><tr><td>&nbsp;</td><td>';
        $html .= '<input type="file" id="' . $field->id . '_fileupload" name="' . $field->id . '_fileupload" ' . $uploadAttribs . '>&nbsp;';
        $html .= '<input type="button" onclick="S_jTools.clearInput(\'' . $field->id . '_fileupload\');return false;" value="Clear Field" />&nbsp;';
        $html .= '<input type="checkbox" id="' . $field->id . '_del" name="' . $field->id . '_del" ' . $deleteAttribs . '>&nbsp;Delete Image';

        $html .= self::renderHiddenField($field->id . '_filename', $contents[$field->id]->content);
        $html .= $field->description;

        $html .= '</td></tr>';

        $html .= '<tr><td>Alt. Text:</td>';
        $html .= '<td>' . self::_renderTextBox($field->id . '_alt', null/* $contents[$field->id]->params->get('alt') */, $widthAltText, $maxLengthAltText, $field->settings->get('img_attributes_alt_text')) . '</td></tr>';

        $html .= '<tr><td>Title:</td>';
        $html .= '<td>' . self::_renderTextBox($field->id . '_title', null/* $contents[$field->id]->params->get('title') */, $widthTitle, $maxLengthTitle, $field->settings->get('img_attributes_title')) . '</td></tr>';

        $html .= '<tr><td valign="top">Preview:</td><td>';

        if ($contents[$field->id]->content) {
            $thumbSrc = S_jTools::pathCombine(S_FS_Image::GetThumbnailsUrl($field->projectid, self::formId), $imageHelper->CreateThumbnailImageName($contents[$field->id]->content, $refid));
            $html .= '<img id="' . $field->id . '_preview" src="' . $thumbSrc . '" style="border: 1px solid #000000;">';
        }

        $html .= '</td></tr></table>';
        $html .= self::renderHiddenField('hid' . $field->id, $refid);

        return $html;
    }
}

class S_FS_ImageMethod extends S_FormField {
    public function getRequestData() {
        return S_Uri::getInput()->getString($this->id, 'upload', 'post');
    }
}

class S_FS_ImageDel extends S_FormField {
    public function getRequestData() {
        return S_Uri::getInput()->getVar($this->id, '', 'post');
    }
}

class S_FS_ImagePath extends S_FormField {
    public function getRequestData() {
        return S_Uri::getInput()->getString($this->id, '', 'files', 'array');
    }
}

class S_FS_ImageFilename extends S_FormField {
    public function getRequestData() {
        return S_Uri::getInput()->getVar($this->id, '', 'post');
    }
}

class S_FS_ImageAlt extends S_FormField {
    public function getRequestData() {
        return HtmlHelper::unquoteData(S_Uri::getInput()->getString($this->id, '', 'post'));
    }
}

class S_FS_ImageTitle extends S_FormField {
    public function getRequestData() {
        return HtmlHelper::unquoteData(S_Uri::getInput()->getString($this->id, '', 'post'));
    }
}

?>
