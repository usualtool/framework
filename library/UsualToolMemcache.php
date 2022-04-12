<?php
namespace library\UsualToolMemcache;
use library\UsualToolInc;
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
 * 以静态方法操作Memcache
 */
class UTMemcache{
    public static function GetMemcache(){
        $config=UsualToolInc\UTInc::GetConfig();
        $db=new \Memcached();
        $db->setOption(\Memcached::OPT_BINARY_PROTOCOL,true);
        if(strpos($config["MEMCACHE_HOST"],",")!==false){
            $server=array();
            $hosts=explode(',',$config["MEMCACHE_HOST"]);
            $ports=explode(',',$config["MEMCACHE_PORT"]);
            $you=round(1/count($hosts)*100);
            for($i=0;$i<count($hosts);$i++){
                $server[]=array($hosts[$i],$ports[$i],$you);
            }
            $db->addServers($server);
        }else{
            $db->addServer($config["MEMCACHE_HOST"],$config["MEMCACHE_PORT"]);
        }
        if(!empty($config["MEMCACHE_USER"]) && !empty($config["MEMCACHE_PASS"])){
            $db->setSaslAuthData($config["MEMCACHE_USER"],$config["MEMCACHE_PASS"]);
        }
        return $db;
    }
    /**
     * 判断元素是否存在
     * @param string $key 键
     * @return bool
     */
    public static function ModTable($key){
        $db=UTMemcache::GetMemcache();
        $res=$db->get($key);
        if(!$res){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 查询元素
     * @param string $key 键
     * @return bool/string/array
     */
    public static function QueryData($key){
        $db=UTMemcache::GetMemcache();
        $msg=$db->get($key);
        if(!$msg){
            return false;
        }
        return $msg;
    }
    /**
     * 创建元素
     * @param string $key 键
     * @param string|array $data 值
     * @param int $time 过期时间，0不设置过期时间，1设置过期时间为DBCACHE_TIME
     * @return bool
     */
    public static function InsertData($key,$data,$time='0'){
        $config=UsualToolInc\UTInc::GetConfig();
        $db=UTMemcache::GetMemcache();
        if($time==0){
            return $db->set($key,$data,0);
        }else{
            return $db->set($key,$data,$config["DBCACHE_TIME"]);
        }
    }
    /**
     * 更新元素
     * @param string $key 键
     * @param string $value 值
     * @param int $time 过期时间
     * @return bool
     */
    public static function UpdateData($key,$data,$time='0'){
        $db=UTMemcache::GetMemcache();
        if(!UTMemcache::ModTable($key)){
            return false;
        }else{
            return $db->replace($key,$data,$time);
        }
    }
    /**
     * 删除元素
     * @param string $key 键
     * @param int $time 删除等待时间
     * @return bool
     */
    public static function DelData($key,$time='0'){
        $db=UTMemcache::GetMemcache();
        if(!UTMemcache::ModTable($key)){
            return false;
        }else{
            return $db->delete($key,$time);
        }
    }
    /**
     * 清空元素
     * @return bool
    */
    public static function Clear(){
        $db=UTMemcache::GetMemcache();
        return $db->flush();
    }
    /**
     * 获取服务器池的统计信息
     * @return array
     */
    public static function Status(){
        $db=UTMemcache::GetMemcache();
        return $db->getStats();
    }
}