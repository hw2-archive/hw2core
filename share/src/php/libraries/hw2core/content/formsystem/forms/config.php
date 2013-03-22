<?php namespace Hw2;
S_Core::checkAccess();

class S_FS_Config extends S_FS_Content {
    const cronjob_debug="cronjob_debug";
    const exec_time="exec_time";
    const bak_enabled="bak_enabled";
    const bak_delay="bak_delay";
    const bak_last_day="bak_last_day";
    const css_rebuild="css_rebuild";
    const gmail_account="gmail_account";
    const gmail_password="gmail_password";
    const ganalytic_id="ganalytic_id";
    const save="hid_save";

    function __construct($id=HW2FS_CONF_ID, $lang = "*",Array $options=null) {
        parent::__construct($id, $lang,$options);
        //create fields
        $nc=new S_CT_NodeMgr($this); //node creator
        $node=$nc->_(S_FS_FormTag::cname(),"config_form","Config Form",false,Array("settings"=>Array("form_method"=>"post")));
        $node->setChild($nc->_(S_FS_NumericBox::cname(),self::cronjob_debug, "CronJob debug Mode"));
        $node->setChild($nc->_(S_FS_NumericBox::cname(),self::bak_enabled, "Enable Backup"));
        $node->setChild($nc->_(S_FS_NumericBox::cname(),self::exec_time, "Execution time"));
        $node->setChild($nc->_(S_FS_NumericBox::cname(),self::bak_delay, "Backup delay in seconds:"));
        $node->setChild($nc->_(S_FS_NumericBox::cname(),self::bak_last_day, "Delete backup older than days:"));
        $node->setChild($nc->_(S_FS_SingleLineText::cname(),self::gmail_account, "GMail Account:"));
        $node->setChild($nc->_(S_FS_Password::cname(),self::gmail_password, "GMail Password:"));
        $node->setChild($nc->_(S_FS_NumericBox::cname(),self::ganalytic_id, "Google Analytic ID:",false,Array("settings"=>Array("description"=>"The google analytic profile ID"))));
        $node->setChild($nc->_(S_FS_HiddenField::cname(),self::save, "HiddenVal",false,Array("settings"=>Array("default_value"=>"save")),false,false));
        $node->setChild($nc->_(S_FS_SubmitButton::cname(),"save_btn", "Save",false,null,false,false));
        $this->addNode($node);
        $this->addNode(S_FS_ValueBtn::_($this, self::css_rebuild, "Rebuild CSS", Array("settings"=>Array("form_method"=>"post","default_value"=>"rebuild_css"))));
    }

    function renderForm(S_Node $firstNode = null) {
        ?>
        <fieldset class="adminform">
            <legend>Hw2Config</legend>
            <ul class="adminformlist">
                <?php
                    parent::renderForm($this->getNode("config_form"));
                ?>
            </ul>
        </fieldset>
        <fieldset class="adminform-2">
            <legend>Special</legend>
            <ul class="adminformlist">
                <?php
                    parent::renderForm($this->getNode(self::css_rebuild."_form"));
                ?>
            </ul>
        </fieldset>
        <?php
    }
    
    /**
     * 
     * @return S_FS_Config
     */
    public static function I() {
        return parent::I();
    }
    
    public static function init() {
        parent::init();
        self::I()->getLoader()->getValuesFromDb();
    }
    
    public static function getSection() { return S_CT_Sec::jconfig(); }
    public static function getLayout() { return S_LtConf::def; }

}

?>
