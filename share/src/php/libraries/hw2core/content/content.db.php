<?php namespace Hw2;
S_Core::checkAccess();


class S_CT_Loader extends S_CT_Worker {
    /**
     *  get single item from db
     */
    function loadItem(S_CT_Item $item,$lang="*") {
        return self::getFromDb($this->content->id, $this->content->getSection(), $item->table, $lang, $item->getId());
    }
    
    function loadItemByAlias($alias,$lang="*") {
        $item=$this->content->getItem($alias,$lang);
        return $this->loadItem($item, $lang);
    }
    
    function loadItems($filter,$table,$lang="*") {
        return self::getFromDb($this->content->id, $this->content->getSection(), $table, $lang, $filter);
    }

    /**
     * 
     * @param type $item
     * @param type $data
     * @return Hw2FormData Description
     */
    private function getItemData(S_Node $node, S_CT_Item $item, $content) {
        // get content from db field
        $itemContent=$item->table==S_CT_Tables::content ? 
                $content[$item->getId()][$item->tfield] : $content[$item->tfield];
        
        // nested case
        if ($this->content->isNested($node)) {
            // JRegistry is not nested, all json items are 
            // differentiated by names such as in html items
            $data=new \Hwj\JRegistry($itemContent);
            return $data->get($item->getId()); // recover data from registry
        } else {
            // direct case
            return $itemContent;
        }
    }
    
    
    public function getValuesFromDb() {
        if (!$this->content->getId()) // if no refid , then render without values ( ex: new items )
            return;

        $content = Array();
        // check the tables  
        $items=$this->content->getItemsNode();
        $this->content->buildNesting();
        /* @var $node S_Node */
        foreach ($items as $key => $node) {
            /* @var $item S_Item */
            $item=&$node->getValue();
            
            if (!$item->table)
                continue;

            //get contents from the table only when not already loaded for same table
            if (!array_key_exists($item->table,$content))
                $content[$item->table]=$this->getFromDb($this->content->getId(), $this->content->getSection(), $item->table,  S_CT_DBQuery::ALL_LANG);

            $val=$this->getItemData($node, $item, $content[$item->table]);
            $item->setVal($val);
        }
    }
    
    /**
     * 
     * @param type $refid
     * @param S_CT_Sec $section
     * @param type $table
     * @param type $lang
     * @param (array or string) $filter  in hw2_content table it specify value of alias column, else it specify column for selection
     * @return type
     */
    public static function getFromDb($refid, S_CT_Sec $section, $table, $lang = "*",$filter=null) {
        return S_CT_DBQuery::C()->selectItems($refid, $section, $table, $lang, $filter);
    }
}


class S_CT_Storer extends S_CT_Worker {
    private $data=Array();
    
    /**
     * 
     * @param \Hw2\S_FormField $field
     * @param type $val value to store
     * @param type $id override id specified in $field
     * @param type $table override table specified in $field
     * @param type $tField override tfield specified in $field
     */
    protected function addData(S_CT_Item $field, $firstParent, $id=null, $table=null, $tField=null) {
        $val=$field->getVal();
        if ($val===null)
            return;
        
        $table  =empty($table)  ? $field->table   : $table; 
        $tField =empty($tField) ? $field->tfield  : $tField; 
        $id     =empty($id)     ? $field->getId() : $id; 
        
        if (!$table || !$tField) // this field can't/shouldn't be saved to db
            return;

        $this->data[$firstParent][$table][$tField][$id]=$field;
    }
    
    public function prepareData(S_Node $node) {
         $this->addData($node->getValue(), $this->content->getParentAfterHead($node)->getUid());
    }
    
    protected function _deleteContentItems(Array &$alreadyDel=Array(),\Hwj\JDatabaseDriver $db=null) {
        $items=$this->content->getItemsNode();
        $section=$this->content->getSection()->getValue();
        $refid=$this->content->getId();
        foreach ($items as $key => $node) {
            /* @var $item S_Item */
            $item=$node->getValue();
            
            if (!$item->table)
                continue;
            
            //$item->delete();
            $tname=$item->getTable();
            if (!$alreadyDel[$tname]) {
                if (!S_CT_DBQuery::I()->deleteItem($tname, $section, $refid,$db))
                        return false;
                $alreadyDel[$tname]=true;
            }
        }
        
        return true;
    }
    
    public function storeContentItems() {
            $alreadyDel=Array();
            $db = \Hwj\JFactory::getDbo();
            $db->setDebug(3);
            $db->transactionStart();
            $section=$this->content->getSection()->getValue();
            foreach ($this->data as $firstParent => $tables) {
                foreach ($tables as $tname => $fields) {
                    // delete first all old values
                    if (!$this->_deleteContentItems($alreadyDel,$db)) {
                        $db->transactionRollback ();
                        return false;
                    }
                    
                    $columns=Array("section","ref_id","lang");
                    // we use form lang also for multilang fields 
                    // normally multilang should be used with "all-lang" contents (*)
                    // using it with stricted lang content multilang is usless so we
                    // don't care what will be stored in DB
                    $values=$section.",".$this->content->getId().",".$db->quote($this->content->getLang());
                    
                    // special case
                    if ($tname == S_CT_Tables::content) {
                        $columns[]="alias";
                        $values.=",".$db->quote($firstParent);
                    }
                    $data=Array();
                    foreach ($fields as $fname => $content) {
                        /* @var $content S_FormField */
                        if (count($content)>1) {
                            foreach ($content as $id => $item) {
                                $val=$item->getVal();
                                if ($val===null)
                                    continue;
                                
                                if ( $data[$fname] instanceof \Hwj\JRegistry ) {
                                    /* @var $reg \Hwj\JRegistry */
                                    $data[$fname]->set($id, $db->escape($val));
                                } else {
                                    $data[$fname]=new \Hwj\JRegistry(Array($id => $db->escape($val)));
                                }
                                $item->store($this->content, $this->data);
                            }
                        } else {
                            $item=reset($content); // get first value
                            $item->store($this->content, $this->data);
                            $val=$item->getVal();
                            if ($val===null)
                                continue;
                            
                            $data[$fname]=$db->escape($val);
                        }
                    }
                    
                    $columns=array_merge($columns,array_keys($data));
                    $values.=",'".implode("','", array_values($data))."'";
                    if (!S_CT_DBQuery::I()->insertItem($tname, $columns, $values, $db)) {
                        $db->transactionRollback ();
                        return false;
                    }
                }
            }
            $db->transactionCommit();
            return true;
    }
}

class S_CT_DBQuery extends S_Object {
    const ALL_LANG="ALL";
    
    /**
     *
     * @var \Hwj\JDatabaseDriver 
     */
    private $DBO;
    
    public function __construct() {
        $this->DBO = \Hwj\JFactory::getDbo();
        $this->DBO->setDebug(3);
    }
    
    public function deleteItem($table,$section,$refid,\Hwj\JDatabaseDriver $db=null) {
        !is_null($db) or $db=$this->DBO;
        $query=$db->getQuery(true);
        $query->delete($table);
        $query->where("section=" . $section);
        $query->where("ref_id=" . $refid);
        $db->setQuery($query);
        return $db->execute();
    }
    
    public function insertItem($table,$columns,$values,\Hwj\JDatabaseDriver $db=null) {
        !is_null($db) or $db=$this->DBO;
        $query=$db->getQuery(true);
        $query->insert($table);
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);
        return $db->execute();
    }
    
    /**
     * 
     * @param type $refid
     * @param S_CT_Sec $section
     * @param type $table
     * @param type $lang
     * @param (array or string) $filter  in hw2_content table it specify value of alias column, else it specify column for selection
     * @return type
     */
    public function selectItems($refid, S_CT_Sec $section, $table, $lang = "*",$filter=null,\Hwj\JDatabaseDriver $db=null) {
        !is_null($db) or $db=$this->DBO;
        $query = $db->getQuery(true);

        $query->from($table);
        $query->where("ref_id=" . $refid);
        if ($section)
            $query->where("section=" . $section->getValue());
        if ($lang != self::ALL_LANG)
            $query->where("lang=" . $db->quote($lang));
        // if not an array but just an alias string, create a singleton array
        if (!empty($filter) && !is_array($filter)) 
            $filter=Array($filter);
        
        switch ($table) {
            case S_CT_Tables::content:
                $query->select("*");

                if (!empty($filter)) {
                    $query->where("alias IN (".$db->quote(implode('","', $filter)).")");
                }

                $db->setQuery($query);
                $res = $db->loadAssocList("alias");
            break;
            default:
                $filter=!empty($filter) ? implode(',', $filter) : "*";
                $query->select($filter);

                $db->setQuery($query);
                $res = $db->loadRowList();
            break;
        }
   
        return $res;
    }
    
}

?>
