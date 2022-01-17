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
 * 以实例方法操作Redis
 */
class UTRedis{
    private $redis;
    public function __construct(){
        $config=UsualToolInc\UTInc::GetConfig();
            $this->redis=new \Redis();
            $this->redis->connect($config["REDIS_HOST"],$config["REDIS_PORT"]);
            if($config["REDIS_PASS"]!="UT"):
                $this->redis->auth($config["REDIS_PASS"]);
            endif;
    }
    /**
     * 设置一个key
     * @param string $key
     * @param string $value
     */
    public function Set($key,$value){
        return $this->redis->set($key,$value);
    }
    /**
     * 获取一个key的值
     * @param string $key
     */
    public function Get($key){
        return $this->redis->get($key);
    }  
    /**
     * 判断key是否存在
     * @param string $key
     */  
    public function Exists($key){
        return $this->redis->exists($key);
    }  
    /**
     * 返回key, ‘*’全部, ‘?’一个
     * @param string $key
     * @return array
     */   
    public function Keys($key){
        return $this->redis->keys($key);
    }
    /**
     * 删除key
     * @param string $key
     */
    public function Del($key){
        return $this->redis->del($key);
    }
    /**
     * 设置key的过期时间，单位秒
     * @param string $key
     * @param int $expire
     */
    public function Expire($key,$expire){
        return $this->redis->expire($key,$expire);
    }
    /**
     * 查询key过期时间
     * @param string $key
     */
    public function Ttl($key){
        return $this->redis->ttl($key);
    }
    /**
     * 设置key过期时间，时间为时间戳
     * @param string $key
     * @param int $time
     */
    public function ExprieAt($key,$time){
        return $this->redis->expireat($key,$time);
    }
    /**
     * 返回随机key
     */
    public function RandomKey(){
        return $this->redis->randomkey();
    }
    /**
     * 获取当前数据库id
     * @return int
     */
    public function GetDbId(){
        return $this->dbId;
    }
   /**
     * 监控key,就是一个或多个key添加一个乐观锁
     * 在此期间如果key的值如果发生的改变，刚不能为key设定值
     * 可以重新取得Key的值。
     * @param $key
     */
    public function Watch($key){
        return $this->redis->watch($key);
    }
    /**
     * 取消当前链接对所有key的watch
     * EXEC 命令或 DISCARD 命令先被执行了的话，那么就不需要再执行 UNWATCH 了
     */
    public function UnWatch(){
        return $this->redis->unwatch();
    }
    /**
     * 开启一个事务
     * 事务的调用有两种模式Redis::MULTI和Redis::PIPELINE，
     * 默认是Redis::MULTI模式，
     * Redis::PIPELINE管道模式速度更快，但没有任何保证原子性有可能造成数据的丢失
     */
    public function Multi($type=\Redis::MULTI){
        return $this->redis->multi($type);
    }   
    /**
     * 执行一个事务
     * 收到 EXEC 命令后进入事务执行，事务中任意命令执行失败，其余的命令依然被执行
     */
    public function Exec(){
        return $this->redis->exec();
    }    
    /**
     * 回滚一个事务
     */
    public function Discard(){
        return $this->redis->discard();
    }
    /**
     * 设置一个有过期时间的key
     * @param string $key
     * @param int $expire
     * @param string $value
     */
    public function SetEx($key,$expire,$value){
        return $this->redis->setex($key,$expire,$value);
    }
    /**
     * 设置一个key,如果key存在,不做任何操作.
     * @param string $key
     * @param string $value
     */
    public function SetNx($key,$value){
        return $this->redis->setnx($key,$value);
    }
    /**
     * 批量设置key
     * @param array $arr
     */
    public function Mset($arr){
        return $this->redis->mset($arr);
    }
    /**
     * 返回集合中所有元素
     * @param string $key
     */
    public function Smembers($key){
        return $this->redis->smembers($key);
    }
    /**
     * 求两个集合的差集
     * @param string $key1
     * @param string $key2
     */
    public function Sdiff($key1,$key2){
        return $this->redis->sdiff($key1,$key2);
    }
    /**
     * 添加集合
     * @param string $key
     * @param string|array $value
     */
    public function Sadd($key,$value){
        if(!is_array($value))
            $arr=array($value);
        else
            $arr=$value;
        foreach($arr as $row)
            $this->redis->sadd($key,$row);
    }
    /**
     * 返回无序集合的元素个数
     * @param string $key
     */
    public function Scard($key){
        return $this->redis->scard($key);
    }
    /**
     * 从集合中删除一个元素
     * @param string $key
     * @param string $value
     */
    public function Srem($key,$value){
        return $this->redis->srem($key,$value);
    }  
    /**
     * 选择数据库
     * @param int $dbId 数据库ID号
     * @return bool
     */
    public function Select($dbId){
        $this->dbId=$dbId;
        return $this->redis->select($dbId);
    }
    /**
     * 清空当前数据库
     * @return bool
     */
    public function FlushDB(){
        return $this->redis->flushdb();
    }
    /**
     * 返回当前数据库状态
     * @return array
     */
    public function Info(){
        return $this->redis->info();
    }
     /**
     * 给当前集合添加一个元素
     * 如果value已经存在，会更新order的值。
     * @param string $key
     * @param string $order 序号
     * @param string $value 值
     * @return bool
     */   
    public function Zadd($key,$order,$value){
        return $this->redis->zadd($key,$order,$value);   
    }
    /**
     * 给$value成员的order值，增加$num,可以为负数
     * @param string $key
     * @param string $num 序号
     * @param string $value 值
     * @return 返回新的order
     */
    public function ZinCry($key,$num,$value){
        return $this->redis->zincry($key,$num,$value);
    }
    /**
     * 删除值为value的元素
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function Zrem($key,$value){
        return $this->redis->zrem($key,$value);
    }
    /**
     * 集合以order递增排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function Zrange($key,$start,$end){
        return $this->redis->zrange($key,$start,$end);
    }
    /**
     * 集合以order递减排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function ZrevRange($key,$start,$end){
        return $this->redis->zrevrange($key,$start,$end);
    }
    /**
     * 集合以order递增排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function ZrangeByScore($key,$start='-inf',$end="+inf",$option=array()){
        return $this->redis->zrangebyscore($key,$start,$end,$option);
    }
    /**
     * 集合以order递减排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function ZrevRangeByScore($key,$start='-inf',$end="+inf",$option=array()){
        return $this->redis->zrevrangebyscore($key,$start,$end,$option);
    }
    /**
     * 返回order值在start与end之间的数量
     * @param string $key
     * @param int $start
     * @param int $end
     */
    public function Zcount($key,$start,$end){
        return $this->redis->zcount($key,$start,$end);
    }
    /**
     * 返回值为value的order值
     * @param string $key
     * @param string $value
     */
    public function Zscore($key,$value){
        return $this->redis->zscore($key,$value);
    } 
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param string $key
     * @param string $value
     */
    public function Zrank($key,$value){
        return $this->redis->zrank($key,$value);
    }
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param string $key
     * @param string $value
     */
    public function ZrevRank($key,$value){
        return $this->redis->zrevrank($key,$value);
    }
    /**
     * 删除集合中，score值在start与end之间的元素　包括start end
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @return int
     */
    public function ZremRangeByScore($key,$start,$end){
        return $this->redis->zremrangebyscore($key,$start,$end);
    }
    /**
     * 返回集合元素个数。
     * @param string $key
     */
    public function Zcard($key){
        return $this->redis->zcard($key);
    }
    /**
     * 在队列尾部插入一个元素
     * @param string $key
     * @param string $value
     * 返回队列长度
     */
    public function Rpush($key,$value){
        return $this->redis->rpush($key,$value); 
    }
    /**
     * 在队列尾部插入一个元素 如果key不存在则不执行
     * @param string $key
     * @param string $value
     * 返回队列长度
     */
    public function Rpushx($key,$value){
        return $this->redis->rpushx($key,$value);
    }
    /**
     * 在队列头部插入一个元素
     * @param string $key
     * @param string $value
     * 返回队列长度
     */
    public function Lpush($key,$value){
        return $this->redis->lpush($key,$value);
    }
    /**
     * 在队列头插入一个元素 如果key不存在不执行
     * @param string $key
     * @param string $value
     * 返回队列长度
     */
    public function Lpushx($key,$value){
        return $this->redis->lpushx($key,$value);
    }
    /**
     * 返回队列长度
     * @param string $key
     */
    public function Llen($key){
        return $this->redis->llen($key); 
    }
    /**
     * 返回队列指定区间的元素
     * @param string $key
     * @param int $start
     * @param int $end
     */
    public function Lrange($key,$start,$end){
        return $this->redis->lrange($key,$start,$end);
    }
    /**
     * 返回队列中指定索引的元素
     * @param string $key
     * @param int $index
     */
    public function Lindex($key,$index){
        return $this->redis->lindex($key,$index);
    }
    /**
     * 设定队列中指定index的值
     * @param string $key
     * @param int $index
     * @param string $value
     */
    public function Lset($key,$index,$value){
        return $this->redis->lset($key,$index,$value);
    }
    /**
     * 删除值为vaule的count个元素
     * count<0从尾部开始，>0从头部开始，=0删除全部
     * @param string $key
     * @param int $count
     * @param string $value
     */
    public function Lrem($key,$count,$value){
        return $this->redis->lrem($key,$value,$count);
    }
    /**
     * 删除并返回队列中的开头元素
     * @param string $key
     */
    public function Lpop($key){
        return $this->redis->lpop($key);
    }
    /**
     * 删除并返回队列中的末尾元素
     * @param string $key
     */
    public function Rpop($key){
        return $this->redis->rpop($key);
    }      
}