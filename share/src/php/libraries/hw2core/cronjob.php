<?php namespace Hw2;
S_Core::checkAccess();

class S_CronJobs extends S_Object {
    
    /**
     * trait use alternative
     * @return S_ClassRegister
     */
    public static function clReg() { return S_ClassRegister::I(self); }
    
    public static function run() {
        if (S_FS_Config::I()->getItemVal(S_FS_Config::cronjob_debug) != 1 ) {
            ignore_user_abort(true); // optional
            session_write_close();//close session file on server side to avoid blocking other requests
            header("Content-Encoding: none");//send header to avoid the browser side to take content as gzip format
            header("Content-Length: ".ob_get_length());//send length header
            header("Connection: close");
            //header("Location:".$url, true);
            //fastcgi_finish_request(); // important when using php-fpm!
            ob_end_flush();flush();//really send content, can't change the order:1.ob buffer to normal buffer, 2.normal buffer to output
        } else
            S_Exception::raise ("NOTICE: Cronjob debug enabled", S_Exception_type::notice (),false);
        // Do processing here 
        S_Core::setRoot(true); // set core on root mode

        $cb=self::clReg()->getCallBacks("cronjob");
        if (!empty($cb)) {
            foreach ($cb as $method) {
                call_user_func($method);
            }
        }

        exit();
    }
    
    public static function init() {
        if (!parent::initReg()) {  
            ob_start(); // will be closed in run function
            S_ShutdownScheduler::registerShutdownEvent('Hw2\S_CronJobs::run');
        }
    }
    
    public static function addCronJob($class_name) {
        self::clReg()->addCallBack("cronjob", $class_name."::cronJob");
    }
}
?>
