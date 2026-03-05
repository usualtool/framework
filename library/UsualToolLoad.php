<?php
/**
       * --------------------------------------------------------       
       *  |                  █   █ ▀▀█▀▀                    |           
       *  |                  █▄▄▄█   █                      |           
       *  |                                                 |           
       *  |    Author: Huang Hui                            |           
       *  |    Repository 1: https://gitee.com/usualtool    |           
       *  |    Repository 2: https://github.com/usualtool   |           
       *  |    Applicable to Apache 2.0 protocol.           |           
       * --------------------------------------------------------       
*/
class Loader{
    //共享映射
    private static $mapping=[
        'share'=>APP_ROOT.'/share'
    ];
    public static function Register(){
        //第三方依赖
        $vendor=UTF_ROOT.'/vendor/autoload.php';
        if(file_exists($vendor)){
            require_once $vendor;
        }
        spl_autoload_register(['Loader','AutoLoad']);
    }
    public static function AutoLoad($class){
        $parts=explode('\\',$class);
        $count=count($parts);
        if($count<2) return false;
        $place=strtolower($parts[0]);
        //类库
        if($place==='library'){
            $path_part=array_slice($parts,1,-1); 
            $filename_path=implode('/',$path_part);
            $file_path=UTF_ROOT.'/library/'.$filename_path.'.php';
            if(file_exists($file_path)){
                require_once $file_path;
                return true;
            }
            return false;
        }
        //模型
        if($place==='model' && $count>=3){
            $module=strtolower($parts[1]);
            $sub_part=array_slice($parts,2);
            $sub_path=implode('/',$sub_part).'.php';
            $model=APP_ROOT.'/modules/'.$module.'/model/'.$sub_path;
            if(file_exists($model)){
                require_once $model;
                return true;
            }
            return false;
        }
        //通用PSR-4
        if(isset(Loader::$mapping[$place])){
            $basedir=Loader::$mapping[$place];
            $relative=implode('/',array_slice($parts,1));
            $file_path=$basedir.'/'.$relative.'.php';
            if(file_exists($file_path)){
                require_once $file_path;
                return true;
            }
            return false;
        }
        return false;
    }
}
Loader::Register();