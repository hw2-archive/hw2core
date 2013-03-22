<?php namespace Hw2;
S_Core::checkAccess();

set_time_limit(0);
@ob_end_flush();
ob_implicit_flush(true);

final class S_BashScr {
    const config=-1;
    const symlink=1;
    const backup=2;
    const mysql_dump=3;
    const dbsync=4;
    const cleangit=5;
    const create_branch=6;
    const conn_remote=7;
}

class S_CliMenu extends S_Cli_Obj {    
    // for windows
    //$result=shell_exec("C:\bash-2.03\bash.exe test.txt");
    //echo($result);
    
    private static function executeMenu($opt,S_Cli_Prompt $prompt) {
        switch ($opt ) {
            // internal cases
            case -1:
                // output configs
                $prompt->printf($prompt->loaderCmd(S_BashScr::config,null,false));
            break;
            case 0:
                ; // exit
            break;
            // menu cases
            case 1: 
                $prompt->loaderCmd(S_BashScr::symlink);
            break;
            case 2: //backup folder
                $prompt->loaderCmd(S_BashScr::backup);
            break;
            case 3: // mysql dump
                $prompt->loaderCmd(S_BashScr::mysql_dump);
            break;
            case 4: // db sync
                $prompt->loaderCmd(S_BashScr::dbsync);
            break;
            /*case 5: // clean git
                S_Cli_Git::init();
            break;*/
            case 6: // connect remote
                $prompt->loaderCmd(S_BashScr::conn_remote);
            break;
            case 7: // site creator
                S_Cli_SiteCreator::init();
            break;
            /*case 8: // obfuscator
                S_Cli_Obfuscator::init(S_Factory::getConf(S_CfgSec::info,"alias"));
            break;*/
            case 9: // backup site
                S_Com_Backup::run("backup");
            break;
            default:
                $prompt->printf("No option selected.");
            break;
        }
    }
    
    public static function init($argc,$argv) { 
        
        $prompt=self::prompt();
        if ($argc == 2 ) {
            self::executeMenu($argv[1],$prompt);
        } else {
            $prompt->printf("====== Hw2Core =====");
            $prompt->printf("type 'exit' to quit");
            do {
                $prompt->printf("====== MENU =====");
                $prompt->printf("### BASH ###");
                $prompt->printf(" 1: create links");
                $prompt->printf(" 2: backup folder");
                $prompt->printf(" 3: mysql dump");
                $prompt->printf(" 4: db sync");
                //$prompt->printf(" 5: clean git");
                $prompt->printf(" 6: remote connection");
                $prompt->printf("### PHP ###");
                $prompt->printf(" 7: site creator");
                //$prompt->printf(" 8: obfuscator");
                $prompt->printf(" 9: backup site");
                $prompt->printf(" 0: exit");
                $prompt->printf();
                $buffer = $prompt->gets("Select an option: ");
                if (is_numeric($buffer)) {
                    $opt=intval ($buffer);
                    self::executeMenu($buffer,$prompt);
                }

            } while ($buffer !== "exit" && $buffer !=="0");
            $prompt->printf("Goodbye");
            exit();
        }
    }
}
?>
