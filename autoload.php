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
ini_set("error_reporting","E_ALL & ~E_NOTICE");
ini_set('magic_quotes_gpc',0);
/**
 * 框架根路径
 */
define('UTF_ROOT', dirname(__FILE__));
/**
 * 应用根路径
 */
define('APP_ROOT', dirname(__FILE__).'/app');
/**
 * 应用公共模块
 */
define('PUB_PATH', APP_ROOT.'/modules/ut-frame');
/**
 * 应用公共模板
 */
define('PUB_TEMP', PUB_PATH.'/skin');
/**
 * 开启session
 */
session_start();
/**
 * 类自动加载
 */
require UTF_ROOT.'/library/UsualToolLoad.php';
/**
 * 加载配置
 */
$config=library\UsualToolInc\UTInc::GetConfig();
/**
 * 识别路由
 */
foreach(library\UsualToolRoute\UTRoute::Analy(library\UsualToolInc\UTInc::CurPageUrl()) as $key=>$val){
    $_GET[$key]=$val;
}
/**
 * 加载模块
 */
$m=empty($_GET["m"]) ? $config["DEFAULT_MOD"] : library\UsualToolInc\UTInc::SqlCheck($_GET["m"]);
/**
 * 加载页面
 */
$p=empty($_GET["p"]) ? $config["DEFAULT_PAGE"] : library\UsualToolInc\UTInc::SqlCheck(str_replace(".php","",$_GET["p"]));
/**
 * 获取当前模块目录
 */
$modpath=APP_ROOT."/modules/".$m;
/**
 * 获取模板末尾节点路径
 */
$endpath=library\UsualToolInc\UTInc::TempEndPath();
/**
 * 配置应用模板引擎
 * 拼接模板工程路径
 */ 
$frontwork=APP_ROOT."/formwork/".$config["FORMWORK_FRONT"];
$adminwork=APP_ROOT."/formwork/".$config["FORMWORK_ADMIN"];
$isdevelop=library\UsualToolInc\UTInc::Contain("app/dev",library\UsualToolInc\UTInc::CurPageUrl());
/**
 * 开发端
 */
if($config["FORMWORK_ADMIN"]!=0 && $isdevelop):
    $skin=$adminwork."/skin/".$m;
    $cache=$skin."/cache";
/**
 * 客户端
 */
elseif($config["FORMWORK_FRONT"]!=0 && !$isdevelop):
    $skin=$frontwork."/skin/".$m;
    $cache=$skin."/cache";
/**
 * 默认配置
 */
else:
    $skin=$modpath."/skin";
    $cache=$modpath."/cache";
endif;
$app=new library\UsualToolTemp\UTTemp(
    $config["TEMPCACHE"],
    $skin."/".$endpath,
    $cache."/".$endpath
);
/**
 * 绑定应用名称、地址、模块及页面
 */
$app->Runin(array("appname","appurl","module","page"),array($config["APPNAME"],$config["APPURL"],$m,$p));
/**
 * 获取应用语言
 */
$app->Runin(array("lang","thelang"),array(explode(",",$config["LANG_OPTION"]),$config["LANG"]));
if(!empty($_COOKIE['Language'])):
    $language=library\UsualToolInc\UTInc::SqlCheck($_COOKIE['Language']);
else:
    if($config["LANG"]=="big5"):
        $language="zh";
        setcookie("Language","zh");
        setcookie("chinaspeak","big5");
    else:
        $language=$config["LANG"];
        setcookie("Language",$config["LANG"]);
        setcookie("chinaspeak","");
    endif;
endif;
/**
 * 其他可选设置
*/
$app->Runin(array("editor"),array($config["EDITOR"]));