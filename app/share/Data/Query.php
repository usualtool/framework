<?php
/**
 * 链式查询构造器
 *
 * 文件路径：app/share/Data/Query.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\Query
 * 引用方式：use Share\Data\Query; 或 Share\Data\Db::table("表名")
 *
 * 示例
 *   $q=Db::table("cms_goods")
 *       ->field("id,title")
 *       ->where(array("status"=>1,"price"=>array(">=",10)))
 *       ->where_raw("stock>?",array(0))
 *       ->order("id desc")
 *       ->limit(10);
 *   $rows=$q->get();
 *   $total=$q->count();      // 复用同一条件
 */
namespace Share\Data;

class Query{

    /**
     * 执行适配器
     */
    private $db;

    private $table;
    private $field="*";
    private $where="";
    private $param=array();
    private $order="";
    private $limit="";

    /**
     * @param DbInterface $db
     * @param string $table
     */
    public function __construct(DbInterface $db,$table){
        $this->db=$db;
        $this->table=Guard::Table($table);
    }

    /**
     * 换表
     * @return $this
     */
    public function table($table){
        $this->table=Guard::Table($table);
        return $this;
    }

    /**
     * 字段，支持 'id,title' 或 ['id','title']
     * @return $this
     */
    public function field($field){
        $this->field=Guard::Field($field);
        return $this;
    }

    /**
     * field() 的别名
     * @return $this
     */
    public function select($field){
        return $this->field($field);
    }

    /**
     * 追加条件：数组走条件语法，字符串可带 ? 与 $param
     * @return $this
     */
    public function where($where,array $param=array()){
        $cond=Guard::Where($where,$param);
        $this->Append($cond["sql"],$cond["param"]);
        return $this;
    }

    /**
     * 追加原生条件串，值走 $param 绑定
     * @return $this
     */
    public function where_raw($sql,array $param=array()){
        $cond=Guard::Where((string)$sql,$param);
        $this->Append($cond["sql"],$cond["param"]);
        return $this;
    }

    /**
     * 排序
     * @return $this
     */
    public function order($order){
        $this->order=Guard::Order($order);
        return $this;
    }

    /**
     * 条数，$offset 非空时拼成 “偏移,条数”
     * @return $this
     */
    public function limit($limit,$offset=null){
        if($offset!==null){
            $limit=(int)$offset.",".(int)$limit;
        }
        $this->limit=Guard::Limit($limit);
        return $this;
    }

    /**
     * 查询多行
     * @return array
     */
    public function get(){
        return $this->db->Select($this->table,$this->field,$this->where,$this->param,$this->order,$this->limit);
    }

    /**
     * 查询单行
     * @return array|null
     */
    public function first(){
        return $this->db->First($this->table,$this->field,$this->where,$this->param,$this->order);
    }

    /**
     * 单行单列
     * @return mixed|null
     */
    public function value($field){
        return $this->db->Value($this->table,$field,$this->where,$this->param,$this->order);
    }

    /**
     * 单列一维数组
     * @return array
     */
    public function column($field){
        return $this->db->Column($this->table,$field,$this->where,$this->param,$this->order,$this->limit);
    }

    /**
     * 统计条数
     * @return int
     */
    public function count(){
        return $this->db->Count($this->table,$this->where,$this->param);
    }

    /**
     * 是否存在
     * @return bool
     */
    public function exists(){
        return $this->db->Exists($this->table,$this->where,$this->param);
    }

    /**
     * 分页
     * @return array rows/total/page/size/pages
     */
    public function page($page=1,$size=20){
        return $this->db->page($this->table,$this->where,$this->param,$page,$size,$this->field,$this->order);
    }

    /**
     * 写入一行
     * @return int|bool
     */
    public function insert(array $data){
        return $this->db->Insert($this->table,$data);
    }

    /**
     * 按当前条件更新
     * @return bool
     */
    public function update(array $data){
        return $this->db->Update($this->table,$data,$this->where,$this->param);
    }

    /**
     * 按当前条件删除
     * @return bool
     */
    public function delete(){
        return $this->db->Delete($this->table,$this->where,$this->param);
    }

    /**
     * 按当前条件自增
     * @return bool
     */
    public function increment($field,$step=1){
        return $this->db->Increment($this->table,$field,$step,$this->where,$this->param);
    }

    /**
     * 按当前条件自减
     * @return bool
     */
    public function decrement($field,$step=1){
        return $this->db->Decrement($this->table,$field,$step,$this->where,$this->param);
    }

    /**
     * 当前执行适配器
     * @return DbInterface
     */
    public function db(){
        return $this->db;
    }

    /**
     * 查看当前构造状态，便于调试
     * @return array
     */
    public function sql(){
        return array(
            "table"=>$this->table,
            "field"=>$this->field,
            "where"=>$this->where,
            "param"=>$this->param,
            "order"=>$this->order,
            "limit"=>$this->limit
        );
    }

    /**
     * 清空累积状态，保留表名
     * @return $this
     */
    public function reset(){
        $this->field="*";
        $this->where="";
        $this->param=array();
        $this->order="";
        $this->limit="";
        return $this;
    }

    /**
     * 条件拼接，括号包裹已有条件以保留 OR 语义
     */
    private function Append($sql,array $param){
        if($sql===""){
            return;
        }
        $this->where=$this->where==="" ? $sql : "(".$this->where.") and (".$sql.")";
        if(!empty($param)){
            $this->param=array_merge($this->param,$param);
        }
    }
}