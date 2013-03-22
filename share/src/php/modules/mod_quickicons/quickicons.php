<?php namespace Hw2;
S_Core::checkAccess();

class S_QuickIcons {
    private static $customIcon=Array();
    
    /* deprecated function
    static public function getCustomQuickIconList()
    {
        $array=Array();
        foreach (self::$customIcon as $icon)
            if (Hw2Factory::getPVersion(Hw2Factory::ver_latest))
                $array[]=Array("link"=>$icon["link"],"image"=>'header/'.$icon["img"],"text"=>$icon["text"],"access"=>$icon["access"]);
            else
                $array[]=Array($icon["com"],$icon["link"],$icon["img"],$icon["text"]);
        
        return $array;
    }*/
    
    
    static public function render() {
?>
    <div class="cpanel">
<?php
        S_Acl::cleanList(self::$customIcon,'link');

        foreach (self::$customIcon as $icon):
            if ( !S_Acl::checkCustomAcl($icon["com"]) )
                    continue;
                    
?>
            <div class="icon-wrapper">
                            <div class="icon">
                                    <a href="<?php echo $icon["link"]; ?>">
                                        <img src="<?php echo S_Uri::getUri()->root(true).DS."images".DS."shared_images".DS.$icon["img"] ?>"></img>
                                            <span><?php echo $icon["text"]; ?></span></a>
                            </div>
            </div>
<?php
       endforeach;
?>
    </div>
<?php
    }
    
    /**
     * 
     * @param type $component Name of the component ( deprecated in latest builds )
     * @param type $link      link of the component
     * @param type $icon      icon image path
     * @param type $text      text to show for the icon
     * @param type $access    access level
     */
    static public function addCustomQuickIcon($component,$link,$icon,$text,$access=true) {
         self::$customIcon[] = array("com"=>$component,"link"=>$link,"img"=>$icon,"text"=>$text,"access"=>$access);
    }
}
?>
