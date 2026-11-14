<?php
/**
 * 进程内数组缓存：真实实现，不依赖任何扩展
 *
 * 文件路径：app/share/Cache/ArrayCache.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\ArrayCache
 * 引用方式：use Share\Cache\ArrayCache;
 */
namespace Share\Cache;
class ArrayCache extends Adapter{
    /**
     * 存储：键 => array(value, expireAt)
     */
    private static $store=array();
    private static $hits=0;
    private static $misses=0;
    protected function rawGet($key){
        if(!array_key_exists($key,self::$store)){
            self::$misses++;
            return null;
        }
        $expire=self::$store[$key][1];
        if($expire>0 && $expire<=time()){
            unset(self::$store[$key]);
            self::$misses++;
            return null;
        }
        self::$hits++;
        return self::$store[$key][0];
    }
    protected function rawSet($key,$value,$ttl){
        self::$store[$key]=array($value,$ttl>0 ? time()+$ttl : 0);
        return true;
    }
    protected function rawDelete($key){
        unset(self::$store[$key]);
    }
    protected function rawExists($key){
        return $this->rawGet($key)!==null;
    }
    protected function rawFlush(){
        foreach(array_keys(self::$store) as $key){
            if($this->prefix==='' || strpos($key,$this->prefix)===0){
                unset(self::$store[$key]);
            }
        }
        return true;
    }
    protected function rawCounter($key,$step){
        $current=$this->rawGet($key);
        $value=($current===null ? 0 : (int)$current)+(int)$step;
        self::$store[$key]=array((string)$value,isset(self::$store[$key]) ? self::$store[$key][1] : 0);
        return $value;
    }
    /**
     * 清空全部静态存储，测试隔离用
     * @return void
     */
    public static function reset(){
        self::$store=array();
        self::$hits=0;
        self::$misses=0;
    }
    /**
     * 命中统计
     * @return array
     */
    public static function stats(){
        return array('hits'=>self::$hits,'misses'=>self::$misses,'keys'=>count(self::$store));
    }
    public function name(){
        return 'array';
    }
}