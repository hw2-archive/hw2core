<?php namespace Hw2;
S_Core::checkAccess(true);
// we cannot use different name class and cannot define namespace here
class plgSystemHw2 extends S_jPlugin {
    
    protected $isSite;


    public function __construct(&$subject, $config = array()) {
        $this->isSite = !S_PApi::isBackend();
        parent::__construct($subject, $config);
    }
    
    public function onAfterInitialise() {
        

        // force to load default override file before others
        $lang =&\JFactory::getLanguage();
        $extension = 'override';
        $base_dir = JPATH_BASE;
        $language_tag = 'en-GB';
        $reload = true;
        $filename=$base_dir.DS."language".DS."overrides".DS.$language_tag.".".$extension.".ini";
        $res=$lang->load($extension, $base_dir, $language_tag, $reload, true, $filename);

        if ($this->isSite) {
            /*
            //[TODO] implement routing after init in future ( using table and parent checks )
            // to speedup relocate process
            $menuitemid = JRequest::getInt( 'Itemid' );
            $route=hw2s::get("S_Controller")->getRouter()->getAllInitRInfo($menuitemid); 
             */
            S_Router::I()->checkUrlInsertion();
        }
    }
    
    public function onAfterRoute() {
        
    }
    
    public function onAfterDispatch() {
        $component=S_Uri::getInput()->get("option");
        // access control check
        if (!$this->isSite && !S_Acl::checkCustomAclTask($component) && $component!="com_login")  // [hw2] custom check
        {
            $app= new \JApplication();
            $app->redirect('index.php', S_Language::I()->getLangText("NOAUTH"));
            return;
        }
        
        if ($this->isSite) {
            if (defined('HW2_CONF_ROUTE') && HW2_CONF_ROUTE==1) {
                //$route=hw2s::get("S_Controller")->getRouter()->getPageMetaInfo(\JFactory::getDocument());
                $route= S_Router::I()->getAllRouteInfo(\JFactory::getDocument());
                 S_Router::I()->checkCurrentPage($route);
                 S_Router::I()->setCurPageRoute($route);
            }   
        }
        
        $option=\Hw2\S_Uri::getInput()->get(S_CoreDef::hw2ext);
        if ($option) {
            $contents=S_Document::renderComponent($option,false);
            \JFactory::getDocument()->setBuffer($contents, "component");
        }
    }
    
    // only 1.7+
    public function onBeforeRender() {

    }
    
    public function onAfterRender() {        
        if ($this->isSite) {
            $document = \JFactory::getDocument();
            $docType = $document->getType();

            // not in pdf's
            if ($docType == 'pdf') {
                    return;
            }

            $html = \JResponse::getBody();

            if (!empty($html)) {
                    $html=S_Language::replaceInDocument($html);
            }

            \JResponse::setBody($html);
        }
        
        if (S_FS_Config::I()->getItemVal(S_FS_Config::exec_time)==1) {
            S_Profiler::getExecutionTime();
        }
        
        // last instruction
        S_CronJobs::init();
    }
    
    public function onBeforeCompileHead() {
        S_Core::callGlobal("Hw2\S_Loader", "loadMedia");
        
        S_Document::renderHead();
    }
    
    public function onSearch() {
        
    }
    
    public function onSearchAreas() {
        
    }
    
    public function onGetWebServices() {
        
    }
    
    public function onContentBeforeSave() {
        
    }
    
    public function onContentAfterSave() {
        S_jForm::eventStore(S_FS_Actions::save(), func_get_args());
    }
    
    public function onContentBeforeDelete() {
        S_jForm::eventStore(S_FS_Actions::delete(), func_get_args());
    }
    
    public function onContentAfterDelete() {
    }
    
    public function onContentChangeState() {
        
    }
}


class S_jPlugin extends \JPlugin { 
    const onBeforeExecute="onBeforeExecute";
    const onAfterExecute="onAfterExecute";
    
    public static function register($type,$name) {        
        $class=  get_called_class();
        $dispatcher = \JDispatcher::getInstance(); /** @see https://docs.google.com/document/d/1IXepD_GS0Y9YEvtLlSCRls2A4n_WauyGnyyDyGyLzLI/pub **/
        $config=Array(
            "name"=>$name,
            "type"=>$type
        );
        
        new $class($dispatcher,$config);
    }
}
?>
