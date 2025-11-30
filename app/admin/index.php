<?php
/**
       * --------------------------------------------------------       
       *  |                  █   █ ▀▀█▀▀                    |           
       *  |                  █▄▄▄█   █                      |           
       *  |                                                 |           
       *  | Author:HuangDou   Email:292951110@qq.com        |           
       *  | QQ-Group:583610949                              |           
       *  | Applicable to Apache 2.0 protocol.              |           
       * --------------------------------------------------------       
*/
require dirname(dirname(__FILE__)).'/'.'config.php';
use library\UsualToolInc\UTInc;
/**
 * 写入后端公共模板路径
 * 公共使用的模板可以放在公共模块ut-frame/skin/下
 * 使用方法：<{include "$pubtemp/head.cms"}>
 * 当前示例表示后端共用头部模板
 * 以下设置/admin表示后端，/front表示前端
 */
$app->Runin("pubtemp",PUB_TEMP."/admin");
/**
 * 写入模板工程后端公共路径
 */
$app->Runin("template",$adminwork."/skin/".$config["DEFAULT_MOD"]."/admin");
/**
 * 权限验证机制
 * 排除不需要验证的页面
 * 数组形式Contain($p,array("login","captcha"))，判断数组中是否包含页面名$p
 * 字符形式Contain("login",$p)，判断页面名$p中是否包含login
 */
if(!UTInc::Contain($p,array("login","captcha"))){
    /**
     * 加载自定义权限文件
     * 该文件亦可封装为函数让autoload自动加载
     */
    require PUB_PATH.'/admin/session.php';
}
/**
 * 拼接当前文件
 */
$modfile=$modpath."/admin/".$p.".php";
/**
 * 判断文件真实性
 */
if(library\UsualToolInc\UTInc::SearchFile($modfile)){
    /**
     * 引用后端模板
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