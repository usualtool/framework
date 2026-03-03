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
 * 控制方向
 */
$controlform="/front";
/**
 * 公共模板
 */
$app->Runin("pubtemp",PUB_TEMP.$controlform);
/**
 * 公共模板工程
 */
$app->Runin("template",$frontwork."/skin/".$config["DEFAULT_MOD"].$controlform);
/**
 * 拼接当前文件
 */
$modfile=$modpath."/".$controlform."/".$p.".php";
/**
 * 判断文件真实性
 */
if(UTInc::SearchFile($modfile)):
    require_once $modfile;
    $classname=UTInc::GetClassName($modfile);
    /**
     * 分层模式
     */
    if($classname):
        $action=UTInc::SqlCheck($_GET["action"]) ?? "index";
        if(!preg_match('/^[a-zA-Z0-9_]+$/',$action)):
            $action="index"; 
        endif;
        $controller=new $classname();
        /**
         * 执行动作
         */
        if(method_exists($controller,$action) || method_exists($controller,'__call')):
            $controller->$action();
        endif;
    endif;
else:
    require_once PUB_PATH.'/front/error.php';
    exit();
endif;
$config["DEBUG"] && UTDebug::Debug($config["DEBUG_BAR"]);