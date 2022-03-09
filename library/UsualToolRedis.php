<?php
namespace library\UsualToolRedis;
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
 * 以静态方法操作Redis
 */
class UTRedis{
    /**
     * 连接Redis
     */
    public static function GetRedis(){
        $config=UsualToolInc\UTInc::GetConfig();
        $db=new \Redis();
        $db->connect($config["REDIS_HOST"],$config["REDIS_PORT"]);
        if($config["REDIS_PASS"]!="UT"):
            $db->auth($config["REDIS_PASS"]);
        endif;
    }
    /**
     * 判断元素是否存在
     * @param string $key 键
     * @return bool
     */
    public static function ModTable($key){
        $db=UTRedis::GetRedis();
        $res=$db->exists($key);
        if(!$res){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 查询数据
     * @param string|array $key 键，单查xxx或多查array("xxx","yyy")
     * @param string $type 是否批量查询。0为单查，1为多查。
     * @return array
     */
    public static function QueryData($key,$type='0'){
        $db=UTRedis::GetRedis();
        if($type==0):
            return json_decode($db->get($key),true);
        else:
            return json_decode($db->mget(json_encode($key)),true);
        endif;
    }
    /**
     * 创建数据
     * @param string $key 键
     * @param string|array $data 值
     * @param int $time 秒，0不设置过期时间，1设置过期时间为DBCACHE_TIME
     * @return bool
     */
    public static function InsertData($key,$data,$time='0'){
        $db=UTRedis::GetRedis();
        $data=is_array($data) ? json_encode($data) : $data;
        $config=UsualToolInc\UTInc::GetConfig();
        if($time==0):
            $db->set($key,$data);
        else:
            $db->set($key,$data,$config["DBCACHE_TIME"]);
        endif;
    }
    /**
     * 编辑数据
     * @param string $key 键
     * @param string|array $data 值
     * @param int $time 秒，0不设置过期时间，1设置过期时间为REDIS_TIME
     * @return bool
     */
    public static function UpdateData($key,$data,$time='0'){
        $db=UTRedis::GetRedis();
        $data=is_array($data) ? json_encode($data) : $data;
        if(!UTRedis::ModTable($key)):
            return false;
        else:
            if($time==0):
                $db->set($key,$data);
            else:
                $db->set($key,$data,$config["DBCACHE_TIME"]);
            endif;
        endif;
    }
    /**
     * 删除数据
     * @param string $key 键
     */
    public static function DelData($key){
        $db=UTRedis::GetRedis();
        $db->del($key);
    }
    /**
     * 查询所有键
     * @return array
     */
    public static function QueryKey(){
        $db=UTRedis::GetRedis();
        return $db->keys("*");
    }
    /**
     * 创建队列任务
     * @param array $array 加入队列的数组
     */
    public static function AddQueue($array){
        $db=UTRedis::GetRedis();
        foreach($array as $k=>$v){
            $db->rpush("queue",$v);
        }
    }
    /**
     * 执行队列任务
     * @return array
     */
    public static function RunQueue(){
        $db=UTRedis::GetRedis(); 
        $value=$db->lpop('queue');
        if($value):
            echo$value;
        else:
            echo"Queue Complete";
        endif;
    }
    /**
     * 清空当前数据库
     * @return bool
     */
    public static function Clear(){
        $db=UTRedis::GetRedis();
        return $db->flushdb();
    }
}