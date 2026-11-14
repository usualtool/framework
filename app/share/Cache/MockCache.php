<?php
/**
 * 测试替身：不落任何存储，记录收到的语义参数并回放预置结果
 *
 * 文件路径：app/share/Cache/MockCache.php
 * 命名空间：Share\Cache
 * 完整类名：Share\Cache\MockCache
 * 引用方式：use Share\Cache\MockCache;
 *
 *   用法
 *   $cache=new MockCache();
 *   $cache->push("user:1",array("id"=>1,"name"=>"a"));
 *   $cache->get("user:1");              // 返回预置值，同时记录一次 get
 *   $cache->calls();                    // 全部调用记录
 *   $cache->last();                     // 最后一次调用
 *   $cache->failWrites=true;            // 模拟写失败
 */
namespace Share\Cache;
class MockCache extends Adapter{
    private $store=array();
    private $calls=array();
    /**
     * 置 true 后所有写入返回 false
     */
    public $failWrites=false;
    /**
     * 置 true 后所有读取返回未命中
     */
    public $failReads=false;
    /**
     * 预置一个键值
     * @param string $key 不含前缀的原始键
     * @param mixed $value
     * @return $this
     */
    public function push($key,$value){
        $this->store[$this->key($key)]=$value;
        return $this;
    }
    /**
     * 批量预置
     * @param array $pairs 键值对
     * @return $this
     */
    public function pushAll(array $pairs){
        foreach($pairs as $key=>$value){ $this->push($key,$value); }
        return $this;
    }
    /**
     * 全部调用记录
     * @return array
     */
    public function calls(){
        return $this->calls;
    }
    /**
     * 最后一次调用
     * @return array|null
     */
    public function last(){
        return $this->calls ? $this->calls[count($this->calls)-1] : null;
    }
    /**
     * 清空记录与预置
     * @return void
     */
    public function reset(){
        $this->store=array();
        $this->calls=array();
        $this->failWrites=false;
        $this->failReads=false;
    }
    private function log($op,$key,$extra=array()){
        $this->calls[]=array_merge(array('op'=>$op,'key'=>$key),$extra);
    }
    protected function rawGet($key){
        $this->log('get',$key);
        if($this->failReads){ return null; }
        if(!array_key_exists($key,$this->store)){ return null; }
        $value=$this->store[$key];
        return is_string($value) ? $value : $this->encode($value);
    }
    protected function rawSet($key,$value,$ttl){
        $this->log('set',$key,array('ttl'=>$ttl,'value'=>$value));
        if($this->failWrites){ return false; }
        $this->store[$key]=$value;
        return true;
    }
    protected function rawDelete($key){
        $this->log('delete',$key);
        unset($this->store[$key]);
    }
    protected function rawExists($key){
        $this->log('exists',$key);
        if($this->failReads){ return false; }
        return array_key_exists($key,$this->store);
    }
    protected function rawFlush(){
        $this->log('flush',$this->prefix);
        if($this->failWrites){ return false; }
        foreach(array_keys($this->store) as $key){
            if($this->prefix==='' || strpos($key,$this->prefix)===0){ unset($this->store[$key]); }
        }
        return true;
    }
    protected function rawCounter($key,$step){
        $this->log('counter',$key,array('step'=>$step));
        if($this->failWrites){ return false; }
        $value=(isset($this->store[$key]) ? (int)$this->store[$key] : 0)+(int)$step;
        $this->store[$key]=$value;
        return $value;
    }
    public function name(){
        return 'mock';
    }
}