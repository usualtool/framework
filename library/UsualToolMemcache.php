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
 * 以实例方法操作Memcache
 */
class UTMemcache{
    public $db='';
    public function __construct(){
        $config=UsualToolInc\UTInc::GetConfig();
        $memcache=new \Memcached();
        $memcache->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
        if(strpos($config["MEMCACHE_HOST"],",")!==false){
            $server=array();
            $hosts=explode(',',$config["MEMCACHE_HOST"]);
            $ports=explode(',',$config["MEMCACHE_PORT"]);
            $you=round(1/count($hosts)*100);
            for($i=0;$i<count($hosts);$i++){
                $server[]=array($hosts[$i],$ports[$i],$you);
            }
            $memcache->addServers($server);
        }else{
            $memcache->addServer($config["MEMCACHE_HOST"],$config["MEMCACHE_PORT"]);
        }
        if(!empty($config["MEMCACHE_USER"]) && !empty($config["MEMCACHE_PASS"])){
            $memcache->setSaslAuthData($config["MEMCACHE_USER"],$config["MEMCACHE_PASS"]);
        }
        $this->db=$memcache;
    }
    /**
     * 增加一个元素，存在失败，不存在创建
     * @param string $key 键
     * @param string $value 值
     * @param int $time 过期时间
     * @return bool
     */
    public function Add($key,$value,$time='0'){
        if($this->db->add($key,$value,$time)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 向已存在元素后追加数据
     * @param string $key 键
     * @param string $value 值
     * @return bool
     */
    public function Append($key,$value){
        return $this->db->append($key,$value);
    }
    /**
     * 增加一个元素，存在替换，不存在创建
     * @param string $key 键
     * @param string $value 值
     * @param int $time 过期时间
     * @return bool
     */
    public function Set($key,$value,$time='0'){
        return $this->db->set($key,$value,$time);
    }
    /**
     * 增加多个元素
     * @param array $data 键值对数组
     * @param int $time 过期时间
     * @return bool
     */
    public function SetMulti($data,$time='0'){
        return $this->db->setMulti($setMulti,$time);
    }
    /**
     * 向存在的元素追加数据
     * @param string $key 键
     * @param string $value 追加数据
     * @return bool
     */
    public function Prepend($key,$value){
        $msg=$this->db->get($key);
        if(!$msg){
            return false;
        }else{
            return $this->db->prepend($key,$value);
        }
    }
    /**
     * 替换存在的元素，存在成功，不存在失败
     * @param string $key 键
     * @param string $value 值
     * @param int $time 过期时间
     * @return bool
     */
    public function Replace($key,$value,$time='0'){
        $msg=$this->db->get($key);
        if(!$msg){
            return false;
        }else{
            return $this->db->replace($key,$value,$time);
        }
    }
    /**
     * 获取一个元素
     * @param string $key 键
     * @return bool/string/array
     */
    public function Get($key){
        $msg=$this->db->get($key);
        if(!$msg){
            return false;
        }
        return $msg;
    }
    /**
     * 删除一个元素
     * @param string $key 键
     * @param int $time 删除等待时间
     * @return bool
     */
    public function Del($key,$time='0'){
        $msg=$this->db->get($key);
        if(!$msg){
            return false;
        }else{
            return $this->db->delete($key,$time);
        }
    }
    /**
     * 作废缓存中的所有元素
     * @return bool
    */
    public function DelAll(){
        return $this->db->flush();
    }
    /**
     * 获取服务器池的统计信息
     * @return array
     */
    public function Status(){
        return $this->db->getStats();
    }
}