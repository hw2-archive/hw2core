<?php namespace Hw2;
S_Core::checkAccess();

class S_Router extends S_Object {
    private $sdomain;
    private $sdomain_str;
    
    function __construct() {
        $this->sdomain=S_Uri::parseHost(S_Uri::getUri()->getHost(), 0,-2); // get all subdomains
        $this->sdomain_str=implode(".", $this->sdomain);
    }
    
    public function checkUrlInsertion() {
        if (S_Uri::getUri()->getHost()=="localhost") // no redirection when localhost
            return false;
        
        $app = \JFactory::getApplication();
        $on=false;
        //this variable must be defined in htaccess, this if exists to avoid errors
        // HW2_ROUTE_ENABLED checks when we are using direct subdomain access: sub.enmovida.com
        if (array_key_exists("HW2_ROUTE_ENABLED", $_SERVER))
            $on = $_SERVER["HW2_ROUTE_ENABLED"];

        if ($on && !empty($this->sdomain_str) && $this->sdomain_str != "www") {
            // change the page if you use a dedicated subdomain from the url bar
            $query="SELECT id,type FROM hw2_route"
            ." WHERE gsubdomain=\"".$this->sdomain_str."\""
            ." OR subdomain=\"".$this->sdomain_str."\"";

            $db = \Hwj\JFactory::getDbo();
            $db->setQuery($query);

            $res = $db->loadObject();
            if (!$res)
                return false;

            $id=$res->id;
            $type=$res->type;
            $url=S_Uri::getUri()->base().$this->getPage($id, $type);

            $app->redirect((string)$url);
        }
        
        return true;
    }
    
    public function checkCurrentPage($route) {
        if (S_Uri::getUri()->getHost()=="localhost") // no redirection when localhost
            return false;

        $rdomain="www";
        $app = JFactory::getApplication();
        if (!empty($route))
            $rdomain=$this->findRoutingDomain($route[0], $route[1]);

        if ($this->sdomain_str!=$rdomain) {
            // if you are on a page registered with a subdomain, then add this 
            //$url=$this->replaceSubdomain($rdomain);
            //$app->redirect((string)$url);
        }
        
        return true;
    }
    
    public function findRoutingDomain($id,$type) {
        if (empty($id))
            return 0;
        
        $query="SELECT gsubdomain,subdomain FROM hw2_route"
        ." WHERE id=".$id
        ." AND type=".$type;
        
        $db = \Hwj\JFactory::getDbo();
        $db->setQuery($query);
        
        $res = $db->loadObject();
        if (!$res)
            return 0;
        

        $domainInfo=$res;
        $subdomain= !empty($domainInfo->subdomain) ? $domainInfo->subdomain : $domainInfo->gsubdomain;
        return $subdomain;
    }
}
?>
