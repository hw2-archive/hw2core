<?php namespace Hw2;
S_Core::checkAccess();

class S_Acl {
    private static $customAcl=Array();
    
    public static function getCustomAcl() {
        return self::$customAcl;
    }
    
    public static function addCustomAcl($option, $gid, $name, $task)
    {
            self::$customAcl[] = array($option, $gid, $name, $task);
    }

    // to compare multidimensional arrays
    function compareDeepValue($a, $b)
    {
        if ($a==$b)
            return 0;

        return -1; // not important if negative or positive in this case
    }

    //TODO: try to merge checkCustomAcl and checkCustomAclTask in single function
    public static function checkCustomAclTask($option = "", $CustomUrl = null,$verbose=false)
    {
        $result=true;
        $urlParams=Array();
        $hw2User= S_jUser::getInstance(\JFactory::getUser());
        $gPath= $hw2User->getGroupPath();

        $url="";
        if (!empty($CustomUrl) && is_array($CustomUrl))
            $urlParams=$CustomUrl;
        else {
            if (is_string($CustomUrl))
                $url=$CustomUrl;
            else
                $url= S_Uri::hw2_curPageURL();

            $urlParams=  S_Uri::queryToArray($url);
        }
            
        if (empty($option))
            $option=$urlParams["option"];
                    
        
        for ($i=0; $i < count(self::$customAcl); $i++)
        {
            $mAcl =& self::$customAcl[$i];
            if (strcasecmp( $option, $mAcl[0] ) == 0)
            {                
                if (empty($mAcl[2]) && empty($mAcl[3])) {
                    $result=self::_checkLvl($mAcl[1], $gPath);
                    continue;
                }
                
                if (empty($urlParams) || empty($mAcl[3]))
                    continue;

                $check = array_uintersect_uassoc($mAcl[3], $urlParams, "self::compareDeepValue","self::compareDeepValue");
                // using array we MUST have the correspondance for all values
                if (is_array($CustomUrl) && count($check) != count($urlParams)
                        || (!empty($url) && count($check)==0))
                    continue;

                // if macl is present in group path, means that the user have privileges
                return self::_checkLvl($mAcl[1], $gPath);
            }
        }
        
        if (!$result && $verbose) {
            trigger_error("Restricted Access",E_USER_ERROR);
            die();
        }

        return $result;
    }


    public static function checkCustomAcl($option, $subname = null,$verbose=false)
    {
        $hw2User= S_jUser::getInstance(\JFactory::getUser());
        $gPaths= $hw2User->getGroupPath();
        $result=true;
        for ($i=0; $i < count(self::$customAcl); $i++)
        {
            $mAcl =& self::$customAcl[$i];
            if (strcasecmp( $option, $mAcl[0] ) == 0)
            {                    
                // se stiamo cercando l'acl di un sottomenu
                if (!empty($subname) && strcasecmp($subname, $mAcl[2]) == 0)
                    return self::_checkLvl($mAcl[1], $gPaths);

                // se stiamo cercando l'acl del componente base, 
                // oppure se non è stato trovato il sottomenu
                if (empty($mAcl[2]) && empty($mAcl[3]))
                    $result=self::_checkLvl($mAcl[1], $gPaths);
            }
        }
        
        if (!$result && $verbose) {
            trigger_error("Restricted Access",E_USER_ERROR);
            die();
        }

        return $result;
    }
    
    private static function _checkLvl($lvl,$gPaths) {
        foreach ($gPaths as $GIDs)
            // if super admin, all privileges are grants ...else check in list
            if (in_array (GID_SADMIN, $GIDs) || in_array ($lvl, $GIDs))
                return true;
            
        return false;
    }
    
    /**
     * 
     * @param type $list passing reference of the list will modified it
     * @param type $key the key of the list that contains the url
     */
    public static function cleanList(&$list,$key) {
        $result=Array();
        foreach ($list as $item) {
            if ( self::checkCustomAclTask("",$item[$key]) )
                    $result[]=$item;
        }
        
        $list=$result;
    }
    
    
}
?>
