<?php
/**
 * 测试替身：不连数据库，记录调用并回放预置结果
 *
 * 文件路径：app/share/Data/MockDb.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\MockDb
 * 引用方式：use Share\Data\MockDb;
 *
 * 用法
 *   $db=new MockDb();
 *   $db->pushRows(array(array('id'=>1,'title'=>'A')));   // 预置下一次查询结果
 *   $rows=$db->Select("cms_goods","id,title",array("status"=>1));
 *   $args=$db->calls("Select")[0]["args"];               // 断言收到的参数
 */
namespace Share\Data;

class MockDb extends Adapter{

    /**
     * 待回放的返回值，先进先出
     */
    private $queue=array();

    /**
     * 调用记录：method / args
     */
    private $log=array();

    /**
     * 事务计数
     */
    private $counters=array("begin"=>0,"commit"=>0,"rollback"=>0);

    /**
     * 是否让所有写操作返回 false
     */
    private $failWrites=false;

    /**
     * 适配器标识
     * @return string
     */
    public function Name(){
        return "mock";
    }

    /**
     * 预置下一次调用的返回值
     * @param mixed $response
     * @return $this
     */
    public function push($response){
        $this->queue[]=$response;
        return $this;
    }

    /**
     * 预置下一次查询返回的行数组
     * @param array $rows
     * @return $this
     */
    public function pushRows(array $rows){
        $this->queue[]=$rows;
        return $this;
    }

    /**
     * 预置下一次写入返回的自增 ID
     * @param int $id
     * @return $this
     */
    public function pushId($id){
        $this->queue[]=(int)$id;
        return $this;
    }

    /**
     * 让所有写操作返回 false，模拟写失败
     * @param bool $on
     * @return $this
     */
    public function failWrites($on=true){
        $this->failWrites=(bool)$on;
        return $this;
    }

    /**
     * 取调用记录，$method 为空返回全部
     * @param string|null $method
     * @return array
     */
    public function calls($method=null){
        if($method===null){
            return $this->log;
        }
        $list=array();
        foreach($this->log as $item){
            if($item["method"]===$method){
                $list[]=$item;
            }
        }
        return $list;
    }

    /**
     * 取最后一次调用，$method 为空取任意方法的最后一次
     * @param string|null $method
     * @return array|null
     */
    public function last($method=null){
        $list=$this->calls($method);
        return empty($list) ? null : $list[count($list)-1];
    }

    /**
     * 事务计数
     * @return array begin/commit/rollback
     */
    public function counters(){
        return $this->counters;
    }

    /**
     * 清空预置与记录，保留配置
     * @return $this
     */
    public function reset(){
        $this->queue=array();
        $this->log=array();
        $this->counters=array("begin"=>0,"commit"=>0,"rollback"=>0);
        $this->failWrites=false;
        return $this;
    }

    protected function doSelect($table,$field,$where,array $param,$order,$limit){
        return (array)$this->record("Select",func_get_args(),array());
    }

    protected function doCount($table,$where,array $param){
        return (int)$this->record("Count",func_get_args(),0);
    }

    protected function doInsert($table,array $data){
        $result=$this->record("Insert",func_get_args(),1);
        return $this->failWrites ? false : $result;
    }

    protected function doUpdate($table,array $data,$where,array $param){
        $result=$this->record("Update",func_get_args(),true);
        return $this->failWrites ? false : $result;
    }

    protected function doDelete($table,$where,array $param){
        $result=$this->record("Delete",func_get_args(),true);
        return $this->failWrites ? false : $result;
    }

    protected function doIncrement($table,$field,$step,$where,array $param){
        $result=$this->record("Increment",func_get_args(),true);
        return $this->failWrites ? false : $result;
    }

    protected function doQuery($sql,array $param){
        return (array)$this->record("Query",func_get_args(),array());
    }

    protected function doExecute($sql,array $param){
        $result=$this->record("Execute",func_get_args(),true);
        return $this->failWrites ? false : $result;
    }

    protected function begin(){
        $this->counters["begin"]++;
        return true;
    }

    protected function commit(){
        $this->counters["commit"]++;
        return true;
    }

    protected function rollback(){
        $this->counters["rollback"]++;
        return true;
    }

    /**
     * 记录调用并按队列回放
     */
    private function record($method,array $args,$default){
        $this->log[]=array("method"=>$method,"args"=>$args);
        if(!empty($this->queue)){
            return array_shift($this->queue);
        }
        return $default;
    }
}