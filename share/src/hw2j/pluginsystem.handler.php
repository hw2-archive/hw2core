<?php namespace Hw2;
S_Core::checkAccess(true);

class S_jPluginHandler {
    
    private static $exIdList=Array();
    private static $exClList=Array();
    private static $sqlList="0";
    
    public static function addExId($id) {
        if (!$id)
            return;
        self::$exIdList[]=$id;
        self::$sqlList= implode(",", self::$exIdList);
    }
        
    public static function addExClass($class) {
        self::$exClList[]=$class;
    }
    
    /**
     * 
     * @return Array
     */
    public static function getExIdList() {
        return self::$exIdList;
    }
    
    public static function getExIdSqlList() {
        return self::$sqlList;
    }
    
    public static function getExClassList() {
        return self::$exClList;
    }
}
?>
