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
 * 框架根路径
 */
define('UTF_ROOT',__DIR__);
/**
 * 开发根路径
 */
define('APP_ROOT',__DIR__.'/app');
/**
 * 应用根路径
 */
define('OPEN_ROOT',__DIR__.'/open');
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
foreach(UTRoute::Analy(UTInc::CurPageUrl()) as $key=>$val):
    $_GET[$key]=$val;
endforeach;
/**
 * 加载模块及页面
 */
$m=UTInc::SqlCheck($_GET["m"] ?? $config["DEFAULT_MOD"]);
$p=UTInc::SqlCheck(str_replace(".php","",$_GET["p"] ?? $config["DEFAULT_PAGE"]));
/**
 * 当前模块
 */
$modpath=APP_ROOT."/modules/".$m;
/**
 * 模块依赖
 */
UTInc::HasComposer($modpath);
/**
 * 模板节点
 */
$endpath=UTInc::TempEndPath();
/**
 * 配置模板
 */
$frontwork=APP_ROOT."/template/".$config["TEMPFRONT"];
$adminwork=APP_ROOT."/template/".$config["TEMPADMIN"];
/**
 * 入端路径判断
 */
$develop=UTInc::Contain($config["MANAGE"],UTInc::CurPageUrl());
/**
 * 开发/管理端
 */
if($config["TEMPADMIN"]!='0' && $develop):
    $skin=$adminwork."/skin/".$m;
    $cache=$skin."/cache";
/**
 * 客户端
 */
elseif($config["TEMPFRONT"]!='0' && !$develop):
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
$app->Runin(
    array("appname","appurl","module","page"),
    array($config["APPNAME"],$config["APPURL"],$m,$p)
);
/**
 * 语言配置
 */
$app->Runin(
    array("lang","thelang"),
		array(explode(",",$config["LANG_OPTION"]),$config["LANG"])
);
if(!empty($_COOKIE["lang"])):
    $lang=UTInc::SqlCheck($_COOKIE["lang"]);
else:
    if($config["LANG"]=="big5"):
        $lang="zh";
        $speak="big5";
    else:
        $lang=$config["LANG"];
        $speak="";
    endif;
    setcookie("lang",$lang);
    setcookie("speak",$speak);
endif;