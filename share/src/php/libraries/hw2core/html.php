<?php namespace Hw2;
S_Core::checkAccess();

class S_Html extends S_Object {
        /**
     * 
     * @param string $html
     * @return string cleaned html code, or the original
     */
    public static function cleanHTML($html,$useDOM=true) {
        if (!$useDOM) {
            $tidy = new tidy();
            $tidy->parseString($html,array('show-body-only'=>true),'utf8');
            $tidy->cleanRepair();
            return $tidy;
        } else {
            $doc=new DOMDocument();
            $fix='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' .$html; //hack for DOMDocument
            if ($doc->loadHTML($fix))
                return $doc->saveHTML();
            else
                return $html;
        }
    }
    
    /**
     * 
     * @param string $s 
     * @param int $l
     * @param string $e
     * @param boolean $isHTML
     * @return string
     */
    public static function truncate($s, $l, $e = '...', $isHTML = false){
		$i = 0;
		$tags = array();
		if($isHTML){
			preg_match_all('/<[^>]+>([^<]*)/', $s, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			foreach($m as $o){
				if($o[0][1] - $i >= $l)
					break;
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				if($t[0] != '/')
					$tags[] = $t;
				elseif(end($tags) == substr($t, 1))
					array_pop($tags);
				$i += $o[1][1] - $o[0][1];
			}
                        $result=self::cleanHTML(substr($s, 0, $l = min(strlen($s),  $l + $i)) . (strlen($s) > $l ? $e : '')) ;
		} else 
                    $result=substr($s, 0, $l = min(strlen($s),  $l + $i)) . (strlen($s) > $l ? $e : '') ;
		return $result;
    }

    static public function getHw2MetaKeyParameters($metaKeys) {
        $keys = preg_split('/(?<!\\\)},/', $metaKeys);
        $result = null;
        if ($keys) {
            $param = $keys[0];
            $result = S_AltRegistry::create($param);
        }
        return $result;
    }
    
    /**
     * 
     * @param Hw2jJRegistry() $params
     */
    static public function getHw2SharedParam($params) {
        $par = $params->toArray();
        //unset($par['']);
        var_dump($par);
    }
    
    function HiddenField($name, $value)
    {
            return '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.htmlspecialchars($value).'">';
    }

    function detectUTF8($string)
    {
        return preg_match('%(?:
            [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )+%xs', 
        $string);
    }

    function stringHTMLSafe($string)
    {
            if(self::detectUTF8($string))
            {
                    $safeString = htmlentities ($string, ENT_COMPAT, 'UTF-8');
            }
            else
            {
                    $safeString = htmlentities ($string, ENT_COMPAT);
            }

            return $safeString;
    }

    function unquoteData($data)
    {
            if(get_magic_quotes_gpc())
            {
                    return(stripslashes($data));
            }
            else
            {
                    return $data;
            }
    }

    public function renderCalendar($valueFormatted, $valueRaw, $name, $id, $format = '%Y-%m-%d', $attribs = null)
    {
            static $done;

            if ($done === null) 
            {
                    $done = array();
            }

            $readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
            $disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
            if (is_array($attribs)) {
                    $attribs = \Hwj\JArrayHelper::toString($attribs);
            }

            if ((!$readonly) && (!$disabled)) {
                    // Load the calendar behavior
                    \Hwj\JHtml::_('behavior.calendar');
                    \Hwj\JHtml::_('behavior.tooltip');

                    // Only display the triggers once for each control.
                    if (!in_array($id, $done))
                    {
                            $document = \Hwj\JFactory::getDocument();
                            $document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
                            inputField: "'.$id.'",		// id of the input field
                            ifFormat: "'.$format.'",	// format of the input field
                            button: "'.$id.'_img",		// trigger for the calendar (button ID)
                            align: "Tl",				// alignment (defaults to "Bl")
                            singleClick: true,
                            firstDay: '.\Hwj\JFactory::getLanguage()->getFirstDay().'
                            });});');
                            $done[] = $id;
                    }
            }

            return '<input type="text" title="'.(0!==(int)$valueRaw ? \Hwj\JHtml::_('date',$valueRaw):'').'" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($valueFormatted, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'.
                            ($readonly ? '' : \Hwj\JHTML::_('image','system/calendar.png', \Hwj\JText::_('JLIB_HTML_CALENDAR'), array( 'class' => 'calendar', 'id' => $id.'_img'), true));
    }

    /**
     * Create the HTML page title.
     *
     * @param	string	$title	The title as provided by the component.
     *
     * @return	string	The title as it should be displayed in the browser.
     * @since	3.2.1
     */
    public function getPageTitle($title)
    {
            $app = \Hwj\JFactory::getApplication();

            if(empty($title))
            {
                    $title = $app->getCfg('sitename');	
            }
            else
            {
                    // test the version of of Joomla, see if we have 1.7.x or higher
                    list($major, $minor, $revision) = explode('.', JVERSION);

                    if((int)$minor > 6)
                    {
                            switch($app->getCfg('sitename_pagetitles', 0))
                            {
                                    case 0: // No
                                            break;
                                    case 1: // After
                                            $title = \Hwj\JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
                                            break;
                                    case 2: // Before
                                            $title = \Hwj\JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
                                            break;
                            }
                    }
                    else
                    {
                            switch($app->getCfg('sitename_pagetitles', 0))
                            {
                                    case 0: // No
                                            break;
                                    case 1: // After
                                            $title = \Hwj\JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
                                            break;
                            }
                    }
            }

            return $title;		
    }
    
}
?>
