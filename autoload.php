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
    //通用映射
    private static $mapping=[
        'task'=>APP_ROOT.'/task',
        'share'=>APP_ROOT.'/share',
        'module'=>APP_ROOT.'/modules',
        'plugin'=>APP_ROOT.'/plugins'
    ];
    public static function Register(){
        //核心依赖
        if(!class_exists('usualtool\Lib') && !file_exists(UTF_ROOT.'/vendor/usualtool/ut-lib')){
            http_response_code(503);
            header('Content-Type: text/html; charset=utf-8');
            echo'<pre style="padding:24px;font:14px/1.8 monospace">';
            echo'框架缺少 usualtool/ut-lib 依赖<br/>';
            echo'https://github.com/usualtool/ut-lib';
            echo'</pre>';
            exit;
        }
        //第三方依赖
        $vendor=UTF_ROOT.'/vendor/autoload.php';
        if(file_exists($vendor)){
            require_once $vendor;
        }
        spl_autoload_register(['Loader','AutoLoad']);
        //权限
        self::Permission();
    }
    public static function AutoLoad($class){
        $parts=explode('\\',$class);
        $count=count($parts);
        if($count<2) return false;
        $place=strtolower($parts[0]);
        //兼容旧版类库
        if($place==='library'){
            $path_part=array_slice($parts,1,-1); 
            $filename_path=implode('/',$path_part);
            return self::Load(UTF_ROOT.'/library',$filename_path.'.php');
        }
        //模型
        if($place==='model' && $count>=3){
            $module=str_replace('_','-',strtolower($parts[1]));
            $sub_part=array_slice($parts,2);
            $sub_path=implode('/',$sub_part).'.php';
            return self::Load(APP_ROOT.'/modules',$module.'/model/'.$sub_path);
        }
        //控制
        if($place==='controller' && $count>=3){
            $module=str_replace('_','-',strtolower($parts[1]));
            $item=strtolower($parts[2]);
            $lowercase=($item=='front' || $item=='admin');
            if($item=='front' || $item=='admin'){
                $sub_part=array_slice($parts,3);
                $middle=$item;
            }else{
                $sub_part=array_slice($parts,2);
                $middle='controller';
            }
            $sub_path=implode('/',$sub_part).'.php';
            if(self::Load(APP_ROOT.'/modules',$module.'/'.$middle.'/'.$sub_path)) return true;
            if($lowercase){
                $dir_path=dirname($sub_path);
                $lower_sub_path=($dir_path==='.' ? '' : $dir_path.'/').strtolower(basename($sub_path,'.php')).'.php';
                return self::Load(APP_ROOT.'/modules',$module.'/'.$middle.'/'.$lower_sub_path);
            }
            return false;
        }
        //通用PSR-4
        if(isset(Loader::$mapping[$place])){
            $basedir=Loader::$mapping[$place];
            $relative=implode('/',array_slice($parts,1));
            return self::Load($basedir,str_replace('_','-',$relative).'.php');
        }
        return false;
    }
    private static function Load($base,$relative){
        $file_path=$base.'/'.$relative;
        if(file_exists($file_path)){
            require_once $file_path;
            return true;
        }
        $ci_path=self::ResolvePath($base,$relative);
        if($ci_path!==false){
            require_once $ci_path;
            return true;
        }
        return false;
    }
    private static function ResolvePath($base,$relative){
        $path=rtrim($base,'/\\');
        $segments=explode('/',str_replace('\\','/',$relative));
        foreach($segments as $segment){
            if($segment==='') continue;
            $next=$path.'/'.$segment;
            if(@file_exists($next)){
                $path=$next;
                continue;
            }
            $matched=false;
            $items=@scandir($path);
            if($items!==false){
                foreach($items as $item){
                    if($item==='.' || $item==='..') continue;
                    if(strcasecmp($item,$segment)===0){
                        $path=$path.'/'.$item;
                        $matched=true;
                        break;
                    }
                }
            }
            if(!$matched) return false;
        }
        return @is_file($path) ? $path : false;
    }
    private static function Permission(){
        $root=defined('UTF_ROOT') ? UTF_ROOT : __DIR__;
        $checks=[];
        foreach(['log'=>'日志','update'=>'升级'] as $rel=>$desc){
            $checks[$root.'/'.$rel]=$desc;
        }
        foreach(glob($root.'/app/modules/*/cache',GLOB_ONLYDIR) ?: [] as $p){
            $checks[$p]='模板编译缓存';
        }
        foreach(glob($root.'/app/template/*/skin/*/*/cache',GLOB_ONLYDIR) ?: [] as $p){
            $checks[$p]='模板工程缓存';
        }
        $bad=[];
        foreach ($checks as $p => $desc){
            if(is_dir($p) && !is_writable($p)){
                $bad[]=str_replace($root,'',$p).' ('.$desc.')';
            }
        }
        if(!$bad) return;
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo'<pre style="padding:24px;font:14px/1.8 monospace">';
        echo'框架部分目录无写入权限<br/>';
        foreach($bad as $b) echo' - '.htmlspecialchars($b,ENT_QUOTES,'UTF-8')."\r\n";
        echo'</pre>';
        exit;
    }
}
Loader::Register();