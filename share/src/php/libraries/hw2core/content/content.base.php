<?php namespace Hw2;
S_Core::checkAccess();

class S_CT_NodeMgr extends S_CT_Worker {
    /**
     * 
     * @param type $itemClass
     * @param type $id
     * @param type $title
     * @param type $multilang
     * @param type $parent the id(alias) of parent item
     * info:
     * - if parent is stored in "params" item , then create a nested Hw2jJRegistry() in parent Hw2jJRegistry() value: {"parent":"{child:""}"}
     * - if parent is stored in content table, then use the same row to store child item instead create new one 
     * - for custom table , rules can be defined specifically or just use the standard
     * @param type $options
     * @param type $table
     * @param type $tfield
     * @return \Hw2\S_Node
     */
    public function _($itemClass,$id, $title, $multilang=false, $options = null, $table = HW2FS_TABLE_CONTENT, $tfield = HW2FS_FIELD_CONTENT) {
        $id=  S_CT_Item::normalizeId($id);
        if ($multilang) {
            $jlang = \JFactory::getLanguage();
            $langs = $jlang->getKnownLanguages();
            $node=new S_Node(new S_CT_MultiLangItem($id, $title, $table, $tfield),$id);
            foreach ($langs as $key => $info) {
                $langid=  S_CT_Item::normalizeId($id,$key);
                $node->setChild(new S_Node(new $itemClass($langid, $title, $table, $tfield, $options, $key),$langid));
            }
        } else {
            $node=new S_Node(new $itemClass($id, $title, $table, $tfield, $options, $this->content->getLang()), $id);
        }
        
        return $node;
    }
}

/**
 * this is more a structure than a class
 * can be used to pass content info to other class/methods
 */
class S_Content_Info extends S_Object {

    protected $id; // refid
    protected $lang="*";
    /** an array with arbitrary values **/
    protected $options=Array(); 
    /**  @var S_Tree */
    protected $ItemsTree;
    protected $tItemElemCnt = Array();
    public static function getSection() { return ; }

    function __construct($id, $lang = "*",Array $options=null) {
        parent::__construct(true);  // enable strict mode to avoid undefined var declaration
        $this->ItemsTree=new S_Tree();
        $this->_setObjVars($params = Array(&$id, &$lang), get_defined_vars());
        $this->_setVal_R($options, get_defined_vars());
    }
    /**
     * 
     * @return S_Tree
     */
    public function getItemsTree() {
        return $this->ItemsTree;
    }
    
    public function getItemsNode() {
        $tree=$this->ItemsTree->getNodeList();
        foreach ($tree as $key => $node) {
            /* @var $node S_Node */
            if ($node->getValue()===S_Tree::head) //exclude HEAD node
                continue;
            $values[]=$node;
        }
        return $values;
    }
    
    public function getItems() {
        $tree=$this->ItemsTree->getNodeList();
        $items=Array();
        foreach ($tree as $key => $node) {
            /* @var $node S_Node */
            $items[]=$node->getValue();
        }
        return $items;
    }
    
    /**
     * 
     * @param type $id
     * @return S_CT_Item
     */
    public function getItem($id,$lang="*") {
        if ($lang=="*")
            $lang=$this->lang;
        return $this->ItemsTree->getValue(S_CT_Item::normalizeId($id,$lang));
    }
    
    public function getNode($id) {
        return $this->ItemsTree->getNode(S_CT_Item::normalizeId($id,$lang));
    }
    
    public function setItemVal($id,$val,$lang="*") {
        $this->ItemsTree->getValue(S_CT_Item::normalizeId($id,$lang))->setVal($val);
    }
    
    public function getItemVal($id,$lang="*",$default=null) {
        $item=$this->getItem($id,$lang);
        if (!empty($item)) {
           // if it's a multilang $item, we've to return value of $item with same language of client
           if ($item instanceof S_CT_MultiLangItem ) {
               $item=$this->getItem($id,  S_PApi::getLangTag());
               $result= !empty($item) ? $item->getVal() : null;
           } else
               $result= $item->getVal();
        }
        
        return empty($result) ? $default : $result;
    }
  

    public function isNested(S_Node $node) {
        $item=$node->getValue();
        $firstParent=$this->getParentAfterHead($node);
        return $this->tItemElemCnt[$firstParent->getUid()][$item->table][$item->tfield] > 1;
    }
    
    /**
     * get head node
     * @return S_Node
     */
    public function getParentAfterHead(S_Node $node) {
        $parent=$node;
        while ($parent && $parent->getValue()!=S_Tree::head && !$parent->getValue() instanceof S_CT_MultiLangItem) {
            $node=$parent;
            $parent=$parent->getParent();
        }
        return $node;
    }
    
    public function buildNesting() {
        $tree=$this->ItemsTree->getNodeList();
        $this->tItemElemCnt=0;
        foreach ($tree as $key => $node) {
            /* @var $node S_Node */
            /* @var $item S_CT_Item */
            $item=$node->getValue();
            if ($item instanceof S_CT_Item ) {
                $firstParent=$this->getParentAfterHead($node); // it is the row where is stored
                $this->tItemElemCnt[$firstParent->getUid()][$item->table][$item->tfield]++;
            }
        }
    }    
}

/**
 * it represents the hw2 content of a single ARTICLE/CATEGORY or any referenced OBJECT
 */
class S_CT_Container extends S_Content_Info {  
    /** @var S_CT_Loader */
    protected $loader;
    /** @var S_CT_Storer */
    protected $storer;
    
    public static function init() { 
        $class = get_called_class();
        S_Form::addCallBack($class::getSection(), $class);
    }
    
    public function __construct($id, $lang = "*",Array $options=null) {
        parent::__construct($id, $lang,$options);
        $this->loader=new S_CT_Loader($this);
        $this->storer=new S_CT_Storer($this);
    }
    
    protected function addNode(S_Node $node,$parentUid=null) {
            if ($parentUid)
                $parentUid = S_CT_Item::normalizeId($parentUid);
            $this->ItemsTree->addNode($node, $parentUid );
    }
    
    public function getLoader() {
        return $this->loader;
    }
    
    public function getStorer() {
        return $this->storer;
    }
    
}

class S_CT_Worker {
    /** @var S_CT_Container */
    protected $content; // content class with items etc

    public function __construct(S_CT_Container $content) {
        $this->content = $content;
    }
}

?>
