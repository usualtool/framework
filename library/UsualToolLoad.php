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
class Loader{
    public static function AutoLoad($class){
        $class=preg_replace('/(.*)\\\{1}([^\\\]*)/i','$1',$class);
        //UT CLASS UTF_ROOT
        $myutf=str_replace('\\','/',UTF_ROOT.'\\'. $class).'.php';
        //APP CLASS APP_ROOT
        $myapp=str_replace('\\','/',APP_ROOT.'\\'. $class).'.php';
        if(file_exists($myutf)){
            require_once $myutf;
        }elseif(file_exists($myapp)){
            require_once $myapp;
        }
    }
}
spl_autoload_register("Loader::AutoLoad");