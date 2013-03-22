<?php namespace Hw2;
S_Core::checkAccess(true);

class S_jUser extends S_Object {
    /** @var JUser */ private $user=null;
    /** @var Array */ private $GIDs=null;
    private $groupPath=null;
    private $userFields=null;
    
    /**
     * 
     * @param JUser $user
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->GIDs  = S_jUserHelper::getGroups($user);
    }
    
    /**
     * 
     * @return S_jUser
     */
    public static function getInstance($user) {
        return parent::getInstance(func_get_args());
    }
    
    /**
     * 
     * @return Array , bidimensional array
     */
    function getGroupPath() {
        if (empty($this->groupPath))
            foreach ($this->GIDs as $gid )
                $this->groupPath[] = S_jUserHelper::loadGroupPath($gid);
    
        return $this->groupPath;
    }
    
    function getUserFields() {
        if (empty($this->$userFields))
            $this->userFields = S_jUserHelper::loadHw2UserFields($this->user->id);
        
        return $this->userFields;
    }
}


// static methods
class S_jUserHelper {    
    public static function getGroups($user=null) {
        if (!$user)
            $user   = &\JFactory::getUser();
        
        $groups=Array();
        switch (S_Factory::getPVersion()) {
            case S_Factory::ver_latest:
                 $groups = $user->get('groups');
            break;
            case S_Factory::ver_1_5:
                 $groups[]=$user->get('gid');
            break;
        }
        
        return $groups;
    }
    
    public static function loadGroupPath($gid) {
        $db = \Hwj\JFactory::getDbo();
        $db->setDebug(3);
        switch (S_Factory::getPVersion()) {
            case S_Factory::ver_latest:
            $db->setQuery("
                SELECT parent.id
                FROM #__usergroups AS node,
                        #__usergroups AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = ".$gid."
                ORDER BY parent.lft DESC;
                ");
            break;
            case S_Factory::ver_1_5:
            $db->setQuery("
                SELECT parent.id
                FROM #__core_acl_aro_groups AS node,
                        #__core_acl_aro_groups AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = ".$gid."
                ORDER BY parent.lft DESC;
                ");
            break;
        }
        
        return $db->loadColumn();
    }
    
    public static function loadHw2UserFields($userid) {
        $db = \Hwj\JFactory::getDbo();
        $db->setQuery(
                        'SELECT *' .
                        ' FROM hw2_userfields' .
                        ' WHERE userid = "'.$userid .'"'
        );
        $res = $db->loadObject();
        
        return $res;
    }
}

?>
