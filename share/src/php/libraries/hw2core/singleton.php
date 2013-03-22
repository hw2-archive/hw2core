<?php namespace Hw2;
S_Core::checkAccess();

/**
 *  if possible, use or extends Hw2Object instead
 */
final class S_St extends S_Object
{
    public static function get($name, Array $args=Array()) {
        return parent::createInstance($name, $args);
    }
}
?>
