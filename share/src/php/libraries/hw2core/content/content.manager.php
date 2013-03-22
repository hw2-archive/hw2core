<?php namespace Hw2;
S_Core::checkAccess();

class S_CT_Manager extends S_Object {    
    private $contentList;
    
    /**
     * 
     * @param integer $id normally it's the database id
     * @param type $elementName Class name of element ( you can use cname method of elem. class to retrieve this value )
     * @param type $lang
     * @return type
     */
    protected function addCT($id, $elementCName, $lang="*",Array $options=null) {
        /* @var $content S_CT_Container */
        $content=new $elementCName($id,$lang,$options);
        $sec=$content->getSection()->getValue();
        $this->contentList[$sec][$id]=$content;
        return $content;
    }
    
    function __construct() {
        parent::__construct(true);
    }
    
    /**
     * 
     * @param type $id
     * @param type $sec
     * @return S_CT_Container
     */
    public function getContent($id, S_CT_Sec $sec,$load=false) {
        if ($load)
            $this->load ($id, $sec);
        return $this->contentList[$sec->getValue()][$id];
    }
    
    public function load($id,  S_CT_Sec $sec) {
        $this->contentList[$sec->getValue()][$id]->getLoader()->getValuesFromDb();
    }
    
    public function save($id, S_CT_Sec $sec) {
        $content=$this->contentList[$sec->getValue()][$id];
        $items=$content->getItemsNode();
        /* @var $node S_Node */
        foreach ($items as $key => $node) {
             $content->getStorer()->prepareData($node);
        }
        $content->getStorer()->storeContentItems();
    }
    
    /**
     * [TODO] implement
     * @param type $id
     * @param type $sec
     */
    public function delete($id, S_CT_Sec $sec) {
        
    }
}
?>
