<?php namespace Hw2;
S_Core::checkAccess();

class S_String extends \Hwj\JString {
    public static function startsWith($haystack, $needle)
    {
        return !self::strncmp($haystack, $needle, strlen($needle));
    }

    public static function endsWith($haystack, $needle)
    {
        return self::substr($haystack, -self::strlen($needle))===$needle;
    }  
}
?>
