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
class Loader {
    public static function Register(){
        //加载第三方依赖库
        $vendorload=UTF_ROOT.'/vendor/autoload.php';
        if(file_exists($vendorload)){
            require_once $vendorload;
        }
        spl_autoload_register(['Loader','AutoLoad']);
    }

    public static function AutoLoad($class){
        $parts=explode('\\',$class);
        $count=count($parts);
				//加载框架类库
        if($count>=3 && strtolower($parts[0])==='library'){
            $path_part=array_slice($parts, 1, -1); 
            $filename_path=implode('/',$path_part);
            $file_path=UTF_ROOT.'/library/'.$filename_path.'.php';
            if(file_exists($file_path)){
                require_once $file_path;
								return true;
            }
        }
				//加载模块模型
        if($count>=3 && strtolower($parts[0])==='model'){
            $modulename=$parts[1];
            $dir_module_name=strtolower(str_replace('_','-',$modulename));
            $model_part=array_slice($parts, 2);
            if(!empty($model_part)){
                //灵活风格之去尾
                if(count($model_part)===1){
                    $filename_path=$model_part[0];
                }else{
                    $filename_path=implode('/',array_slice($model_part,0,-1));
                }
                $file_path=APP_ROOT.'/modules/'.$dir_module_name.'/model/'.$filename_path.'.php';
                if(file_exists($file_path)){
                    require_once $file_path;
										return true;
                }
                //PSR4标准风格
                $full_path=APP_ROOT.'/modules/'.$dir_module_name.'/model/'.implode('/',$model_part).'.php';
                if(file_exists($full_path)){
                    require_once $full_path;
										return true;
                }
            }
        }
        return false;
    }
}
Loader::Register();