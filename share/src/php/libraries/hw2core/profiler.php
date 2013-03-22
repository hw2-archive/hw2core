<?php namespace Hw2;
S_Core::checkAccess();

class S_Profiler {    
    public static function getExecutionTime() {
        // put this at the top of your page/script
        $exectime = \microtime(); 
        $exectime = explode(" ",$exectime); 
        $exectime = $exectime[1] + $exectime[0]; 
        $starttime = $exectime; 

        /*
         *   place your script(s) to here 
         */

        // put this at the bottom of your page/script
        $exectime = \microtime(); 
        $exectime = explode(" ",$exectime); 
        $exectime = $exectime[1] + $exectime[0]; 
        $endtime = $exectime; 
        $totaltime = ($endtime - $starttime); 
        echo "This page has been created in ".$totaltime." seconds";
    }
}


?>
