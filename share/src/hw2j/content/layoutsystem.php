<?php namespace Hw2;
S_Core::checkAccess(true);

/**
 *  Layout system
 */
class S_LtSystem extends S_Object {

    
    private static $cat_lt;    
    private static $art_lt;
    
    public static function init() {
        self::addCatLt(S_LtCat::listlt, S_LtType::stat());
        self::addCatLt(S_LtCat::bloglt, S_LtType::stat());
        self::addCatLt(S_LtCat::menult, S_LtType::dynamic());
        
        self::addArtLt(S_LtArt::def, S_LtType::stat(),Array(S_LtCat::bloglt,S_LtCat::listlt));
        self::addArtLt(S_LtArt::menuitem, S_LtType::dynamic(),Array(S_LtCat::menult));
        self::addArtLt(S_LtArt::menuitem1, S_LtType::dynamic(),Array(S_LtCat::menult));
    }
    
    /**
     * 
     * @param string $name
     * @param int $type
     * @param Array $assoc
     */
    public static function addCatLt($key,S_LtType $type,Array $assoc=Array()) {
        self::$cat_lt[$key] = Array("type" => $type, "assoc" => $assoc);
    }
    
    /**
     * 
     * @param string $name
     * @param int $type
     */
    public static function addArtLt($key,S_LtType $type, Array $assoc=Array()) {
        self::$art_lt[$key] = Array("type" => $type);
        if (!empty($assoc))
            foreach ($assoc as $lt)
                if (!in_array($key, self::$cat_lt[$lt]["assoc"]))
                    self::$cat_lt[$lt]["assoc"][] = $key;
    }
    
    
    public static function checkItemLt($artLt,$catlt,$default=null,$removePrefix=false) {
        $artLt=self::removePrefix($artLt);
        if (in_array($artLt,self::$cat_lt[$catlt]["assoc"]))
                $layout=$artLt;
        else
                $layout=self::$cat_lt[$catlt]["assoc"][0];
        
        if (empty($layout)) {
            if(empty($default)) {
                $params=S_jTools::getExtensionParams("com_content");
                $default=$params->get("article_layout");
            }
            
            $layout=$default;
        }
            
        
        if ($removePrefix)
            $layout=self::removePrefix($layout);
        return $layout;
    }
    
    /**
     * 
     * @param $category you can use both JCategoryNode or category id
     * @param string $layout , default layout in case not found
     * @return string
     */
    public static function getCategoryLt($cat,$default=null,$removePrefix=false) {
        if (!empty($cat)) {
            if ($cat instanceof \JCategoryNode) {
                $parent=$cat->getParent();
                while ($parent && empty($layout)) {
                    $params=new \Hwj\JRegistry($parent->params);
                    $layout=$params->get('category_layout');
                    $parent=$parent->getParent();
                }
            } else {

                $db = \Hwj\JFactory::getDbo();
                $query="SELECT parent.id,parent.params"
                        ." FROM #__categories AS node,"
                        ." #__categories AS parent"
                        ." WHERE node.lft BETWEEN parent.lft AND parent.rgt"
                        ." AND node.id=".$cat
                        ." ORDER BY parent.rgt";
                $db->setQuery($query);
                $res=$db->loadObjectList("id");
                foreach ($res as $key => $params) {
                    $par=new \Hwj\JRegistry($params->params);
                    $layout=$par->get('category_layout');
                    if (!empty($layout))
                        break;
                }
            }
        }
        

        
        if (empty($layout)) {
            if(empty($default)) {
                $params=S_jTools::getExtensionParams("com_content");
                $default=$params->get("category_layout");
            }
            
            $layout=$default;
        }
            
        
        if ($removePrefix)
            $layout=self::removePrefix($layout);
        return $layout;
    }
    
    public function removePrefix($layout) {
        return str_replace("_:", "", $layout);
    }
    
    /**
     * used in category -> view.html.php
     * @param ContentViewCategory $view
     * @param Hw2jJRegistry() $cparams original parameters, not merged
     */
    public static function setCategoryLt(\ContentViewCategory $view,$cparams) {
        $layout=$cparams->get('category_layout');
        $lt=self::getCategoryLt($view->get('Category'), $layout);
        if (!empty($lt))
            $view->setLayout($lt);
    }
    
    public static function setItemLt(\ContentViewArticle $view,$catid) {
        $cat=\JCategories::getInstance('Content')->get($catid);
        $lt=self::checkItemLt($view->getLayout(), self::getCategoryLt($cat,"",true));
        if (!empty($lt))
            $view->setLayout($lt);
    }
    
    public static function filterComLt($id,\JForm $form,&$groups) {
        if ($id=="jform_attribs_article_layout")
            foreach ($groups as $key => $grp) 
                S_LtSystem::filterArtLt($groups[$key]["items"], S_jForm::getCategoryState($form->getValue("catid")));
    }
    
    public static function filterCatByLt(&$catList,$layout) { 
        if (!$layout)
            return;
        
        foreach ($catList as $key => $cat) {
            $id=$cat->value;
            if (self::getCategoryLt($id,null,true)!=$layout)
                unset($catList[$key]);
        }
    }
    
    /**
     * 
     * @param type $ArtLtList the list of layout select box
     * @param type $catLt current category layout
     */
    public static function filterArtLt(&$ArtLtList,$catLt) {
        $itemLt=self::$cat_lt[$catLt]["assoc"];
        foreach ($ArtLtList as $key => $lt ) {
            if (!in_array(self::removePrefix($lt->value), $itemLt))
                unset ($ArtLtList[$key]);
        }    
    }
    
    
    /**
     * find category layouts linked to given article layout
     * @param string $key
     * @return Array
     */
    public static function findArtLtAssoc($artkey) {
        $catLt=Array();
        $artkey=  substr($artkey, 2);
        foreach ( self::$cat_lt as $catkey => $value) {
            $i=0;
            $limit=  count($value["assoc"]);
            do {
                if ($value["assoc"][$i]==$artkey) {
                    $catLt[]=$catkey;
                    break;
                }
                $i++;
            } while ($i < $limit );
        }
        
        return $catLt;
    }
    
    public static function editorSwitch(\JView $view) {
        if (\JRequest::getCmd("showdefault"))  {
            $view->setLayout("edit"); // [hw2] switch on hw2edit, use "default" to see original 
        } else {
            $view->setLayout("hw2edit"); 
        }
            
    }
    
}

S_LtSystem::init();

?>
