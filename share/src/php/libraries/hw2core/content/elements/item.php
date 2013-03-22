<?php namespace Hw2;

class S_CT_Item extends S_Object {
    const prefix="hw2form_";
    
    protected $id; // alias used as base for tag id in forms and db fields
    protected $val; 
    protected $title;
    public $lang="*";
    //db vars
    // WARNING: don't change values before after defined to keep data integrity
    public $table; // table where store 
    // field of the table where store the value, if multiple values are defined in same field
    // then it uses Hw2jJRegistry() to store
    public $tfield; 
    
    function __construct($id,$title,$table,$tfield,Array $options=null,$lang="*") {
        parent::__construct(true);
        $params=Array(&$id,&$title,&$table,&$lang,&$tfield);
                $this->_setObjVars($params,get_defined_vars())
                        ->_setObjVars($options);
    }
    
    public static function normalizeId($id,$lang="*") {
        $id=strpos($id, self::prefix) === false ? self::prefix.$id : $id; //prefix
        if ($lang && $lang!="*")
            $id=strpos($id, $lang, count($id)-count($lang)) === false ? $id."_".$lang : $id; //lang suffix
        return $id;
    }
    
    public function setId($val) {
        $this->_set("id",self::normalizeId($val));
        return $this;
    }
    
    public function store(S_CT_Container $form,Array $preparedData) {
        ;
    }
}

?>
