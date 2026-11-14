<?php
/**
 * 缓存驱动工厂：按配置选后端，选不到就退回数组缓存
 *
 * 文件路径：app/share/Cache/UtCache.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\UtCache
 * 引用方式：由 Cache 门面调用，业务层一般不直接引用
 */
namespace Share\Cache;
class UtCache{
    /**
     * 当前请求的解析结果，避免重复判定
     */
    private static $resolved=null;
    /**
     * 创建缓存实例
     * @param string|null $prefix 键前缀，null 取 APPNAME
     * @return CacheInterface
     */
    public static function make($prefix=null){
        $type=self::driverType();
        if($type==='redis' || $type==='memcache'){
            return new NativeCache($type,$prefix);
        }
        return new ArrayCache($prefix);
    }
    /**
     * 当前生效的驱动类型
     * @return string redis / memcache / array
     */
    public static function driverType(){
        if(self::$resolved!==null){ return self::$resolved; }
        $config=NativeCache::config();
        $want=isset($config['DBCACHE']) ? strtolower(trim((string)$config['DBCACHE'])) : '';
        if($want==='redis'){
            self::$resolved=class_exists('\\Redis') ? 'redis' : 'array';
        }elseif($want==='memcache'){
            self::$resolved=class_exists('\\Memcached') ? 'memcache' : 'array';
        }else{
            self::$resolved='array';
        }
        if($want!=='' && $want!=='array' && self::$resolved==='array'){
            error_log('[UT-CACHE] DBCACHE='.$want.' 但扩展不可用，已降级为进程内数组缓存');
        }
        return self::$resolved;
    }
    /**
     * 配置声明的驱动是否真的可用
     * @return bool
     */
    public static function available(){
        return self::driverType()!=='array';
    }
    /**
     * 重置解析结果，测试用
     * @return void
     */
    public static function reset(){
        self::$resolved=null;
    }
}