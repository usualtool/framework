<?php
/**
 *   Share 层初始化入口
 *   需要容器注入时手动调用一次
 *   新增服务只需在本类 run() 内追加一段。
 *   采用「未绑定才绑」的幂等写法，不设运行标记，便于测试中 App::Forget() 后重新注册。
 */
namespace Share;

use usualtool\Lib\App;
use Share\Data\DbInterface;
use Share\Data\UtDb;
use Share\Cache\CacheInterface;
use Share\Cache\UtCache;

class Init{

    /**
     * 登记全部服务绑定
     */
    public static function run(){
        if(!App::Has(DbInterface::class)){
            App::Instance(DbInterface::class,new UtDb());
        }
        if(!App::Has(CacheInterface::class)){
            App::Instance(CacheInterface::class,UtCache::make());
        }
    }
}