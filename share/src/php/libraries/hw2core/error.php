<?php namespace Hw2;

final class S_Exception_type extends S_TypeDef {
    static function error() { return parent::_(\Hwj\JLog::ERROR); }
    static function warning() { return parent::_(\Hwj\JLog::WARNING); }
    static function notice() { return parent::_(\Hwj\JLog::NOTICE); }
    static function critical() { return parent::_(\Hwj\JLog::CRITICAL); }
}

class S_Exception {
    public static function raise($msg,S_Exception_type $type,$enableCallStack=true) {
        \Hwj\JLog::add($msg."\n", $type->getValue());
        
        if ($type==S_Exception_type::error())
            ob_end_clean();
        
        if ($enableCallStack) {
            trigger_error($msg);
        } else {
            echo $msg;
        }
        
        if ($type==S_Exception_type::error()) {
            exit();
        }
    }
}
?>
