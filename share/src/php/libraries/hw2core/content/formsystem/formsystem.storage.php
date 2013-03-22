<?php namespace Hw2;
S_Core::checkAccess();

class S_FS_Storage extends S_CT_Storer {
        
    private function _prepareFormFields() {        
        // check the tables  
        $fields=$this->content->getItemsNode();
        /* @var $node S_Node */
        foreach ($fields as $key => $node) {
            $item=&$node->getValue();
            $val=$item->getRequestData();
            $item->setVal($val);
            $this->prepareData($node);
        }
    }
    
    public function store(S_FS_Actions $action,Array $fields) {
        switch ($action->getValue()) {
            case S_FS_Actions::delete()->getValue():
                $this->_deleteContentItems();
            break;
            case S_FS_Actions::save()->getValue():
                $this->_prepareFormFields();
                $this->storeContentItems();
            break;
        }
    }
}

?>

