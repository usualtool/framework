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
require_once dirname(dirname(__FILE__)).'/'.'autoload.php';
/**
 * 写入前端公共模板路径
 */
$app->Runin("pubtemp",PUB_TEMP."/front");
/**
 * 写入模板工程前端公共路径
 */
$app->Runin("template",$frontwork."/skin/".$config["DEFAULT_MOD"]."/front");
/**
 * 拼接当前文件
 */
$modfile=$modpath."/front/".$p.".php";
/**
 * 判断文件真实性
 */
if(library\UsualToolInc\UTInc::SearchFile($modfile)){
    /**
     * 引用前端模板
     */
    require_once $modfile;
}else{
    /**
     * 配置公共错误提示
     */
    require_once PUB_PATH.'/front/error.php';
    exit();
}
if($config["DEBUG"]){
    library\UsualToolDebug\UTDebug::Debug($config["DEBUG_BAR"]);
}