<?php
/**
 * 缓存适配层静态入口
 *
 * 文件路径：app/share/Cache/Cache.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\Cache
 * 引用方式：use Share\Cache\Cache;
 *
 *   用法
 *   Cache::set("home:stats",$stats,600);
 *   $stats = Cache::get("home:stats",array());
 *   $rows  = Cache::remember("art:top10",300,function(){ return Article_::Top(); });
 *   Cache::delete("home:stats");
 *   注入场景：$cache->get("k");  // 构造函数注入 CacheInterface
 *   测试：Cache::Use(new MockCache()); ... Cache::Reset();
 */
namespace Share\Cache;

use usualtool\Lib\App;

class Cache{

    /**
     * 显式注入的适配器，null 表示按解析顺序取默认
     */
    private static $store=null;

    /**
     * 兜底适配器，仅在容器与显式注入都未命中时使用
     */
    private static $default=null;

    /**
     * 显式注入适配器
     * @param CacheInterface $cache
     */
    public static function Use(CacheInterface $cache){
        self::$store=$cache;
    }

    /**
     * 临时替换适配器，执行完自动还原，异常也会还原
     * @param CacheInterface $cache
     * @param callable $fn
     * @return mixed
     */
    public static function With(CacheInterface $cache,$fn){
        $old=self::$store;
        self::$store=$cache;
        try{
            return call_user_func($fn);
        }finally{
            self::$store=$old;
        }
    }

    /**
     * 还原到默认解析，测试清理用
     */
    public static function Reset(){
        self::$store=null;
        self::$default=null;
    }

    /**
     * 取当前适配器实例
     * 解析顺序：显式注入 > 容器绑定 > 兜底 UtCache::make()
     * @return CacheInterface
     */
    public static function instance(){
        if(self::$store!==null){
            return self::$store;
        }
        if(class_exists("usaltool\\Lib\\App") && App::Has(CacheInterface::class)){
            return App::Make(CacheInterface::class);
        }
        if(self::$default===null){
            self::$default=UtCache::make();
        }
        return self::$default;
    }

    /**
     * 读取，未命中返回 $default
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key,$default=null){
        return self::instance()->get($key,$default);
    }

    /**
     * 写入
     * @param string $key
     * @param mixed $value
     * @param int $ttl 秒，0 为永久
     * @return bool
     */
    public static function set($key,$value,$ttl=0){
        return self::instance()->set($key,$value,$ttl);
    }

    /**
     * 是否存在
     * @param string $key
     * @return bool
     */
    public static function has($key){
        return self::instance()->has($key);
    }

    /**
     * 删除，幂等
     * @param string $key
     * @return bool
     */
    public static function delete($key){
        return self::instance()->delete($key);
    }

    /**
     * 清空本前缀下的键
     * @return bool
     */
    public static function clear(){
        return self::instance()->clear();
    }

    /**
     * 自增
     * @param string $key
     * @param int $step
     * @param int $ttl
     * @return int|false
     */
    public static function increment($key,$step=1,$ttl=0){
        return self::instance()->increment($key,$step,$ttl);
    }

    /**
     * 读不到就执行回调并写入
     * @param string $key
     * @param int $ttl 秒，0 为永久
     * @param callable $fn
     * @return mixed
     */
    public static function remember($key,$ttl,$fn){
        return self::instance()->remember($key,$ttl,$fn);
    }

    /**
     * 当前驱动名
     * @return string
     */
    public static function name(){
        return self::instance()->name();
    }

    /**
     * 配置声明的驱动类型
     * @return string
     */
    public static function driver(){
        return UtCache::driverType();
    }
}