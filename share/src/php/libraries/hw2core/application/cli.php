<?php namespace Hw2;
S_Core::checkAccess();

class S_jCliApp extends \Hwj\JApplicationCli {
    protected function doExecute() {
        S_CliMenu::init($this->get("argc"), $this->get("argv"));
    }
}

class S_Cli_Obj extends S_Object {
    private static $prompt;
    private static $jCli;
    
    /**
     * 
     * @return S_Cli_Prompt
     */
    public static function prompt($new=false) {
        if (!self::$prompt || $new)
            self::$prompt=new S_Cli_Prompt;
        
        return self::$prompt;
    }
    
    /**
     * 
     * @return \Hwj\JApplicationCli
     */
    public static function getJCli() {
        if (!self::$jCli) {
            // first "base" instance has been created at jplatform load
            self::$jCli=S_jCliApp::getInstance("Hw2\S_jCliApp");
            self::$jCli->loadIdentity();
        }
        return self::$jCli;
    }
}

class S_ApplicationCli extends S_Cli_Obj {    
    
    public static function init($argc,$argv) { 
        register_shutdown_function('Hw2\S_Client::handleShutdown');
        self::getJCli()->set("argc",$argc);
        self::getJCli()->set("argv",$argv);
        self::getJCli()->execute();
    }
    
    public static function handleShutdown() {
        // to avoid suddenly closing
        $error = error_get_last();
        if ($error['type'] === E_ERROR) {
            self::prompt()->get("== aborted! ==");
        }
    }
}

class S_Cli_Prompt extends S_Prompt {
    private $corePath;
    
    public function bashCmd($file,Array $args, &$return_var = null) {
        return system("bash ".$file." \"". implode("\" \"", $args)."\"\"",$return_var);
    }
    
    public function loaderCmd($script, Array $vars=null,$run=true) {
        $conf=new S_Cli_BashConfig();
        foreach ($vars as $key => $value)
            $conf->setConf($key, $value);
        $confOut=$conf->getOutput();
        /* @var $v S_Data */
        $v = S_Data::C()->_setObjVars($vars);
        $v->hw2core_path=$this->getCorePath();
        $v->LOADER_SCRIPT=$script;
        $code="\"$v \n $confOut source ".$this->getLoader()."\"";
        return $run ? system("bash -c $code",$return_var) : $code;
    }

    public function getLoader() {
        return $this->getCorePath()."/share/src/bashscripts/loader.sh";
    }
    
    public function getCorePath() {
        return $this->corePath ? $this->corePath : HW2PATH_CORE;
    }
    
    public function setCorePath($path,$reset=false) {
        $this->corePath =  $reset ? null : $path;
    }
}


class S_Prompt {

    private $tty;

    function __construct() {
        if (\Hwj\IS_WIN) {
            $this->tty = fOpen("\con", "rb");
        } else {
            if (!($this->tty = fOpen("/dev/tty", "r"))) {
                $this->tty = fOpen("php://stdin", "r");
            }
        }
    }
    


    function gets($string, $length = 1024) {
        echo $string;
        $result = trim(fGets($this->tty, $length));
        echo "\n";
        return $result;
    }
    
    function fscanf($string, $format,&$result=null) {
        echo $string;
        $res=fscanf($this->tty,$format,$result);
        echo "\n";
        return $res;
    }
    
    public static function printf($string="", $newline = true) {
        $string.=$newline ? "\n" : null;
        fwrite(STDOUT, $string); // same of echo
    }
    
    public static function outError($string) {
        fwrite(STDERR, $string);
    }
}

?>
