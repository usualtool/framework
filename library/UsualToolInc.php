<?php
namespace library\UsualToolInc;
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
/**
 * 基础函数库
 */
class UTInc{
    /**
     * 获取UT配置
     * 其他格式注释(\/\/.(\s|.*))
     * @return array
     */
    public static function GetConfig(){
        $string = file_get_contents(UTF_ROOT."/.ut.config");
        $string = preg_replace("/(\/\*(\s|.)*?\*\/)|(#(\s*)?(.*))/",'',$string);
        $string = preg_replace('#\s+#',PHP_EOL,$string);
        $arr = explode(PHP_EOL,$string);
        $arr = array_filter($arr);
        $config = array();
        foreach($arr as $k=>$v){
            $arrs = explode('=',$v);
            $config[$arrs[0]] = trim($arrs[1]);
        } 
        return $config;
    }
    /**
     * 跳转链接
     * @param string $url 跳转地址,该参数为空时直接输出文本
     * @param string $text 跳转弹出文本，该参数为空时直接跳转地址
     * @return array
     */
    public static function GoUrl($url,$text=''){
        if(!empty($text)){
            if(!empty($url)){
                if(is_numeric($url)){
                    echo'<script>alert("'.$text.'");window.history.go('.$url.');</script>';
                    exit();
                }else{
                    echo'<script>alert("'.$text.'");window.location.href="'.$url.'"</script>';
                    exit();
                }
            }else{
                echo$text;
                exit();
            }
        }else{
            if(is_numeric($url) || $url===0):
                echo'<script>window.location.reload();</script>';
                exit();
            else:
                echo'<script>window.location.href="'.$url.'"</script>';
                exit();
            endif;
        }
    }
    /**
     * 严格过滤
     * @param string $str
     * @return string
     */
    public static function SqlCheck($str){
        $str=UTInc::SqlChecks($str);
        if(PHP_VERSION>=6 || !get_magic_quotes_gpc()):
            $str=addslashes($str);
        endif;
        $str=htmlspecialchars($str,ENT_QUOTES);
        return $str;
    }
    /**
     * 反解析严格过滤
     * @param string $str
     * @return string
     */
    public static function DeSqlCheck($str){
        $str=str_replace("’","'",$str);
        $str=str_replace('“','"',$str);
        $str=str_replace("（","(",$str);
        $str=str_replace("）",")",$str);
        if(PHP_VERSION>=6 || !get_magic_quotes_gpc()):
            $str=stripslashes($str);
        endif;
        $str=htmlspecialchars_decode($str);
        return $str;
    }
    /**
     * 危险符号转化
     * @param string $str
     * @return string
     */
    public static function SqlChecks($str){
        $str=str_replace("'","’",$str);
        $str=str_replace('"','“',$str);
        $str=str_replace("(","（",$str);
        $str=str_replace(")","）",$str);
        $str=str_replace("@","#",$str);
        $str=str_replace("/*","",$str);
        $str=str_replace("*/","",$str);
        return $str;
    }
    /**
     * 危险函数过滤
     * @param string $str
     * @return string
     */
    public static function SqlCheckv($str){
        $replace=array(
            "eval",
            "assert",
            "create_function",
            "call_user_func",
            "call_user_func_array",
            "array_map",
            "system",
            "shell_exec",
            "passthru",
            "exec",
            "popen",
            "proc_open",
            "ob_start",
            "putenv",
            "putenv",
            "ini_set",
            "preg_match"
        );
        $str=str_ireplace($replace,"",$str);
        return $str;
    }
    /**
     * 过滤并验证数字
     * @param string $str
     * @return int
     */
    public static function SqlCheckx($str){
        $result=false;
        if($str!=='' && !is_null($str)){
            $var=UTInc::SqlChecks($str);
            $var=str_replace(array("+","-","%","*"),array("","","",""),$var);
            if($var!=='' && !is_null($var) && (is_numeric($var) || is_float($var))){
                $result=$var;
            }else{
                $result=false;
            }
        }
        return $result;
    }
    /**
     * 清除字符中的数字
     * @param string $str
     * @return string
     */
    public static function ClearNum($str){
        $str=preg_replace('/[0-9]/','',$str);
        return $str;
    }
    /**
     * 清除Html脚本
     * @param string $str
     * @return string
     */
    public static function DeleteHtml($str){
        global$language;
        $str=htmlspecialchars_decode($str);
        $str = strip_tags($str,"");
        $str = str_replace(array("\r\n", "\r", "\n"), "", $str);   
        $str = str_replace("　","",$str);
        $str = str_replace("&nbsp;","",$str);
        if($language=="zh"):
            $str = str_replace(" ","",$str);
        endif;
        return ltrim(trim($str));
    }
    /**
     * 提取字符串中的所有图片
     * @param string $str
     * @return array
     */
    public static function FindImage($str){
        $pattern='/<img\s+[^>]*src=["\']([^"\']+)["\'][^>]*>/i';
        $matches=array();
        if(preg_match_all($pattern,$str,$matches)){
            return $matches[1];
        }else{
            return array();
        }
    }
    /**
     * 去除URL中的指定参数
     * @param string $param
     * @param string $url
     * @return string
     */
    public static function ClearParam($param,$url){
        if(is_string($param)) {
            $param = array($param);
        }
        $arr = array();
        if(strpos($url, "?") !== false) {
            $urlinfo = explode("?", $url);
            $baseurl = $urlinfo[1];
            parse_str($baseurl, $arr);
            $qurl=$urlinfo[0]."?";
        }else{
            parse_str($url, $arr);
            $qurl="";
        }
        foreach($arr as $name => $v) {
            if (in_array($name, $param)) {
                unset($arr[$name]);
            }
        }
        return $qurl.http_build_query($arr);
    }
    /**
     * 判断某个字符在字符串中是否存在
     * @param string $split 需要判断的字符
     * @param string|array $string 字符串或数组
     * @return bool
     */
    public static function Contain($split,$string){
        if(is_array($string)){
            if(in_array($split,$string))return true;
            else return false;
        }else{
            $tmpArr = explode($split,$string);
            if(count($tmpArr)>1)return true;
            else return false;
        }
    }
    /**
     * B数组是否包含A数组
     * @param array $a 
     * @param array $b 
     * @return bool
     */
    public static function InArray($a,$b){
        sort($a);
        sort($b);
        $same=array_intersect($a,$b);
        $str_s=implode(",",$same);
        $str_a=implode(",",$a);
        if($str_a==$str_s){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * 合并两个数组
     * @param array $a 
     * @param array $b 
     * @return array
     */
    public static function ArrayMerge(&$a,$b){
        foreach($a as $key=>&$val){
            if(is_array($val) && array_key_exists($key, $b) && is_array($b[$key])){
                UTInc::ArrayMerge($val,$b[$key]);
                $val = $val + $b[$key];
            }else if(is_array($val) || (array_key_exists($key, $b) && is_array($b[$key]))){
                $val = is_array($val)?$val:$b[$key];
            }
        }
        $a = $a + $b;
    } 
    /**
     * 检测模块和页面是否存在
     * @param array $page 模块文件名 
     * @param array $mod 模块名
     * @return bool
     */
    public static function ModSearch($page,$mod){
        $json=file_get_contents(APP_ROOT."/modules/module.config");
        $array=json_decode($json,true);
        if(array_key_exists($mod,$array)){
            $values=explode(",",$array[$mod]);
            if(in_array($page,$values)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * 删除模块集合中的某个模块
     * @param array $arr UT模块集合，modules/module.config
     * @param string $key 模块标识名称
     * @return array 返回新的UT模块集合
     */
    public static function DelModArray($arr,$key){
        if(!is_array($arr)){
            return $arr;
        }
        foreach($arr as $k=>$v){
            if($k == $key){
                unset($arr[$k]);
            }
        }
        return $arr;
    }
    /**
     * 获取模块集合
     * @return array
     */
    public static function GetMod($type=0){
        $mod=array();
        $config=UTInc::GetConfig();
        if(empty($type) || $type==0){
            $temp=scandir(APP_ROOT."/modules/");
            foreach($temp as $v){
                $file=APP_ROOT."/modules/".$v;
                if(is_file($file."/usualtool.config")){
                    $mods=file_get_contents($file."/usualtool.config");
                    $modtype=UTInc::StrSubstr("<modtype>","</modtype>",$mods);
                    if($modtype==2){
                        $mid=UTInc::StrSubstr("<id>","</id>",$mods);
                        $catid=UTInc::StrSubstr("<itemid>","</itemid>",$mods);
                        $title=UTInc::StrSubstr("<modname>","</modname>",$mods);
                        $auther=UTInc::StrSubstr("<auther>","</auther>",$mods);
                        $url=UTInc::StrSubstr("<modurl>","</modurl>",$mods);
                        $mod[]=array("mid"=>$mid,"catid"=>$catid,"title"=>$title,"auther"=>$auther,"url"=>$url);
                    }
                }
            }
        }elseif($type==1){
            $modules=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"module");
            preg_match_all("/\<module\>(.*?)\<\/module\>/s",$modules,$moduleblocks);
            foreach($moduleblocks[1] as $module){
                preg_match_all("/\<mid\>(.*?)\<\/mid\>/",$module,$mid);
                preg_match_all("/\<catid\>(.*?)\<\/catid\>/",$module,$catid);
                preg_match_all("/\<title\>(.*?)\<\/title\>/",$module,$title);
                preg_match_all("/\<isfree\>(.*?)\<\/isfree\>/",$module,$isfree);
                $mod[]=array("mid"=>$mid[1][0],"catid"=>$catid[1][0],"title"=>$title[1][0],"isfree"=>$isfree[1][0]);
            }
        }elseif($type==2){
            $modules=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"moduleorder");
            preg_match_all("/\<module\>(.*?)\<\/module\>/s",$modules,$moduleblocks);
            foreach($moduleblocks[1] as $module){
                preg_match_all("/\<moduleid\>(.*?)\<\/moduleid\>/",$module,$moduleid); 
                preg_match_all("/\<orderid\>(.*?)\<\/orderid\>/",$module,$orderid);  
                preg_match_all("/\<mid\>(.*?)\<\/mid\>/",$module,$mid);  
                preg_match_all("/\<title\>(.*?)\<\/title\>/",$module,$title);
                preg_match_all("/\<ordertime\>(.*?)\<\/ordertime\>/",$module,$ordertime);
                $mod[]=array("moduleid"=>$moduleid[1][0],"orderid"=>$orderid[1][0],"mid"=>$mid[1][0],"title"=>$title[1][0],"ordertime"=>$ordertime[1][0]);
            }
        }
        return $mod;
    }
    /**
     * 获取插件集合
     * @return array
     */
    public static function GetPlugin($type=0){
        $plugin=array();
        $config=UTInc::GetConfig();
        if(empty($type) || $type==0){
            $temp=scandir(APP_ROOT."/plugins/");
            foreach($temp as $v){
                $file=APP_ROOT."/plugins/".$v;
                if(is_file($file."/usualtool.config")){
                    $plugins=file_get_contents($file."/usualtool.config");
                    $plugintype=UTInc::StrSubstr("<plugintype>","</plugintype>",$plugins);
                    if($plugintype==2){
                        $pid=UTInc::StrSubstr("<id>","</id>",$plugins);
                        $title=UTInc::StrSubstr("<pluginname>","</pluginname>",$plugins);
                        $auther=UTInc::StrSubstr("<auther>","</auther>",$plugins);
                        $description=UTInc::StrSubstr("<description>","</description>",$plugins);
                        $plugin[]=array("pid"=>$pid,"title"=>$title,"auther"=>$auther,"description"=>$description);
                    }
                }
            }
        }elseif($type==1){
            $hooks=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"plugin");
            preg_match_all( "/\<hook\>(.*?)\<\/hook\>/s",$hooks,$hookblocks);
            foreach($hookblocks[1] as $hook){
                preg_match_all("/\<pid\>(.*?)\<\/pid\>/",$hook,$pid);  
                preg_match_all("/\<type\>(.*?)\<\/type\>/",$hook,$type);  
                preg_match_all("/\<title\>(.*?)\<\/title\>/",$hook,$title);
                preg_match_all("/\<picurl\>(.*?)\<\/picurl\>/",$hook,$picurl);
                preg_match_all("/\<ver\>(.*?)\<\/ver\>/",$hook,$ver);
                preg_match_all("/\<isfree\>(.*?)\<\/isfree\>/",$hook,$isfree);
                $plugin[]=array("pid"=>$pid[1][0],"type"=>$type[1][0],"title"=>$title[1][0],"picurl"=>$picurl[1][0],"ver"=>$ver[1][0],"isfree"=>$isfree[1][0]);
            }
        }elseif($type==2){
            $hooks=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"pluginorder");
            preg_match_all("/\<hook\>(.*?)\<\/hook\>/s",$hooks,$hookblocks);
            foreach($hookblocks[1] as $hook){
                preg_match_all("/\<hookid\>(.*?)\<\/hookid\>/",$hook,$hookid);  
                preg_match_all("/\<orderid\>(.*?)\<\/orderid\>/",$hook,$orderid);  
                preg_match_all("/\<pid\>(.*?)\<\/pid\>/",$hook,$pid);  
                preg_match_all("/\<title\>(.*?)\<\/title\>/",$hook,$title);
                preg_match_all("/\<ordertime\>(.*?)\<\/ordertime\>/",$hook,$ordertime);
                $plugin[]=array("hookid"=>$hookid[1][0],"orderid"=>$orderid[1][0],"pid"=>$pid[1][0],"title"=>$title[1][0],"ordertime"=>$ordertime[1][0]);
            }
        }
        return $plugin;
    }
    /**
     * 获取模板工程集合
     * @return array
     */
    public static function GetTemplate($type=0){
        $template=array();
        $config=UTInc::GetConfig();
        if(empty($type) || $type==0){
            $temp=scandir(APP_ROOT."/formwork/");
            foreach($temp as $v){
                $file=APP_ROOT."/formwork/".$v;
                if(is_file($file."/usualtool.config")){
                    $temps=file_get_contents($file."/usualtool.config");
                    $temptype=UTInc::StrSubstr("<temptype>","</temptype>",$temps);
                    if($temptype==2){
                        $tid=UTInc::StrSubstr("<id>","</id>",$temps);
                        $title=UTInc::StrSubstr("<title>","</title>",$temps);
                        $auther=UTInc::StrSubstr("<auther>","</auther>",$temps);
                        $description=UTInc::StrSubstr("<description>","</description>",$temps);
                        $template[]=array("tid"=>$tid,"title"=>$title,"auther"=>$auther,"description"=>$description);
                    }
                }
            }
        }elseif($type==1){
            $temps=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"temp");
            preg_match_all( "/\<temp\>(.*?)\<\/temp\>/s",$temps,$tempblocks);
            foreach($tempblocks[1] as $temp){
                preg_match_all("/\<tid\>(.*?)\<\/tid\>/",$temp,$tid);
                preg_match_all("/\<title\>(.*?)\<\/title\>/",$temp,$title);
                preg_match_all("/\<picurl\>(.*?)\<\/picurl\>/",$temp,$picurl);
                preg_match_all("/\<module\>(.*?)\<\/module\>/",$temp,$module);
                preg_match_all("/\<lang\>(.*?)\<\/lang\>/",$temp,$lang);
                preg_match_all("/\<isfree\>(.*?)\<\/isfree\>/",$temp,$isfree);
                $template[]=array("tid"=>$tid[1][0],"title"=>$title[1][0],"picurl"=>$picurl[1][0],"module"=>$module[1][0],"lang"=>$lang[1][0],"isfree"=>$isfree[1][0]);
            }
        }elseif($type==2){
            $temps=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"temporder");
            preg_match_all("/\<temp\>(.*?)\<\/temp\>/s",$temps,$tempblocks);
            foreach($tempblocks[1] as $temp){
                preg_match_all("/\<orderid\>(.*?)\<\/orderid\>/",$temp,$orderid);  
                preg_match_all("/\<tempid\>(.*?)\<\/tempid\>/",$temp,$tid);  
                preg_match_all("/\<title\>(.*?)\<\/title\>/",$temp,$title);
                preg_match_all("/\<ordertime\>(.*?)\<\/ordertime\>/",$temp,$ordertime);
                $template[]=array("orderid"=>$orderid[1][0],"tid"=>$tid[1][0],"title"=>$title[1][0],"ordertime"=>$ordertime[1][0]);
            }
        }
        return $template;
    }
    /**
     * 是否安装UT可视化包
     * @return bool
     */
    public static function InstallDev(){
        if(is_dir('install-dev')){
            if(file_exists(UTF_ROOT."/install-dev/usualtool.lock")){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    /**
     * 引用UT插件
     * @param string $pluginname 插件标识
     * @param string $pluginroot 引用插件页面
     * @return string include_once引用页面或者iframe调用
     */
    public static function Plugin($pluginname,$pluginroot='index.php'){
        $config=UTInc::GetConfig();
        if(is_dir(APP_ROOT."/plugins/".$pluginname."")):
            if(empty($pluginroot)||UTInc::Contain(".php",$pluginroot)):
                include_once(APP_ROOT."/plugins/".$pluginname."/".$pluginroot."");
            else:
                $getpost="<iframe src=".$config["APPURL"]."/app/plugins/".$pluginname."/".$pluginroot." frameborder=0 id=external-frame></iframe><style>iframe{width:100%;margin:0 0 1em;border:0;}</style><script src=images/js/autoheight.js></script>";
                echo$getpost;
            endif;
        endif;
    }
    /**
     * UT令牌验证
     * @param string $authcode UT令牌
     * @param string $authurl 验证通讯地址
     * @param string $apitype API接口类型
     * @return string 获得一个XML文件
     */
    public static function Auth($authcode,$authurl,$apitype){
        $fromurl=UTInc::CurPageUrl();
        $url="".$authurl."?AuthCode=".$authcode."&FromUrl=".$fromurl."&Type=".$apitype."";
        $content=UTInc::HttpGet($url);
        return str_replace("#","$",str_replace("&","=",UTInc::StrSubstr("<php>","</php>",$content)));
    }
    /**
     * 生成随机字符串
     * @param string $length 长度
     * @param string $chars 随机因子
     * @return string
     */
    public static function GetRandomString($length,$chars=null){
        if (is_null($chars)){
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }  
        mt_srand(10000000*(double)microtime());
        for($i = 0, $str = '', $lc = strlen($chars)-1; $i < $length; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
    /**
     * 获取IP地址
     * @return string
     */
    public static function GetIp(){
        $unknown = 'unknown';
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if(false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
            return $ip;
    }
    /**
     * 判断是否为移动端
     * @return bool
     */
    public static function IsApp(){
        if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        if(isset($_SERVER['HTTP_VIA'])){
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $keywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','micromessenger','miuibrowser'); 
            if(preg_match("/(".implode('|', $keywords).")/i",strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        if(isset ($_SERVER['HTTP_ACCEPT'])) {
            if((strpos($_SERVER['HTTP_ACCEPT'],'vnd.wap.wml')!== false) && (strpos($_SERVER['HTTP_ACCEPT'],'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
            return true;
            } 
        } 
        return false;
    }
    /**
     * 字节单位格式化
     * @param string $size 字节长度
     */
    public static function ForBytes($size) { 
        $units = array('B','KB','MB','GB','TB'); 
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
        return round($size, 0).$units[$i]; 
    }
    /**
     * 在指定位置截取字符串
     * @param string $str 字符串
     * @param int $start 开始位置
     * @param int $length 长度
     * @param string $charset 字符编码
     * @param bool $suffix 是否加省略号
     * @return string
     */
    public static function CutSubstr($str,$start=0,$length=0,$charset="utf-8",$suffix=true){
        if(function_exists("mb_substr"))
            return mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            return iconv_substr($str,$start,$length,$charset);
        }
        $re['utf-8']   = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        if($suffix) return $slice."…";
        return $slice;
    }
    /**
     * 取年月日
     * @param date $thisdate 日期
     * @param int $type 1取年份，2取月份，3取日期
     * @return int
     */
    public static function OpenDate($thisdate,$type){
        if($type==1):
            return date('y',$thisdate);
        elseif($type==2):
            return date('m',$thisdate);
        elseif($type==3):
            return date('d',$thisdate);
        endif;
    }
    /**
     * 截取字符串中的中文字符
     * @param string $string字符串
     * @param int $length长度
     * @return string
     */
    public static function CnSubStr($string,$length='0'){
        preg_match_all("#[\x{4e00}-\x{9fa5}]#u",$string,$match);
        if($length==0):
            return implode("",$match[0]);
        else:
            return mb_substr(implode("",$match[0]),0,$length);
        endif;
    }
    /**
     * preg方式截取字符串
     * @param string $start 开始字符
     * @param string $end 结束字符
     * @param string $str 字符串
     */
    public static function PregSubstr($start,$end,$str){
        $temp = preg_split($start, $str);
        $content = preg_split($end, $temp[1]);
        return $content[0];
    }
    /**
     * explode方式截取字符串
     * @param string $start 开始字符
     * @param string $end 结束字符
     * @param string $str 字符串
     */
    public static function StrSubstr($start,$end,$str){
        $temp = explode($start, $str, 2);
        $content = explode($end, $temp[1], 2);
        return $content[0];
    }
    /**
     * 按照指定字符截取字符串
     * @param string $str 字符串
     * @param string $key 被指定的字符
     * @param int $len 长度
     * @param string $enc 编码
     */
    public static function SubKey($str,$key,$len=100,$enc='utf-8'){
        $strlen = mb_strlen($str,$enc);
        $keylen = mb_strlen($key,$enc);
        $keypos = mb_strpos($str,$key,0,$enc);
        $leftpos = $keypos - 1;
        $rightpos = $keypos + $keylen;
        if($keylen > $len){
            return "<font color=red>".mb_substr($key,0,$len,$enc)."</font>...";
        }
        $result = "<font color=red>".$key."</font>";
        for($i = 0;$i<$len - $keylen;$i++){
            if($leftpos >= 0){
                $result = mb_substr($str,$leftpos--,1,$enc).$result;
            }else{
                $result .= mb_substr($str,$rightpos++,1,$enc);
            }
        }
        if($leftpos >= 0){
            $result = "...".$result;
        }
        if($rightpos < $strlen){
            $result .= "...";
        }
        return $result;
    }
    /**
     * POST方式提交数据
     * @param string $url 提交地址
     * @param string $data 数据
     * @param int $gzip 默认为0，返回数据是否进行gzip解压
     */
    public static function HttpPost($url,$data,$gzip='0'){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if(!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        if($gzip):
            curl_setopt($curl, CURLOPT_ENCODING, "gzip" );
        endif;
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * GET方式提交数据
     * @param string $url 提交地址
     * @param int $gzip 默认为0，返回数据是否进行gzip解压
     */
    public static function HttpGet($url,$gzip='0'){
        if(function_exists("curl_init")){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 90);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            if($gzip):
                curl_setopt($curl, CURLOPT_ENCODING, "gzip" );
            endif;
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        }else{
            $output=file_get_contents($url);
            return mb_convert_encoding($output, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        }
    }
    /**
     * 获取指定地址网络状态
     * @param string $url 地址
     * @return int 获得地址状态码
     */
    public static function HttpCode($url){
        $ch = curl_init();
        $timeout =10;
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_exec($ch);
        return curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    /**
     * 获取当前页面地址
     */
    public static function CurPageUrl(){
        $pageURL = 'http';
        if(isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"])=="on"){
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        return $pageURL;
    }
    /**
     * 获取模板末尾节点路径，通过判断当前路径是否在app目录打开
     * @return string admin即后端，front即前端
     */
    public static function TempEndPath(){
        $thispage=UTInc::CurPageUrl();
        if(strpos($thispage,'/app/')!==false){
            return "admin/";
        }else{
            return "front/";
        }
    }    
    /**
     * 保存远程文件
     * @param string $url 远程文件地址
     * @param string $save_dir 保存目录
     * @param string $filename 保存文件名
     * @param int $type 默认为0 readfile数据流方式获取，否则为curl
     * @return array 返回文件信息集合
     */
    public static function SaveFile($url,$save_dir='',$filename='',$type=0){  
        if(trim($url) == ''){
            return false;
        }  
        if(trim($save_dir) == ''){
            $save_dir = './';
        }  
        if(0 !== strrpos($save_dir, '/')){
            $save_dir.= '/';
        }   
        if(!file_exists($save_dir) && !mkdir($save_dir, 0777, true)){
            return false;
        }    
        if($type){  
            $ch = curl_init();  
            $timeout = 5;  
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
            $content = curl_exec($ch);  
            curl_close($ch);  
        } else {  
            ob_start();  
            readfile($url);  
            $content = ob_get_contents();  
            ob_end_clean();  
        }   
        $size = strlen($content);
        $fp2 = @fopen($save_dir . $filename, 'a');  
        fwrite($fp2, $content);  
        fclose($fp2);  
        unset($content, $url);  
        return array(  
        'file_name' => $filename,  
        'save_path' => $save_dir . $filename,  
        'file_size' => $size  
        );  
    } 
    /**
     * 检测文件夹权限
     * @param string $path 文件夹路径
     * @return bool
     */
    public static function FileMode($path){
        $result=is_writable($path);
        return $result;
    }
    /**
     * 文件夹列表
     * @param string $dir 文件夹路径名称
     * @param int $mode 模式 0当前 1递归
     * @return array
     */
    public static function DirList($dir,$mode='0'){
        $list=array();
        if($dir_handle = @opendir($dir)){
            while($filename = readdir($dir_handle)){
                if($filename != "." && $filename != ".."){
                    $subFile = $dir."/".$filename;
                    if(is_dir($subFile)){
                        $list[]=$filename;
                        if($mod!=0):
                            DirList($subFile);
                        endif;
                    }
                }
            }
            closedir($dir_handle);
        }
        return $list;
    }
    /**
     * 创建文件夹
     * @param string $dir 文件夹路径名称
     * @param int $mode 文件夹权限
     * @return bool
     */
    public static function MakeDir($dir,$mode=0777){
        if(is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if(!mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }
    /**
     * 移动文件夹
     * @param string $oldpath 原来的文件夹路径
     * @param string $newpath 现在的文件夹路径
     */
    public static function MoveDir($oldpath,$newpath){
        $handle=opendir($oldpath);
        while(false!==($file = readdir($handle))){
            $fileFrom=$oldpath.DIRECTORY_SEPARATOR.$file;
            $fileTo=$newpath.DIRECTORY_SEPARATOR.$file;
                if($file=='.' || $file=='..'){
                    continue;
                }
                if(is_dir($fileFrom)){
                    @mkdir($fileTo,0777);
                    UTInc::MoveDir($fileFrom,$fileTo);
                }else{
                    @copy($fileFrom,$fileTo);
                }
        }
    }
    /**
     * 编辑文件夹名称
     * @param string $oldpath 原来的文件夹路径
     * @param string $newpath 现在的文件夹路径
     * @return bool
     */
    public static function EditDir($oldpath,$newpath){
        $_path = iconv('utf-8', 'gb2312', $oldpath);
        $__path = iconv('utf-8', 'gb2312',$newpath);
        if(is_dir($_path)){
            if(file_exists($__path)==false){
                if (rename($_path, $__path)){
                    return true;
                }else{
                 return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * 获取文件夹中文件列表
     * @param string $path 文件夹路径
     * @return array
     */
    public static function GetDir($path){
        if(!is_dir($path)){
              return false;
        }
        $arr = array();
        $data = scandir($path);
        foreach ($data as $value){
            if($value != '.' && $value != '..'){
                $filetime = date('Y-m-d H:i:s',filemtime($path."/".$value));
                $arr[$filetime] = $value;
            }
        }
        return $arr;
    }
    /**
     * 删除文件
     * @param string $file 文件路径名称
     * @return bool
     */
    public static function UnlinkFile($file){
        if (file_exists($file)){
            unlink($file);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 删除文件夹
     * @param string $directory 文件夹路径名称
     * @return bool
     */
    public static function DelDir($directory){
        if(file_exists($directory)){
            if($dir_handle = @opendir($directory)){
                while($filename = readdir($dir_handle)){
                    if($filename != "."&& $filename != ".."){
                        $subFile = $directory."/".$filename;
                        if(is_dir($subFile))
                        UTInc::DelDir($subFile);
                        if(is_file($subFile)) 
                        unlink($subFile);
                    }
                }
                closedir($dir_handle);
                rmdir($directory);
                return 1;
            }
        }
    }
    /**
     * 查询文件夹是否存在
     * @param string $dir 文件夹路径名称
     * @return bool
     */
    public static function SearchDir($dir){
        if(is_dir($dir)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 查询文件是否存在
     * @param string $file 文件夹路径名称
     * @return bool
     */
    public static function SearchFile($file){
        if(is_file($file)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取服务器信息
     * @return array
     */
    public static function GetSystemInfo(){
        $os=PHP_OS;
        $server = $_SERVER["SERVER_SOFTWARE"];
        $phpver = PHP_VERSION;
        $memory=round(memory_get_peak_usage()/1024, 2).'KB';
        $SystemInfo=array("OS"=>$os,"SERVER"=>$server,"PHP"=>$phpver,"MEMORY"=>$memory);
         return $SystemInfo;
    }
    /**
     * 检测文件编码
     * @param string $file 文件
     */
    public static function DetectEncoding($file) {
        $list = array('GBK', 'UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1');
        $str = file_get_contents($file);
        foreach ($list as $item) {
            $tmp = mb_convert_encoding($str, $item, $item);
            if (md5($tmp) == md5($str)) {
                return $item;
            }
        }
        return null;
    }
    /**
     * 图片转为Base64编码
     * @param string $img 图片
     */
    public static function ImgToBase64($img=''){
        $imageInfo = getimagesize($img);
        $base64 = "" . chunk_split(base64_encode(file_get_contents($img)));
        return 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode(file_get_contents($img)));
    }
    /**
     * Base64编码转为图片
     * @param string $base64 图片Base64编码
     * @param string $path 保存路径
     */
    public static function Base64ToImg($base64,$path){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];
            $newfile = $path."/".time().".{$type}";
            if (file_put_contents($newfile, base64_decode(str_replace($result[1], '', $base64)))){
                return str_replace("../","",$newfile);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * 向浏览器推送下载文件
     * @param string $file 文件路径
     */
    public static function Download($file){
        $filename=iconv('utf-8','gb2312',basename($file));
        if(!file_exists($file)){
            Header("HTTP/1.1 404 Not Found");
            exit();
        }else{
            $thefile=fopen($file,"r");
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: ".filesize($file));
            Header("Content-Disposition: attachment; filename=".UTInc::GetRandomString(8).".".substr(strrchr($filename,'.'),1));
            echo fread($thefile,filesize($file));
            fclose($thefile);
            exit();
        }
    }
    /**
     * each7.x版本替代方法
     * @param array $array 数组
     */    
    public static function NewEach(&$array){
        $res = array();
        $key = key($array);
        if($key !== null){
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        }else{
            $res = false;
        }
        return $res;
    }
    /**
     * 获取客户端操作系统
     */    
    function GetOs(){
        $os=$_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/win/i',$os)){
            $os='Windows';
        }elseif(preg_match('/mac/i',$os)){
            $os='Mac';
        }elseif(preg_match('/Android/i',$os)){
            $os='Android';
        }elseif(preg_match('/iPhone/i',$os)){
            $os='IPhone';
        }elseif(preg_match('/iPad/i',$os)){
            $os='IPad';
        }elseif(preg_match('/linux/i',$os)){
            $os='Linux';
        }elseif(preg_match('/unix/i',$os)){
            $os='Unix';
        }elseif(preg_match('/bsd/i',$os)){
            $os='BSD';
        }else{
            $os='Other';
        }
        return $os;
    }
    /**
     * 获取客户端浏览器
     */   
    function GetBrowser(){
        $browser=$_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/MSIE/i',$browser)){
            $browser='MSIE';
        }elseif(preg_match('/Firefox/i',$browser)){
            $browser='Firefox';
        }elseif(preg_match('/MicroMessenger/i',$browser)){
            $browser='Wechat';
        }elseif(preg_match('/QQ/i',$browser)){
            $browser='Tencent';
        }elseif(preg_match('/Safari/i',$browser)){
            $browser='Safari';
        }elseif(preg_match('/Opera/i',$browser)){
            $browser='Opera';
        }elseif(preg_match('/Chrome/i',$browser)){
            $browser='Chrome';
        }else{
            $browser='Other';
        }
        return $browser;
    }
}
