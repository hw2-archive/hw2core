<?php namespace Hw2;
S_Core::checkAccess();

class S_PApi {
    private static function getClass() {
        $cName="Hw2\\".S_Factory::getPlatform()."_Api";
        return new $cName();
    } 
    
    public static function getQueryVar($var) {
        return self::getClass()->getQueryVar($var);
    }
    
    public static function getLangTag() {
        return self::getClass()->getLangTag();
    }
    
    public static function isAdmin() {
        return self::getClass()->isAdmin();
    }
    
    public static function isBackend() {
        return self::getClass()->isBackend();
    }
}

class hw2j_Api {    
    public static function getLangTag() {
        $lang =& \JFactory::getLanguage();
        $locales = $lang->getLocale();
        return $lang->getTag();
    }
    
    
    public function isAdmin() {
        $user =& \JFactory::getUser();
        
        switch (S_Factory::getPVersion()) {
            case "latest":
                $isAdmin=$user->get('isRoot');
            break;
            case "1_5":
                $isAdmin=$user->usertype == "Super Administrator";
            break;
        }
        
        return $isAdmin;
    }
    
    public function isBackend() {
        $app = \JFactory::getApplication();
        return !$app->isSite();
    }
}

class hw2core_Api {
    public static function getLangTag() {
        return S_Language::default_lang;
    }
    
    
    public function isAdmin() {
        return null;
    }
    
    public function isBackend() {
        return null;
    }
}
?>
