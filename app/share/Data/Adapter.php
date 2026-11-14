<?php
/**
 * 适配器基类：校验 + 通用查询 + 事务编排
 *
 * 文件路径：app/share/Data/Adapter.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\Adapter
 * 引用方式：继承使用，模块不直接引用
 *
 *   UtDb      生产：转发 usualtool\Lib\Data，五驱动通用
 *   MockDb    测试：不连库，记录收到的语义参数，可预置返回
 *   SqliteDb  测试：PDO 内存库，真实执行 SQL
 */
namespace Share\Data;

abstract class Adapter implements DbInterface{

    /**
     * 链式查询入口，绑定本适配器
     *   $db->table("cms_goods")->where(array("status"=>1))->get();
     * @param string $table
     * @return Query
     */
    public function table($table){
        return new Query($this,$table);
    }

    /**
     * 查询多行
     * @param string $table
     * @param string|array $field
     * @param string|array $where
     * @param array $param
     * @param string $order
     * @param string|int $limit
     * @return array
     */
    public function Select($table,$field='*',$where='',$param=array(),$order='',$limit=''){
        $cond=Guard::Where($where,$param);
        return $this->doSelect(Guard::Table($table),Guard::Field($field),$cond["sql"],$cond["param"],Guard::Order($order),Guard::Limit($limit));
    }

    /**
     * 统计条数
     * @return int
     */
    public function Count($table,$where='',$param=array()){
        $cond=Guard::Where($where,$param);
        return (int)$this->doCount(Guard::Table($table),$cond["sql"],$cond["param"]);
    }

    /**
     * 查询单行
     * @return array|null 无数据返回 null
     */
    public function First($table,$field='*',$where='',$param=array(),$order=''){
        $rows=$this->Select($table,$field,$where,$param,$order,'1');
        return empty($rows) ? null : $rows[0];
    }

    /**
     * 取单行单列的值
     * @param string $field 只允许单个字段名
     * @param string $order 排序，取排序后的首行
     * @return mixed|null
     */
    public function Value($table,$field,$where='',$param=array(),$order=''){
        $field=Guard::Field($field);
        if(strpos($field,",")!==false || $field==="*" || stripos($field," as ")!==false){
            throw new \InvalidArgumentException("Value 只接受单个字段名：".$field);
        }
        $row=$this->First($table,$field,$where,$param,$order);
        return $row===null ? null : (array_key_exists($field,$row) ? $row[$field] : null);
    }

    /**
     * 取单列的一维数组
     * @return array
     */
    public function Column($table,$field,$where='',$param=array(),$order='',$limit=''){
        $field=Guard::Field($field);
        $rows=$this->Select($table,$field,$where,$param,$order,$limit);
        $values=array();
        foreach($rows as $row){
            if(array_key_exists($field,$row)){
                $values[]=$row[$field];
            }
        }
        return $values;
    }

    /**
     * 是否存在符合条件的记录
     * @return bool
     */
    public function Exists($table,$where='',$param=array()){
        return $this->Select($table,'1',$where,$param,'','1')!==array();
    }

    /**
     * 分页查询
     * @param string|array $where
     * @param array $param
     * @param int $page 从 1 开始
     * @param int $size 每页条数，超出 1-1000 会夹到边界
     * @param string $field
     * @param string $order
     * @return array rows/total/page/size/pages
     */
    public function page($table,$where='',$param=array(),$page=1,$size=20,$field='*',$order=''){
        $page=(int)$page;
        $size=(int)$size;
        if($page<1){
            $page=1;
        }
        if($size<1){
            $size=20;
        }
        if($size>1000){
            $size=1000;
        }
        $total=$this->Count($table,$where,$param);
        $rows=array();
        if($total>0){
            $rows=$this->Select($table,$field,$where,$param,$order,(($page-1)*$size).",".$size);
        }
        return array(
            "rows"=>$rows,
            "total"=>$total,
            "page"=>$page,
            "size"=>$size,
            "pages"=>$total>0 ? (int)ceil($total/$size) : 0
        );
    }

    /**
     * 写入一行
     * @return int|bool
     */
    public function Insert($table,array $data){
        return $this->doInsert(Guard::Table($table),Guard::Data($data));
    }

    /**
     * 批量写入：单条 SQL 多值，整体走事务
     * @param array $rows 二维数组，列名以首行为准，后续行缺列按 NULL
     * @return int|bool 写入条数，失败 false
     */
    public function BatchInsert($table,array $rows){
        $table=Guard::Table($table);
        if(empty($rows)){
            return 0;
        }
        $first=reset($rows);
        if(!is_array($first) || empty($first)){
            throw new \InvalidArgumentException("批量写入的首行必须是字段/值数组");
        }
        $columns=array_keys($first);
        foreach($columns as $column){
            Guard::Column($column);
        }
        $values=array();
        $slots=array();
        foreach($rows as $row){
            if(!is_array($row)){
                throw new \InvalidArgumentException("批量写入的每一行都必须是数组");
            }
            $one=array();
            foreach($columns as $column){
                $value=array_key_exists($column,$row) ? $row[$column] : null;
                $one[]="?";
                $values[]=is_bool($value) ? (int)$value : $value;
            }
            $slots[]="(".implode(",",$one).")";
        }
        $sql="insert into ".$table." (".implode(",",$columns).") values ".implode(",",$slots);
        return $this->Transaction(function() use ($sql,$values,$rows){
            $result=$this->Execute($sql,$values);
            return $result===false ? false : count($rows);
        });
    }

    /**
     * 更新
     * @return bool
     */
    public function Update($table,array $data,$where='',$param=array()){
        $cond=Guard::Where($where,$param);
        return $this->doUpdate(Guard::Table($table),Guard::Data($data),Guard::Required($cond["sql"],"更新"),$cond["param"]);
    }

    /**
     * 删除
     * @return bool
     */
    public function Delete($table,$where='',$param=array()){
        $cond=Guard::Where($where,$param);
        return $this->doDelete(Guard::Table($table),Guard::Required($cond["sql"],"删除"),$cond["param"]);
    }

    /**
     * 字段自增，$step 为负数即自减
     * @return bool
     */
    public function Increment($table,$field,$step=1,$where='',$param=array()){
        $field=Guard::Column($field);
        if(!is_numeric($step)){
            throw new \InvalidArgumentException("步长必须是数字：".$step);
        }
        $cond=Guard::Where($where,$param);
        return $this->doIncrement(Guard::Table($table),$field,$step+0,Guard::Required($cond["sql"],"自增/自减"),$cond["param"]);
    }

    /**
     * 字段自减
     * @return bool
     */
    public function Decrement($table,$field,$step=1,$where='',$param=array()){
        return $this->Increment($table,$field,-abs($step+0),$where,$param);
    }

    /**
     * 查询类语句直通
     * @return array
     */
    public function Query($sql,array $param=array()){
        $stmt=Guard::Params($sql,$param);
        return $this->doQuery($stmt["sql"],$stmt["param"]);
    }

    /**
     * 写类语句直通
     * @return int|bool
     */
    public function Execute($sql,array $param=array()){
        $stmt=Guard::Params($sql,$param);
        return $this->doExecute($stmt["sql"],$stmt["param"]);
    }

    /**
     * 事务包裹：回调返回 false 回滚并返回 false；抛异常回滚后继续抛出；
     * 其余返回值提交并原样返回。回调接收本适配器实例。
     * @return mixed
     */
    public function Transaction(callable $fn){
        $this->begin();
        try{
            $result=$fn($this);
            if($result===false){
                $this->rollback();
                return false;
            }
            $this->commit();
            return $result;
        }catch(\Throwable $e){
            $this->rollback();
            throw $e;
        }
    }

    /**
     * 查询多行（子类实现）
     */
    abstract protected function doSelect($table,$field,$where,array $param,$order,$limit);

    /**
     * 统计条数（子类实现）
     */
    abstract protected function doCount($table,$where,array $param);

    /**
     * 写入一行（子类实现）
     */
    abstract protected function doInsert($table,array $data);

    /**
     * 更新（子类实现）
     */
    abstract protected function doUpdate($table,array $data,$where,array $param);

    /**
     * 删除（子类实现）
     */
    abstract protected function doDelete($table,$where,array $param);

    /**
     * 自增（子类实现）
     */
    abstract protected function doIncrement($table,$field,$step,$where,array $param);

    /**
     * 查询类直通（子类实现）
     */
    abstract protected function doQuery($sql,array $param);

    /**
     * 写类直通（子类实现）
     */
    abstract protected function doExecute($sql,array $param);

    /**
     * 开启事务
     */
    abstract protected function begin();

    /**
     * 提交事务
     */
    abstract protected function commit();

    /**
     * 回滚事务
     */
    abstract protected function rollback();
}