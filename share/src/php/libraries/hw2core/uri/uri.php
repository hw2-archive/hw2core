<?php namespace Hw2;
S_Core::checkAccess();

use Hw2\S_Paths as SP;

require_once 'juri_copy.php';

class S_Uri extends S_Object {
    
    public static function init() { 
        // define url paths
        $SP=SP::I();
        // setup correct root url        
        $root= dirname(S_Core::isBackend()? str_ireplace('/administrator', '', $_SERVER["SCRIPT_NAME"]) : $_SERVER["SCRIPT_NAME"]).
                SP::I()->get(SP::HW2PATH_CORE)->getUrl() .
                SP::I()->get("s_jplatform")->getUrl() .
                "/cms";

        $base = $root . (S_Core::isBackend() ? "/administrator" : null);

        \Hwj\JURI::reset();
        \Hwj\JURI::base(false, $base);
        \Hwj\JURI::root(false, $root);
        \Hwj\JUri::getInstance()->parse($base);

        if (S_Core::isBackend()) {
            self::getUri()->root(false,str_ireplace('/administrator', '',self::getUri()->base(true)));
        }
        
        $SP->setUrl(SP::HW2PATH_PARENT_URL,self::getUri()->root(), S_PathType::dir);
        $SP->setUrl(SP::HW2PATH_CORE_URL,self::getUri()->root()."/hw2", S_PathType::dir);
    }
    
    /**
     * 
     * @return S_Uri_Ex
     */
    public static function getUri() {
        return \Hwj\JUriCopy::getInstance();
    }
    
    public static function getUriPrefix() {
        return self::getUri()->toString(array('scheme', 'host', 'port'));
    }
    
    public static function normalizeUri($uri) {
        return str_replace(array('/','//', '\\','\\\\'), "//", $uri);
    }
    
    public static function hw2_curPageURL($sef=false) {
            if ($sef)
                return self::getUri()->current();
            else {
                return self::getUri()->base()."index.php?".http_build_query($_GET);
            }

            /*$pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on") {
                    $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            return $pageURL; */
    }
        
    // deprecated
    public static function parseDomain($url,$offset,$length=null) {
        $parsedUrl = parse_url($url);
        self::parseHost($parsedUrl['host'], $offset,$length);
    }

    /**
     * 
     * @param string $url: the url to parse
     * @param int $offset: the part of domain, ex:
     *                     in www.google.com , $offset 1 is com , 2 is google etc.
     * @param int $lenght: how many parts of domain you want, ex: 
     *                     in www.google.com , offset: 1 and $lenght: 2 you will have www.google
     * @return array
     */ 
    public static function parseHost($host,$offset,$length=null) {
        $host = explode('.', $host);

        $domain = array_slice($host, $offset, $length );
        return $domain; 
    }
    
    public static function getInput() {
        return new \Hwj\JInput;
    }
    
    /** 
    * Returns the url query as associative array 
    * 
    * @param    string    url 
    * @return    array    params 
    */ 
    public static function queryToArray($url) { 
        if (!$url)
            return null;
        
        $query=parse_url(htmlspecialchars_decode($url),PHP_URL_QUERY);
        if (!$query)
            return null;
        
        $queryParts = explode('&', $query); 

        $params = array(); 
        foreach ($queryParts as $param) { 
            $item = explode('=', $param); 
            $params[$item[0]] = $item[1]; 
        } 

        return $params; 
    } 
    
    public static function replaceSubdomain($replace) {
        $host=S_Uri::getUri()->getHost();
        if ($host=="localhost") // no change
            return $host;
        
        $host_array=explode('.', $host);
        
        $ln=count($host_array);
        if ($ln<2)
            return $host;
        
        $result=$replace.".".$host_array[$ln-2].".".$host_array[$ln-1];
        S_Uri::getUri()->setHost($result);
        
        return S_Uri::getUri()->toString();
    }
    
    public static function GetClientRoot()
    {
            $config = \Hwj\JFactory::getConfig();
            $root	= S_Uri::getUri()->root();

            switch((int)$config->get('force_ssl'))
            {
                    case 0: // none
                            if(strpos(strtolower($root), 'https') === 0)
                            {
                                    $root = substr_replace($root, 'http', 0, 5);
                            }
                            break;
                    case 1: // admin only
                            $root = substr_replace(S_Uri::getUri()->root(), 'http', 0, 5);
                            break;
                    case 2: // entire site
                            break;
            }

            return $root;
    }	
    

}
?>
