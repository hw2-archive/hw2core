<?php namespace Hw2;
S_Core::checkAccess(true);

class S_jForm {    
    public static function eventStore(S_FS_Actions $action,$args) {
        switch ($args[0]) {
            case "com_categories.category":
                $state=self::getCategoryState($args[1]->id);
                $section= S_CT_Sec::jcategory();
            break;
            case "com_content.article": case "com_content.form":
                $reg=new \Hwj\JRegistry();
                $reg->loadString($args[1]->attribs);
                $state=self::getArticleState($reg->get("article_layout"),$args[1]->catid);
                $section= S_CT_Sec::jarticle();
            break;
            default:
                return;
        }
        
        S_Form::storeFormFields($action,$state,$section,$args[1]->id,$args[1]->language);
    }
    
    public static function getCategoryState($catid) {
        //$cat=JCategories::getInstance("Content")->get($catid);
        //$params=new \Hwj\JRegistry($cat->params);
        $input=S_Uri::getInput();
        $id=$input->get("catval"); //check state from parent id
        if (!$id)
            $id=$catid; // if not catval, then get original parent
        
        $catLtName=$input->get("catltName");
        return $catLtName ? $catLtName : S_LtSystem::getCategoryLt($id, $catLtName, true);
    }
    
    public static function getArticleState($artLt,$catid) {
        return S_LtSystem::checkItemLt($argLt,self::getCategoryState($catid),S_Uri::getInput()->get("artltName"),true);
    }
}

?>
