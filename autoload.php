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
ini_set("display_errors","Off");
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
/**
 * 系统级全局路径
 */
defined('UTF_ROOT') or define('UTF_ROOT',__DIR__);
defined('APP_ROOT') or define('APP_ROOT',__DIR__.'/app');
defined('OPEN_ROOT') or define('OPEN_ROOT',__DIR__.'/open');
/**
 * 加载类库
 */
session_start();
require UTF_ROOT.'/library/UsualToolLoad.php';
use library\UsualToolInc\UTInc;
use library\UsualToolTemp\UTTemp;
use library\UsualToolRoute\UTRoute;
/**
 * 读取配置
 */
$config=UTInc::GetConfig();
/**
 * 公共模块/模板配置
 */
define('PUB_PATH', APP_ROOT.'/modules/'.$config["DEFAULT_MOD"]);
define('PUB_TEMP', PUB_PATH.'/skin');
/**
 * 识别入口/路由
 */
if(pathinfo($_SERVER['SCRIPT_NAME'] ?? '',PATHINFO_FILENAME)=='plugin') return;
foreach(UTRoute::Analy(UTInc::CurPageUrl()) as $k=>$v) $_GET[$k]=$v;
/**
 * 模块/控制/依赖
 */
$m=UTInc::SqlCheck($_GET["m"] ?? $config["DEFAULT_MOD"]);
$p=UTInc::SqlCheck(str_replace(".php","",$_GET["p"] ?? $config["DEFAULT_PAGE"]));
$_modpath_=APP_ROOT."/modules/".$m;
UTInc::HasComposer($_modpath_);
/**
 * 入口节点/模板预设
 */
$_deve_=UTInc::Contain($config["DEVELOP"],UTInc::CurPageUrl()) || (isset($_GET["_control_"]) && $_GET["_control_"]=="admin");
$_form_=$_deve_ ? "admin" : "front";
$_work_=APP_ROOT."/template/".($_deve_ ? $config["TEMPADMIN"] : $config["TEMPFRONT"]);
$_node_=(($_deve_ && $config["TEMPADMIN"]!="0") || (!$_deve_ && $config["TEMPFRONT"]!="0")) ? $_work_ : $_modpath_;
$_skin_=$_node_.($_node_===$_work_ ? "/skin/".$m : "/skin");
$_cache_=$_node_."/cache";
/**
 * 本地化语言
 */
$_lang_=!empty($_COOKIE["lang"]) ? UTInc::SqlCheck($_COOKIE["lang"]) : $config["LANG"];
if(!isset($_COOKIE["lang"])){
    setcookie("lang",$_lang_);
    $_COOKIE["lang"]=$_lang_;
}
/**
 * 启动引擎/全局预设
 */
$app=new UTTemp($config["TEMPCACHE"],$_skin_."/".$_form_,$_cache_."/".$_form_);
$app->Runin(
    array(
        "appname",//应用名称
        "appurl",//应用地址
        "module",//当前模块
        "page",//当前页/方法
        "lang",//本地化语言列表
        "thelang",//当前语言
        "pubtemp",//模块化公共模板路径
        "template"//工程化模板路径
    ),array(
        $config["APPNAME"],
        $config["APPURL"],
        $m,
        $p,
        explode(",",$config["LANG_OPTION"]),
        $config["LANG"],
        PUB_TEMP."/".$_form_,
        $_work_."/skin/".$config["DEFAULT_MOD"]."/".$_form_
));