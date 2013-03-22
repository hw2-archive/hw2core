<?php namespace Hw2;
S_Core::checkAccess(true);

class S_jRouter extends S_Object {
    public function getPage($id,$type) {
        $resid=$this->findRoutingMenu($id,$type);
        $result=null;
        if (!$resid) {
            $resid=$this->findRoutingCategory($id,$type);
            if ($resid){
                // create the page here
                // with page, findroutingcategory is not required
                // but needs just to add a menu item
                $result="index.php?option=com_content&view=category&layout=blog&id=".$resid."&rid=".$id."&rtype=".$type;
            }
        } else
            $result="index.php?Itemid=".$resid;

        return $result;
    }
    
    public function findRoutingMenu($id,$type) {
        if (empty($id) && empty($type))
            return 0;
        
        $query="SELECT id FROM #__menu"
        ." WHERE menutype=\"".$id."-".$type."\""
        ." AND alias=\"home-".$id."-".$type."\"";
        
        $db = \Hwj\JFactory::getDbo();
        $db->setQuery($query);
        
        $res = $db->loadResult();
        if (!$res)
            return 0;
        
        return $res;
    }
    
    /**
     *
     * @param type $id
     * @param type $type
     * @return int id of category
     */
    public function findRoutingCategory($id,$type) {
        if (empty($id))
            return 0;
        
        $query="SELECT id FROM #__categories"
        ." WHERE alias=\"".$id."-".$type."\"";
        
        $db = \Hwj\JFactory::getDbo();
        $db->setQuery($query);
        
        $res = $db->loadResult();
        if (!$res)
            return 0;
        
        return $res;
    }
    
    public function getPageMetaInfo($document) {
        $route=Array();
        $param=S_jTools::getMetaKeyParameters($document->getMetaData('keywords'));
        if (!empty($param) && $param->getValue("id") && $param->getValue("type"))
            $route=Array($param->getValue("id", 0),$param->getValue("type",1));
        return $route;
    }
    
    public function getMenuMetaInfo($menuitemid) {
        $menu = & JSite::getMenu();
        $route=Array();
        if ($menuitemid)
        {
            $menuparams = & $menu->getParams($menuitemid);
            $param=S_jTools::getMetaKeyParameters($menuparams->getValue("menu-meta_keywords", ""));  
            if (!empty($param) && $param->getValue("id") && $param->getValue("type"))
                $route=Array($param->getValue("id", 0),$param->getValue("type",1));
        }
        return $route;
    }
    
    public function getMenuRouteInfo($menuitemid) {
        $route=Array();
        if ($menuitemid)
        {
            $menu = & JSite::getMenu();
            $item = $menu->getItem($menuitemid);
            // then get from root menutye
            $temp=explode("-", $item->menutype);
            if (is_numeric($temp[0]) && is_numeric($temp[1]))
                    $route=$temp;
        }
        return $route;
    }
    
    public function getQueryRouteInfo() {
        $route=Array();
        $temp[0]=JRequest::getVar('rid','');
        $temp[1]=JRequest::getVar('rtype','');
        if (is_numeric($temp[0]) && is_numeric($temp[1]))
            $route=$temp;
        
        return $route;
    }
    
    /**
     * Combine getters for init event, the priority of result is given to
     * metakey parameters
     * 
     * @param type $menuitemid
     * @return array
     */
    public function getAllInitRInfo($menuitemid) {
        // first form meta
        $route=Array();
        $route=$this->getMenuMetaInfo($menuitemid);
        if(empty($route)) {
            // then get from root menutye
            $route=$this->getMenuRouteInfo($menuitemid);
            if (empty($route))
                $route=$this->getQueryRouteInfo();
        }
        
        return $route;
    }
    
    /**
     * Combine all route information getters, the priority of result is given to 
     * metakey parameters
     * 
     * @param type $document
     * @return array 
     */
    public function getAllRouteInfo($document) {

        // first from current page
        $route=$this->getPageMetaInfo($document);
        if (empty($route)) {
            $menuitemid = JRequest::getInt( 'Itemid' );
            $route = $this->getAllInitRInfo($menuitemid);
            //[TODO] finally check from parent items 
        }
        
        return $route;
    }
}
?>
