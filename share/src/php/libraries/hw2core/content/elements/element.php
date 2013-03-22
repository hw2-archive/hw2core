<?php namespace Hw2;
S_Core::checkAccess();

class S_CT_Element extends S_CT_Container {
    const ITEM="element";
    
    private $alias;
    
    function __construct($id,$lang="*",Array $options=null) {
        parent::__construct($id, $lang,$options);
        
        $this->alias=!empty($options["alias"] )? $options["alias"] : self::ITEM;
        //create fields
        $nc=new S_CT_NodeMgr($this); //node creator
        $this->addNode($nc->_(S_CT_Item::cname(),$this->alias, "Core Element"));
    }
    
    public function getElemVal($default=null) {
        return $this->getItemVal($this->alias,"*",$default);
    }
    
    public function setElemVal($val) {
        $this->setItemVal($this->alias, $val);
    }
    
    public static function getSection() { return S_CT_Sec::coreElement(); }
}
?>
