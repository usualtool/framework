<?php
/**
       * --------------------------------------------------------       
       *  |    ░░░░░░░░░     █   █░▀▀█▀▀░    ░░░░░░░░░      |           
       *  |  ░░░░░░░         █▄▄▄█   █                      |            
       *  |                                                 |            
       *  | Author:HuangDou   Email:292951110@qq.com        |            
       *  | QQ-Group:583610949                              |           
       *  | WebSite:http://www.UsualTool.com                |            
       *  | UT Framework is suitable for Apache2 protocol.  |            
       * --------------------------------------------------------                
*/
/**
* Compatible with PSR0
* 
*/
class Loader{
    public static function AutoLoad($class){
        $class=preg_replace('/(.*)\\\{1}([^\\\]*)/i','$1',$class);
        /*UT built-in function library*/
        $lib_load_file=str_replace('\\','/',UTF_ROOT.'\\'. $class).'.php';
        /*App custom function library*/
        $app_load_file=str_replace('\\','/',APP_ROOT.'\\'. $class).'.php';
        if(file_exists($lib_load_file)){
            require_once $lib_load_file;
        }elseif(file_exists($app_load_file)){
            require_once $app_load_file;
        }
    }
}
spl_autoload_register("Loader::AutoLoad");
/**
* Compatible with PSR4
* Composer dependency Library
*/
if(library\UsualToolInc\UTInc::SearchFile(UTF_ROOT."/vendor/autoload.php")):
    require_once UTF_ROOT.'/vendor/autoload.php';
endif;