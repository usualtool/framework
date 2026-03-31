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
            "&raquo","&copy","&aquo","&not","&curren"
        );
        $n=array(
            "&amp;times","&amp;cent","&amp;pound","&amp;yen","&amp;shy",
            "&amp;thorn","&amp;sect","&amp;micro","&amp;uml","&amp;reg",
            "&amp;laquo","&amp;para","&amp;acute","&amp;deg","&amp;eth",
            "&amp;raquo","&amp;copy","&amp;aquo","&amp;not","&amp;curren"
        );
        $url=str_replace($o,$n,$url);
        return $url;
    }
    /**
     * 解析路由
     * @param string $url
     * @return array
     */
    public static function Analy($url = ''){
        $config = UsualToolInc\UTInc::GetConfig();
        $param = array();
        if(empty($url)) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            $path = parse_url($requestUri, PHP_URL_PATH);
            $scriptName = basename($_SERVER['SCRIPT_NAME']);
            $path = str_replace("/".$scriptName,"",$path);
            $path = trim($path,'/');
        }else{
            $url = self::ConverUrl($url);
            $parsedUrl = parse_url($url);
            $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : $url;
            if (!empty($config["APPURL"])) {
                $path = str_replace($config["APPURL"],"",$path);
            }
            $path = trim($path,'/');
        }
        $segment = explode('/', $path);
        $develop = trim($config["DEVELOP"] ?? '', '/');
        if(!empty($develop) && isset($segment[0]) && $segment[0]===$develop){
            return array();
        }
        $segments = array_values(array_filter(explode('/', $path)));
        $m = $config["DEFAULT_MOD"] ?? 'index';
        $p = $config["DEFAULT_PAGE"] ?? 'index';
        if (count($segments) >= 1) {
            $m = $segments[0];
            if (isset($segments[1])) {
                $p = $segments[1];
            }
        }
        $route = OPEN_ROOT.'/route.php';
        if (file_exists($route)) {
            $map = include $route;
            if (is_array($map) && isset($map[$m])) {
                $m = $map[$m];
            }
        }
        $param['m'] = $m;
        $param['p'] = $p;
        if (!empty($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'],$queryParams);
            $param = array_merge($param,$queryParams);
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
    public static function Link($module="", $page="", $param=""){
        $config = UsualToolInc\UTInc::GetConfig();
        $rewrite = $config["REWRITE"];
        $m = empty($module) ? $config["DEFAULT_MOD"] : $module;
        $p = empty($page) ? $config["DEFAULT_PAGE"] : $page;
        if($rewrite==0){
            $params = ['m'=>$m,'p'=>$p];
            if(!empty($param)){
                $extraParams = UTRoute::UrlToArray($param);
                if(is_array($extraParams)){
                    $params=array_merge($params, $extraParams);
                }
            }
            $link="?".http_build_query($params);
        }elseif($rewrite==1){
            $link="/{$m}/{$p}";
            if(!empty($param)){
                $link.="?".$param;
            }
        }
        return $link;
    }
    /**
     * 解析URL
     * @param string $url
     * @return array
     */
    public static function UrlToArray($url){
        $params = array();
        $query = explode('&', $url);
        foreach ($query as $param) {
            $item = explode('=', $param);
            if(isset($item[0]) && isset($item[1])){
                $params[$item[0]] = $item[1];
            } elseif(isset($item[0])) {
                $params[$item[0]] = '';
            }
        }
        return $params;
    }
    /**
     * 获取指定URL参数值
     * @param string $url
     * @param string $key
     * @return string
     */
     public static function GetUrlVal($url, $key){
        $res='';
        $a=strpos($url, '?');
        if($a!==false){
            $str=substr($url, $a + 1);
        }else{
            $str = $url;
        }
        $arr=explode('&', $str);
        foreach($arr as $v){
            $tmp = explode('=', $v);
            if(isset($tmp[0]) && isset($tmp[1]) && $tmp[0] == $key){
                $res = $tmp[1];
                break;
            }
        }
        return $res;
    }
}