<?php
namespace library\UsualToolSpider;
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
class UTSpider{
    protected $http_data = array();
    protected $agent;
    protected $cookies;
    protected $referer;
    protected $ip;
    protected $header = array();
    protected $_option = array();
    protected $_post_data = array();
    //多列队任务进程数，0表示不限制
    protected $multi_exec_num = 100;
    const ERROR_HOST = 'NULL';
    const ERROR_GET = 'NULL';
    const ERROR_POST = 'NULL';
    function __construct(){}
    //设置$cookie
    public function SetAgent($agent){
        $this->agent = $agent;
        return $this;
    }
    public function SetCookies($cookies){
        $this->cookies = $cookies;
        return $this;
    }
    public function SetReferer($referer){
        $this->referer = $referer;
        return $this;
    }
    public function SetIp($ip){
        $this->ip = $ip;
        return $this;
    }
    // 设置curl参数
    public function SetOption($key, $value){
        if ( $key===CURLOPT_HTTPHEADER ){
            $this->header = array_merge($this->header,$value);
        }else{
            $this->_option[$key] = $value;
        }
        return $this;
    }
    //设置多个列队默认排队数上限
    public function SetMultiMaxNum($num=0){
        $this->multi_exec_num = (int)$num;
        return $this;
    }
    //用POST方式提交，支持多个URL
    public function Post($url, $vars, $timeout = 60){
        # POST模式
        $this->SetOption( CURLOPT_HTTPHEADER, array('Accept-Language: zh-CN') );
        $this->SetOption( CURLOPT_POST, true );
        if (is_array($url)){
            $myvars = array();
            foreach ($url as $k=>$url){
                if (isset($vars[$k])){
                    if (is_array($vars[$k])){
                        $myvars[$url] = http_build_query($vars[$k]);
                    }else{
                        $myvars[$url] = $vars[$k];
                    }
                }
            }
        }else{
        $myvars = array($url=>$vars);
        }
        $this->_post_data = $myvars;
        return $this->Get($url,$timeout);
    }
    //GET方式获取数据，支持多个URL
    public function Get($url, $timeout = 30){
        if ( is_array($url) ){
            $getone = false;
            $urls = $url;
        }else{
            $getone = true;
            $urls = array($url);
        }
        $data = $this->RequestUrls($urls, $timeout);
        $this->ClearSet();
        if ( $getone ){
            $this->http_data = $this->http_data[$url];
            $encode = mb_detect_encoding($data[$url], array('GB2312','GBK','UTF-8'));
            if($encode=="GB2312"){$datas = iconv("GBK","UTF-8",$data[$url]);}
            else if($encode=="GBK"){$datas = iconv("GBK","UTF-8",$data[$url]);}
            else if($encode=="EUC-CN"){$datas = iconv("GBK","UTF-8",$data[$url]);}
            else{$datas =$data[$url];}
            return $datas;
        }else{
            return $data;
        }
    }
    //创建一个CURL对象
    public function _create($url,$timeout){
        if ( false===strpos($url, '://') ){
            preg_match('#^(http(?:s)?\://[^/]+/)#', $_SERVER["SCRIPT_URI"] , $m);
            $the_url = $m[1].ltrim($url,'/');
        }else{
            $the_url = $url;
        } 
        if ($this->ip){
            # 如果设置了IP，则把URL替换，然后设置Host的头即可
            if ( preg_match('#^(http(?:s)?)\://([^/\:]+)(\:[0-9]+)?/#', $the_url.'/',$m) ){
                $this->header[] = 'Host: '.$m[2];
                $the_url = $m[1].'://'.$this->ip.$m[3].'/'.substr($the_url,strlen($m[0]));
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $the_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        if ( preg_match('#^https://#i', $the_url) ){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        if ( $this->cookies ){
            curl_setopt($ch, CURLOPT_COOKIE, http_build_query($this->cookies, '', ';'));
        }
        if ( $this->referer ){
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }
        if ( $this->agent ){
            curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
        }
        elseif ( array_key_exists('HTTP_USER_AGENT', $_SERVER) ){
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        foreach ( $this->_option as $k => $v ){
            curl_setopt($ch, $k, $v);
        }
        if ( $this->header ){
            $header = array();
            foreach ($this->header as $item){
            # 防止有重复的header
            if (preg_match('#(^[^:]*):.*$#', $item,$m)){
                $header[$m[1]] = $item;
            }
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_values($header));
        }
        # 设置POST数据
        if (isset($this->_post_data[$the_url])){
            curl_setopt($ch , CURLOPT_POSTFIELDS , $this->_post_data[$the_url]);
        }
        return $ch;
    }
    //支持多线程获取网页
    protected function RequestUrls($urls, $timeout = 10){
        # 去重
        $urls = array_unique($urls);
        if (!$urls)return array();
        $mh = curl_multi_init();
        # 监听列表
        $listener_list = array();
        # 返回值
        $result = array();
        # 总列队数
        $list_num = 0;
        # 排队列表
        $multi_list = array();
        foreach ( $urls as $url ){
        # 创建一个curl对象
        $current = $this->_create($url, $timeout);
        if ( $this->multi_exec_num>0 && $list_num>=$this->multi_exec_num ){
            # 加入排队列表
            $multi_list[] = $url;
        }else{
            # 列队数控制
            curl_multi_add_handle($mh, $current);
            $listener_list[$url] = $current;
            $list_num++;
        }
        $result[$url] = null;
        $this->http_data[$url] = null;
        }
        unset($current);
        $running = null;
        # 已完成数
        $done_num = 0; 
        do{
            while ( ($execrun = curl_multi_exec($mh, $running)) == CURLM_CALL_MULTI_PERFORM );
            if ( $execrun != CURLM_OK ) break;
            while ( true==($done = curl_multi_info_read($mh)) ){
                foreach ( $listener_list as $done_url=>$listener ){
                    if ( $listener === $done['handle'] ){
                        # 获取内容
                        $this->http_data[$done_url] = $this->GetData(curl_multi_getcontent($done['handle']), $done['handle']);
                        if ( $this->http_data[$done_url]['code'] != 200 ){
                            $result[$done_url] = false;
                        }else{
                        # 返回内容
                            $result[$done_url] = $this->http_data[$done_url]['data']; 
                        }
                        curl_close($done['handle']);
                        curl_multi_remove_handle($mh, $done['handle']);
                        # 把监听列表里移除
                        unset($listener_list[$done_url],$listener);
                        $done_num++;
                        # 如果还有排队列表，则继续加入
                        if ( $multi_list ){
                            # 获取列队中的一条URL
                            $current_url = array_shift($multi_list);
                            # 创建CURL对象
                            $current = $this->_create($current_url, $timeout);
                            # 加入到列队
                            curl_multi_add_handle($mh, $current);
                            # 更新监听列队信息
                            $listener_list[$current_url] = $current;
                            unset($current);
                            # 更新列队数
                            $list_num++;
                        } 
                        break;
                    }
                }
            }
            if ($done_num>=$list_num)break;
        } while (true);
        # 关闭列队
        curl_multi_close($mh);
        return $result;
    }
    public function GetResultData(){
        return $this->http_data;
    } 
    protected function GetData($data, $ch){
        $header_size  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['code']   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['data']   = substr($data, $header_size);
        $result['header'] = explode("\r\n", substr($data, 0, $header_size));
        $result['time']   = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        return $result;
    }
    protected function ClearSet(){
        $this->_option = array();
        $this->header = array();
        $this->ip = null;
        $this->cookies = null;
        $this->referer = null;
        $this->_post_data = array();
    }
}