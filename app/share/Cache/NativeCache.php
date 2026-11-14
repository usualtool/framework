<?php
/**
 * 原生驱动缓存：直接使用 phpredis / memcached 扩展
 *
 * 文件路径：app/share/Cache/NativeCache.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\NativeCache
 * 引用方式：由 UtCache 创建，业务层不直接引用
 */
namespace Share\Cache;
use usualtool\Lib\Inc;
class NativeCache extends Adapter{
    /**
     * 驱动类型：redis / memcache
     */
    private $type;
    /**
     * 连接句柄，静态复用
     */
    private static $conn=array();
    /**
     * 配置快照，静态复用
     */
    private static $config=null;
    public function __construct($type,$prefix=null){
        parent::__construct($prefix);
        $this->type=$type==='redis' ? 'redis' : 'memcache';
    }
    /**
     * 读配置，只读一次
     * @return array
     */
    public static function config(){
        if(self::$config===null){
            self::$config=array();
            if(class_exists('usaltool\\Lib\\Inc')){
                try{ self::$config=(array)Inc::GetConfig(); }catch(\Throwable $e){ self::$config=array(); }
            }
        }
        return self::$config;
    }
    /**
     * 取连接，失败返回 null
     * @return object|null
     */
    private function conn(){
        if(isset(self::$conn[$this->type])){ return self::$conn[$this->type] ?: null; }
        $config=self::config();
        $handle=false;
        try{
            if($this->type==='redis'){
                $redis=new \Redis();
                $port=isset($config['REDIS_PORT']) ? (int)$config['REDIS_PORT'] : 6379;
                $handle=@$redis->connect(isset($config['REDIS_HOST']) ? $config['REDIS_HOST'] : '127.0.0.1',$port,2.0);
                if($handle && !empty($config['REDIS_PASS']) && $config['REDIS_PASS']!=='UT'){
                    @$redis->auth($config['REDIS_PASS']);
                }
                $handle=$handle ? $redis : false;
            }else{
                if(!class_exists('\\Memcached')){ $handle=false; }else{
                    $mem=new \Memcached();
                    $mem->setOption(\Memcached::OPT_BINARY_PROTOCOL,true);
                    $mem->addServer(isset($config['MEMCACHE_HOST']) ? $config['MEMCACHE_HOST'] : '127.0.0.1',isset($config['MEMCACHE_PORT']) ? (int)$config['MEMCACHE_PORT'] : 11211);
                    $handle=$mem;
                }
            }
        }catch(\Throwable $e){
            error_log('[UT-CACHE] 连接失败：'.$e->getMessage());
            $handle=false;
        }
        if(!$handle){ error_log('[UT-CACHE] '.$this->type.' 不可用，本次请求缓存降级为直连'); }
        self::$conn[$this->type]=$handle;
        return $handle ?: null;
    }
    protected function rawGet($key){
        $conn=$this->conn();
        if(!$conn){ return null; }
        $value=$conn->get($key);
        return ($value===false || $value===null) ? null : (string)$value;
    }
    protected function rawSet($key,$value,$ttl){
        $conn=$this->conn();
        if(!$conn){ return false; }
        if($this->type==='redis'){
            return $ttl>0 ? (bool)$conn->setex($key,$ttl,$value) : (bool)$conn->set($key,$value);
        }
        return (bool)$conn->set($key,$value,$ttl);
    }
    protected function rawDelete($key){
        $conn=$this->conn();
        if(!$conn){ return; }
        if($this->type==='redis'){
            $conn->del($key);
        }else{
            $conn->delete($key);
        }
    }
    protected function rawExists($key){
        $conn=$this->conn();
        if(!$conn){ return false; }
        if($this->type==='redis'){
            return (bool)$conn->exists($key);
        }
        $conn->get($key);
        return $conn->getResultCode()!==\Memcached::RES_NOTFOUND;
    }
    protected function rawFlush(){
        $conn=$this->conn();
        if(!$conn){ return false; }
        if($this->type!=='redis'){
            return (bool)$conn->flush();
        }
        $iterator=null;
        $pattern=$this->prefix.'*';
        $deleted=0;
        while(true){
            $keys=$conn->scan($iterator,$pattern,200);
            if($keys===false){
                if($iterator===0 || $iterator===null){ break; }
                continue;
            }
            if($keys){ $deleted+=(int)$conn->del($keys); }
            if($iterator===0 || $iterator===null){ break; }
        }
        return true;
    }
    protected function rawCounter($key,$step){
        $conn=$this->conn();
        if(!$conn){ return false; }
        if($this->type==='redis'){
            return (int)$conn->incrBy($key,$step);
        }
        $value=$conn->increment($key,$step);
        if($value===false){
            $conn->set($key,(string)$step,0);
            return (int)$step;
        }
        return (int)$value;
    }
    public function name(){
        return $this->type;
    }
}