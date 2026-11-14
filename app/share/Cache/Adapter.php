<?php
/**
 * 缓存适配器基类：键前缀、序列化、取值语义统一
 *
 * 文件路径：app/share/Cache/Adapter.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\Adapter
 */
namespace Share\Cache;
use usualtool\Lib\Inc;
abstract class Adapter implements CacheInterface{
    /**
     * 键前缀，用于同实例多站点隔离
     */
    protected $prefix;
    /**
     * 迭代器回调栈深度保护
     */
    const MAX_KEY_LEN=250;
    public function __construct($prefix=null){
        $this->prefix=$prefix===null ? self::defaultPrefix() : (string)$prefix;
    }
    /**
     * 默认前缀取 APPNAME，避免多站共用 Redis 时互撞
     * @return string
     */
    protected static function defaultPrefix(){
        $name='ut';
        if(class_exists('usaltool\\Lib\\Inc')){
            try{
                $config=Inc::GetConfig();
                if(!empty($config['APPNAME'])){ $name=$config['APPNAME']; }
            }catch(\Throwable $e){
                $name='ut';
            }
        }
        return $name.'.';
    }
    /**
     * 拼前缀并校验键，非法字符直接抛错而不是静默查空
     * @param string $key
     * @return string
     */
    public function key($key){
        $key=(string)$key;
        if($key===''){
            throw new \InvalidArgumentException('cache key 不能为空');
        }
        if(!preg_match('/^[A-Za-z0-9_:.\-]+$/',$key)){
            throw new \InvalidArgumentException('cache key 仅允许字母数字与 _ : . - ：'.$key);
        }
        $full=$this->prefix.$key;
        if(strlen($full)>self::MAX_KEY_LEN){
            throw new \InvalidArgumentException('cache key 超长（含前缀 '.strlen($full).' 字节）');
        }
        return $full;
    }
    /**
     * TTL 归一：负数与 0 都视为永久
     * @param int $ttl
     * @return int
     */
    public static function ttl($ttl){
        $ttl=(int)$ttl;
        return $ttl<0 ? 0 : $ttl;
    }
    /**
     * 统一序列化，避免驱动各做各的
     * @param mixed $value
     * @return string
     */
    protected function encode($value){
        return (string)json_encode($value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    /**
     * 统一反序列化，非 JSON 的历史数据原样返回
     * @param string|null $raw
     * @return mixed
     */
    protected function decode($raw){
        if($raw===null || $raw===false || $raw===''){ return null; }
        $value=json_decode($raw,true);
        if($value===null && $raw!=='null'){ return $raw; }
        return $value;
    }
    public function get($key,$default=null){
        $result=$this->read($this->key($key));
        return $result[0] ? $result[1] : $default;
    }
    public function set($key,$value,$ttl=0){
        return (bool)$this->write($this->key($key),$value,self::ttl($ttl));
    }
    public function has($key){
        return (bool)$this->rawExists($this->key($key));
    }
    public function delete($key){
        $this->rawDelete($this->key($key));
        return true;
    }
    public function clear(){
        return (bool)$this->rawFlush();
    }
    public function increment($key,$step=1,$ttl=0){
        $full=$this->key($key);
        if(method_exists($this,'rawCounter')){
            $value=$this->rawCounter($full,(int)$step);
            if($value!==false){ return $value; }
        }
        $current=$this->read($full);
        $value=($current[0] ? (int)$current[1] : 0)+(int)$step;
        if(!$this->write($full,$value,self::ttl($ttl))){ return false; }
        return $value;
    }
    public function remember($key,$ttl,$fn){
        $full=$this->key($key);
        $result=$this->read($full);
        if($result[0]){ return $result[1]; }
        $value=call_user_func($fn);
        $this->write($full,$value,self::ttl($ttl));
        return $value;
    }
    /**
     * 带序列化的读取
     * @param string $key 已含前缀
     * @return mixed
     */
    protected function read($key){
        $raw=$this->rawGet($key);
        if($raw===null || $raw===false){ return array(false,null); }
        return array(true,$this->decode($raw));
    }
    /**
     * 带序列化的写入
     * @param string $key 已含前缀
     * @param mixed $value
     * @param int $ttl 秒
     * @return bool
     */
    protected function write($key,$value,$ttl){
        return (bool)$this->rawSet($key,$this->encode($value),$ttl);
    }
    /**
     * 取原始字符串，未命中返回 null
     * @param string $key
     * @return string|null
     */
    abstract protected function rawGet($key);
    /**
     * 写原始字符串
     * @param string $key
     * @param string $value
     * @param int $ttl 秒
     * @return bool
     */
    abstract protected function rawSet($key,$value,$ttl);
    /**
     * 删除键
     * @param string $key
     */
    abstract protected function rawDelete($key);
    /**
     * 键是否存在
     * @param string $key
     * @return bool
     */
    abstract protected function rawExists($key);
    /**
     * 清空本前缀下的键
     * @return bool
     */
    abstract protected function rawFlush();
    /**
     * 原子自增，不支持时返回 false 由基类降级处理
     * @param string $key
     * @param int $step
     * @return int|false
     */
    protected function rawCounter($key,$step){
        return false;
    }
}