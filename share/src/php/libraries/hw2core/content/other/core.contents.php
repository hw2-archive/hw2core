<?php namespace Hw2;
S_Core::checkAccess();

class S_CT_Core extends S_CT_Manager {
    const last_bak_time=1;
    const last_css_rebuild=2;
    const last_gcounter_refresh=3;
    const site_visits=4;
    
    public function __construct() {
        parent::__construct(true);
        $name=S_CT_Element::cname();
        
        $this->addCT(self::last_bak_time, $name,'*',Array("alias"=>"last_bak_time"));
        $this->addCT(self::last_css_rebuild, $name,'*',Array("alias"=>"last_css_rebuild"));
        $this->addCT(self::last_gcounter_refresh, $name,'*',Array("alias"=>"last_gcounter_refresh"));
        $this->addCT(self::site_visits, $name,'*',Array("alias"=>"site_visits"));
    }
    
    /**
     * 
     * @return S_CT_Core
     */
    public static function I() {
        return parent::I();
    }
    
    /**
     * 
     * @return S_CT_Element
     */
    public function getContent($id) {
        return parent::getContent($id, S_CT_Element::getSection(),true); 
    }
    
    public function getContentVal($id,$default=null) {
        return $this->getContent($id)->getElemVal($default);
    }
    
    public function setContentVal($id,$val,$save=false) {
        $content=$this->getContent($id);
        $content->setElemVal($val);
        if ($save) {
            $this->save($id);
        }
    }
    
    public function save($id) {
        parent::save($id, S_CT_Element::getSection());
    }
}
?>
