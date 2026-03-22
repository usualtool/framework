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
require_once __DIR__.'/'.'config.php';
use library\UsualToolInc\UTInc;
use library\UsualToolDebug\UTDebug;
/**
 * 控制终端
 */
$_form_="front";
/**
 * 模板终端
 */
$_temp_="front";
/**
 * 接引模板
 */
$app->Runin("pubtemp",PUB_TEMP."/".$_temp_);
$app->Runin("template",$frontwork."/skin/".$config["DEFAULT_MOD"]."/".$_temp_);
/**
 * 路由分发控制
 */
$_map_=$modpath."/route.php";
$_file_=$p;
if(UTInc::SearchFile($_map_)):
    $_route=include $_map_;
    $_file_=$_route[$p] ?? $p;
endif;
$_file_path_=$modpath."/".$_form_."/".$_file_.".php";
/**
 * 判断文件真实性
 */
if(UTInc::SearchFile($_file_path_)):
    require_once $_file_path_;
    $_class_=UTInc::GetClassName($_file_path_);
    /**
     * 分层模式
     */
    if($_class_):
        $action=$_GET["action"] ?? "";
        $action=preg_match('/^[a-zA-Z0-9_]+$/',$action) ? $action : "index";
        $controller=new $_class_();
        /**
         * 执行动作
         */
        if(method_exists($controller,$action) || method_exists($controller,'__call')):
            $controller->$action();
        endif;
    endif;
else:
    UTDebug::Error("module",str_replace(APP_ROOT."/modules","",$modfile));
endif;
$config["DEBUG"] && UTDebug::Debug($config["DEBUG_BAR"]);