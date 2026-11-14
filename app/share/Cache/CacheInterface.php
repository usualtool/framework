<?php
/**
 * 缓存适配层契约
 *
 * 文件路径：app/share/Cache/CacheInterface.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\CacheInterface
 * 引用方式：use Share\Cache\CacheInterface;
 *
 * 语义约定（与框架 Memcache/Redis 的差异在此抹平）
 *   get   取不到返 $default，绝不返 false 表示"没命中"
 *   set   $ttl 单位是秒，0 表示永久；返回 bool
 *   has   存在返回 true，不存在的过期键也返回 false
 *   delete 幂等，键不存在也返回 true
 *   clear 只清本前缀下的键，不碰同实例的其他数据
 *   increment 键不存在时以 0 为基数加 $step
 */
namespace Share\Cache;
interface CacheInterface{
    /**
     * 读取，未命中返回 $default
     * @param string $key 键
     * @param mixed $default 未命中时的返回值
     * @return mixed
     */
    public function get($key,$default=null);
    /**
     * 写入
     * @param string $key 键
     * @param mixed $value 值，数组会被序列化
     * @param int $ttl 生存秒数，0 为永久
     * @return bool
     */
    public function set($key,$value,$ttl=0);
    /**
     * 是否存在且未过期
     * @param string $key 键
     * @return bool
     */
    public function has($key);
    /**
     * 删除，幂等
     * @param string $key 键
     * @return bool
     */
    public function delete($key);
    /**
     * 清空本前缀下的全部键
     * @return bool
     */
    public function clear();
    /**
     * 自增，键不存在时从 0 起算
     * @param string $key 键
     * @param int $step 步长，可为负
     * @param int $ttl 仅在新建时生效，秒，0 为永久
     * @return int|false
     */
    public function increment($key,$step=1,$ttl=0);
    /**
     * 读不到就执行回调并写入
     * @param string $key 键
     * @param int $ttl 生存秒数，0 为永久
     * @param callable $fn 回源函数，返回值写入缓存
     * @return mixed
     */
    public function remember($key,$ttl,$fn);
    /**
     * 当前驱动名，用于排查
     * @return string
     */
    public function name();
}