<?php namespace Hw2;
S_Core::checkAccess();
/** GAcounter 1.1 - 03 settembre 2012
* based on GAPI php class
* ( http://code.google.com/p/gapi-google-analytics-php-interface/ )
* developed by Marco Cilia ( http://www.goanalytics.info )
*/

class S_Gcounter extends S_Object {
    private static $_inUse=false;

    public static function render() {
        self::$_inUse=true;
        $coreCT=S_CT_Core::I();

        echo "<div id=\"gacounter\">";
        echo "<div id=\"visits\">visite</div><div class=\"numero1\">" . $coreCT->getContentVal(S_CT_Core::site_visits) . "</div>";
        //echo "<div id=\"visitors\">visitatori</div><div class=\"numero2\">" . $ga->getVisitors() . "</div>";
        //echo "<div id=\"pv\">pagine viste</div><div class=\"numero1\">" . $ga->getPageviews() . "</div>";	
        //echo "<div id=\"credits\"><a href=\"http://www.goanalytics.info/gac.php\">GAcounter by goanalytics.info</a></div>";
        echo "</div>";	
    }
    
    public static function cronJob() {
        if (self::$_inUse) {
            $coreCT=S_CT_Core::I();
            $conf=  S_FS_Config::I();
            $lastRefresh=$coreCT->getContentVal(S_CT_Core::last_gcounter_refresh);
            $delay=time() - $lastRefresh;
            $gdelay=S_Factory::getConf(S_CfgSec::hw2core, "gcounter_delay");
            if ( $delay >= $gdelay) {
                define('ga_email',$conf->getItemVal(S_FS_Config::gmail_account));
                define('ga_password',$conf->getItem(S_FS_Config::gmail_password)->getValDecoded());
                define('ga_profile_id',$conf->getItemVal(S_FS_Config::ganalytic_id));
                $startdate = '2009-04-01'; //data nel formato YYYY-MM-DD
                
                $coreCT->setContentVal(S_CT_Core::last_gcounter_refresh, time(), true);
                $ga = new S_GApi(ga_email,ga_password);
                $ga->requestReportData(ga_profile_id,array('visitorType'),array('visitors','pageviews','visits'),'','',$startdate);
                $coreCT->setContentVal(S_CT_Core::site_visits, $ga->getVisits(), true);
            }
        }
    }
}

?>
