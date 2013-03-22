<?php namespace Hw2;
S_Core::checkAccess();

class S_Plugin extends \Hwj\JPlugin { 
    const onBeforeExecute="onBeforeExecute";
    const onAfterExecute="onAfterExecute";
    
    public static function load(S_AppType $type,$name) {
        if (!$type->checkVal(S_AppType::base()))
            if (S_Core::isCli() && !$type->checkVal(S_AppType::cli()))
                return;
        
        $class=  get_called_class();
        $dispatcher = \Hwj\JEventDispatcher::getInstance(); /** @see https://docs.google.com/document/d/1IXepD_GS0Y9YEvtLlSCRls2A4n_WauyGnyyDyGyLzLI/pub **/
        $config=Array(
            "name"=>$name,
            "type"=>$type->getValue()
        );
        
        new $class($dispatcher,$config);
    }
}

?>
