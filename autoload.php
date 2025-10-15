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
 * 开启SESSION
 */
session_start();
/**
 * 类自动加载
 */
require UTF_ROOT.'/library/UsualToolLoad.php';
use library\UsualToolInc\UTInc;
use library\UsualToolTemp\UTTemp;
use library\UsualToolRoute\UTRoute;
/**
 * 加载配置
 */
$config=UTInc::GetConfig();
/**
 * 禁止配置联网
 */
if(!empty($config["APPURL"]) && UTInc::HttpCode($config["APPURL"]."/.ut.config")=="200"):
    UTInc::GoUrl("-1","Error:The configuration must be disconnected from the network.");
endif;
/**
 * 公共模块
 */
define('PUB_PATH', APP_ROOT.'/modules/'.$config["DEFAULT_MOD"]);
/**
 * 公共模板
 */
define('PUB_TEMP', PUB_PATH.'/skin');
/**
 * 识别路由
 */
foreach(UTRoute::Analy(UTInc::CurPageUrl()) as $key=>$val){
    $_GET[$key]=$val;
}
/**
 * 加载模块
 */
$m=empty($_GET["m"]) ? $config["DEFAULT_MOD"] : UTInc::SqlCheck($_GET["m"]);
/**
 * 加载页面
 */
$p=empty($_GET["p"]) ? $config["DEFAULT_PAGE"] : UTInc::SqlCheck(str_replace(".php","",$_GET["p"]));
/**
 * 当前模块
 */
$modpath=APP_ROOT."/modules/".$m;
/**
 * 模板节点
 */
$endpath=UTInc::TempEndPath();
/**
 * 配置模块化模板及模板工程模板
 */ 
$frontwork=APP_ROOT."/template/".$config["TEMPFRONT"];
$adminwork=APP_ROOT."/template/".$config["TEMPADMIN"];
$ismanage=UTInc::Contain(UTInc::GetConfig()["MANAGE"],UTInc::CurPageUrl());
/**
 * 开发端
 */
if($config["TEMPADMIN"]!='0' && $ismanage):
    $skin=$adminwork."/skin/".$m;
    $cache=$skin."/cache";
/**
 * 客户端
 */
elseif($config["TEMPFRONT"]!='0' && !$ismanage):
    $skin=$frontwork."/skin/".$m;
    $cache=$skin."/cache";
/**
 * 默认配置
 */
else:
    $skin=$modpath."/skin";
    $cache=$modpath."/cache";
endif;
$app=new UTTemp($config["TEMPCACHE"],$skin."/".$endpath,$cache."/".$endpath);
/**
 * 基础绑定
 */
$app->Runin(array("appname","appurl","module","page","editor"),array($config["APPNAME"],$config["APPURL"],$m,$p,$config["EDITOR"]));
/**
 * 语言配置
 */
$app->Runin(array("lang","thelang"),array(explode(",",$config["LANG_OPTION"]),$config["LANG"]));
if(!empty($_COOKIE['Language'])):
    $language=UTInc::SqlCheck($_COOKIE['Language']);
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