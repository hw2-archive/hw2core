<?php namespace Hw2;
S_Core::checkAccess();

class S_AltRegistry {
    
    /**
     * this function create a registry() object using ' instead " as var character
     * @param type $params
     * @param type $fromCommaList
     * @return \JRegistry
     */
    static public function create($param) {
        $reg = null;
        if (!empty($param)) {
            //as soon as find "somechar}," split the string
            // change ' not preceded by \   (however ' in keys value should not be present)
            $param = preg_replace("/(?<!\\\)'/", "\"", $param);
            $param = preg_replace("/\\\,/", ",", $param);
            $reg = new \Hwj\JRegistry($param);
        }

        return $reg;
    }
}
?>
