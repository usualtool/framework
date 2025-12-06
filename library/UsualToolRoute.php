<?php
namespace library\UsualToolRoute;
use library\UsualToolInc;
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
/**
 * 操作路由
 */
class UTRoute{
    /**
     * URL易错特殊参数转义
     * @param string $url
     * @return array
     */
    public static function ConverUrl($url){
        $o=array(
            "&times","&cent","&pound","&yen","&shy",
            "&thorn","&sect","&micro","&uml","&reg",
            "&laquo","&para","&acute","&deg","&eth",
            "&raquo","&copy","&aquo","&not","&curren");
        $n=array(
            "&amp;times","&amp;cent","&amp;pound","&amp;yen","&amp;shy",
            "&amp;thorn","&amp;sect","&amp;micro","&amp;uml","&amp;reg",
            "&amp;laquo","&amp;para","&amp;acute","&amp;deg","&amp;eth",
            "&amp;raquo","&amp;copy","&amp;aquo","&amp;not","&amp;curren");
        $url=str_replace($o,$n,$url);
        return $url;
    }
    /**
     * 解析路由
     * @param string $url
     * @return array
     */
    public static function Analy($url){
        $config=UsualToolInc\UTInc::GetConfig();
        $rule=$config["REWRITE"];
        if($rule==0){
            $url=$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"];
        }else{
            if(empty($url)){
                $url=UsualToolInc\UTInc::CurPageUrl();
            }else{
                $url=$url;
            }
        }
        $url=UTRoute::ConverUrl($url);
        $url=str_replace("//","/",str_replace("app/dev","",str_replace($config["APPURL"],"",$url)));
        $url=substr($url,1);
        $param=array();
        if(!UsualToolInc\UTInc::Contain("m=",$url) && !UsualToolInc\UTInc::Contain("p=",$url) && $rule==1){
            $urls=explode("/",$url);
            $param["m"]=$urls[0];
            $param["p"]=$urls[1];
            $q=explode(".",$urls[2])[0];
            $qs = explode('-',$q);
            for($i=0;$i<count($qs);$i++){
                if($i%2==0){
                    $param[$qs[$i]]=$qs[$i+1];
                }
            }
        }elseif(!UsualToolInc\UTInc::Contain("m=",$url) && !UsualToolInc\UTInc::Contain("p=",$url) && $rule==2){
            $urls=explode("/",$url);
            $param["m"]=$urls[0];
            $param["p"]=$urls[1];
            $qs=explode("/",explode(".",$url)[0]);
            for($i=2;$i<count($qs);$i++){
                if($i%2==0){
                    $param[$qs[$i]]=$qs[$i+1];
                }
            }
        }else{
            $urls=explode("?",$url);
            $surl=$urls[1];
            $qs= explode("&",$surl);
            for($i=0;$i<count($qs);$i++){
                $qx=explode("=",str_replace("amp;","",$qs[$i]));
                $param[$qx[0]]=$qx[1];
            }
        }
      return $param;
    }
    /**
     * 编制路由
     * Link("article","index","cid=1&page=1")
     * @param string $module
     * @param string $page
     * @param string $param
     * @return string
     */
    public static function Link($module="",$page="",$param=""){
        $config=UsualToolInc\UTInc::GetConfig();
        $appurl=rtrim($config["APPURL"],"/");
        $rule=$config["REWRITE"];
        if($rule==0){
            /**m={m}&p={p}&id={id}*/
            $m=empty($module) ? "" : "m=".$module."&";
            $p=empty($page) ? "" : "p=".$page."&";
            $r=empty($param) ? "" : $param."&";
            $link="/".substr("?".$m.$p.$r,0,-1);
        }elseif($rule==1){
            /**{m}/{p}/id-{id}.html*/
            $m=empty($module) ? "/".$config["DEFAULT_MOD"]."/" : "/".$module."/";
            $p=empty($page) ? $config["DEFAULT_PAGE"]."/" : $page."/";
            $r="";
            if(!empty($param)){
                foreach(UTRoute::UrlToArray($param) as $key=>$val){
                    $r.=$key."-".$val."-";
                }
            }
            if(empty($r)){
                $link=$m.$p."index.html";
            }else{
                $link=substr($m.$p.$r,0,-1).".html";
            }
        }elseif($rule==2){
            /**{m}/{p}/id/{id}.html*/
            $m=empty($module) ? "/".$config["DEFAULT_MOD"]."/" : "/".$module."/";
            $p=empty($page) ? $config["DEFAULT_PAGE"]."/" : $page."/";
            $r="";
            if(!empty($param)){
                foreach(UTRoute::UrlToArray($param) as $key=>$val){
                    $r.=$key."/".$val."/";
                }
            }
            if(empty($r)){
                $link=$m.$p;
            }else{
                $link=substr($m.$p.$r,0,-1).".html";
            }
        }
        $d=parse_url($config["APPURL"])["host"];
        $e=str_replace("/","",explode($d,$config["APPURL"])[1]);
        if(!empty($e)){
            return $appurl.$link;
        }else{
            return $link;
        }
    }
    /**
     * 解析URL
     * @param string $url
     * @return array
     */
    public static function UrlToArray($url){
      $query = explode('&',$url);
      $params = array();
      foreach ($query as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
      }
      return $params;
    }
    /**
     * 获取指定URL参数值
     * @param string $url
     * @param string $key
     * @return string
     */
    public static function GetUrlVal($url,$key){
        $res = '';
        $a = strpos($url,'?');
        if($a!==false){
            $str = substr($url,$a+1);
            $arr = explode('&',$str);
            foreach($arr as $k=>$v){
            $tmp = explode('=',$v);
                if(!empty($tmp[0]) && !empty($tmp[1])){
                    $barr[$tmp[0]] = $tmp[1];
                }
            }
        }
        if(!empty($barr[$key])){
            $res = $barr[$key];
        }
        return $res;
    }
}