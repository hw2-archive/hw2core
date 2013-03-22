<?php namespace Hwj;

function my_is_callable_d($name, $syntax_only = false, &$callable_name = null) {
    return my_is_callable($name, $syntax_only, $callable_name);
}

function my_is_callable(&$name, $syntax_only = false, &$callable_name = null) {
    if (is_array($name))
        $class_name=&$name[0];
    else
        $class_name=&$name; // in this case should be the method name
    
    $hw2_class=strpos(strtolower($class_name), "hwj\\") !== 0 && strpos(strtolower($class_name), "hw2\\") !== 0 ?
        'Hwj\\'.$class_name : $class_name;
    
    $my_call=  is_array($name) ? Array($hw2_class,$name[1]) : $hw2_class;
    $call= is_array($name) ? Array($class_name,$name[1]) : $class_name;
    
    $result=  is_callable($my_call, $syntax_only,$callable_name);
    if (!$result)
        return is_callable($call, $syntax_only,$callable_name);
    
    $class_name=$hw2_class;
    return $result;
    
}

function my_define_d($name, $value, $case_insensitive = false) {
    my_define($name, $value, $case_insensitive);
}

function my_define(&$name, $value, $case_insensitive = false) {
    $hw2_name=strpos(strtolower($class_name), "hwj\\") !== 0 && strpos(strtolower($class_name), "hw2\\") !== 0 ?
        'Hwj\\'.$name : $name;
    
    defined($hw2_name) or define($hw2_name, $value, $case_insensitive = false) or define($name);
    
    $name=$hw2_name;
}

function my_is_subclass_of_d($object, $class_name) {
    return my_is_subclass_of($object, $class_name);
}

function my_is_subclass_of($object, $class_name) {
    $hw2_class=strpos(strtolower($class_name), "hwj\\") !== 0 && strpos(strtolower($class_name), "hw2\\") !== 0 ?
        'Hwj\\'.$class_name : $class_name;
    
    $result=is_subclass_of($object, $hw2_class);
    if (!$result)
        return is_subclass_of($object, $class_name);
    
    $class_name=$hw2_class;
    return $result;
}

function my_class_exists_d($class_name, $autoload = true) {
    return my_class_exists($class_name,$autoload);
}

function my_class_exists(&$class_name, $autoload = true) {
    $hw2_class=strpos(strtolower($class_name), "hwj\\") !== 0 && strpos(strtolower($class_name), "hw2\\") !== 0 ?
            'Hwj\\'.$class_name : $class_name;
    
    //[workaround] 1) first check namespaced case
    // 2) then, after autoload, recheck without autoload to see if it has been loaded
    $exist=class_exists($hw2_class,$autoload) || class_exists($hw2_class,false);
    if (!$exist) {
        // 3) finally check for un-namespaced case
        if (class_exists($class_name, false)) {
            $reflector = new \ReflectionClass("\Iterator");
            // this condition will work since jplatform will use only
            // classes from "internal" files or from php core/extensions
            // no external files will be present
            if ($reflector->getFileName()!==false)
                return true;
        }
                
    }
         
    // if it's an HW2CORE class, then not change name
    if (!\Hw2\S_CIndex::getPath($class_name))
        $class_name=$hw2_class; // replace class name for pointer
    return $exist;
}

function my_get_class_methods_d($class) {
    return my_get_class_methods($class);
}

function my_get_class_methods(&$class) {
    if (is_string($class)) {
        $hw2_class=strpos(strtolower($class), "hwj\\") !== 0 && strpos(strtolower($class), "hw2\\") !== 0 ?
                'Hwj\\'.$class : $class;

        $methods=get_class_methods($hw2_class);
        if(!$methods) //[workaround] double check, but works 
            return get_class_methods ($class);
        
        $class=$hw2_class; // replace class name for pointer
    } else 
       $methods=get_class_methods($class); 
            
    return $methods;
}

function my_defined($name) {
    return defined("Hwj\\".$name) or defined($name);
}

?>
