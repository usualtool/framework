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
require_once __DIR__.'/config.php';
use library\UsualToolInc\UTInc;
use library\UsualToolDebug\UTDebug;
/**
 * 权限中间件
 */
$_page_=$m."/".$_form_."/".$p;
$_power_page_=$_deve_ ? ($config["ADMIN_POWER_PAGE"] ?? '') : ($config["FRONT_POWER_PAGE"] ?? '');
$_power_out_=$_deve_ ? ($config["ADMIN_POWER_OUT"] ?? '')  : ($config["FRONT_POWER_OUT"] ?? '');
if(!empty($_power_page_) && !UTInc::Contain($_page_,$_power_out_)){
    require_once MODULE_PATH."/".$_power_page_.".php";
}
/**
 * 路由分发控制
 */
$_map_=$_modpath_."/route.php";
$_file_=$p;
if(UTInc::SearchFile($_map_)){
    $_route=include $_map_;
    $_file_=$_route[$p] ?? $p;
}
$_file_path_=$_modpath_."/".$_form_."/".$_file_.".php";
/**
 * 判断文件真实性
 */
if(UTInc::SearchFile($_file_path_)){
    require_once $_file_path_;
    $_class_=UTInc::GetClassName($_file_path_);
    /**
     * 分层模式
     */
    if($_class_){
        $action=$_GET["action"] ?? "";
        $action=preg_match('/^[a-zA-Z0-9_]+$/',$action) ? $action : "index";
        $controller=new $_class_();
        /**
         * 执行动作
         */
        if(method_exists($controller,$action) || method_exists($controller,'__call')){
            $controller->$action();
        }
    }
}else{
    UTDebug::Error("module",str_replace(APP_ROOT."/modules","",$_file_path_));
}
$config["DEBUG"] && UTDebug::Debug($config["DEBUG_BAR"]);