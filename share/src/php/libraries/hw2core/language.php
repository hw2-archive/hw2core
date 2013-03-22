<?php namespace Hw2;
S_Core::checkAccess();

Final Class S_Language extends S_Object {
    
    const default_lang="en-GB";
    const err_text="Language error";
    
    private $lang;
    
    public function __construct($lang) {
        $this->lang=\Hwj\JFactory::getLanguage();
        if(!$lang)
            $lang=S_PApi::getLangTag();
        
        $this->lang->setLanguage ($lang);
        parent::__construct();
    }
    
    private function loadNeededFiles($file="general",$tag=null,$reload=false,$default=false) {
        $this->lang->load($file, HW2PATH_LOCAL, $tag, $reload, $default);
        $this->lang->load($file, HW2PATH_SHARE, $tag, $reload, $default);
    }
    
    /**
     * 
     * @param type $key
     * @param type $tag if not specified , get it from core config
     * @param type $default true if you want check in default lang if in current doesn't exists
     * @param type $jsSafe @see \Hwj\JText
     * @param type $interpretBackSlashes @see \Hwj\JText
     * @param type $script @see \Hwj\JText
     * @return null
     */
    public function getLangText($key, $file="general", $tag=null, $default=true, $jsSafe = false, $interpretBackSlashes = true, $script = false ) {
        $this->loadNeededFiles($file, $tag, false, $default);
        $text=\Hwj\JText::_($key, $jsSafe, $interpretBackSlashes, $script);
        // if we are not processing the default lang and the string is not found , then recall this function using default lang tag
        if ($tag != self::default_lang && ($text==$key || empty($text))) 
            return $this->getLangText ($key,$file,self::default_lang,false,$jsSafem,$interpretBackSlashes,$script);
            
        return $text;
    }
    
    private static function replaceMatches($matches) {
        $key=substr($matches[0], strlen('{HW2LANG:'),-1);
        return S_Language::I()->getLangText($key);
    }
    
    public static function callback( $matches ) { 
            $result=self::replaceMatches($matches); 
            if ($result)
                return $result;
            else
                return self::err_text;
    }
    
    public static function replaceInDocument($document) {
        $pattern='/{HW2LANG:([^}]*)}/i';
        
        $result=preg_replace_callback($pattern,'self::callback',$document);
        return !empty($result)? $result : $document; 
    }
}
?>
