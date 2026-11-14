<?php
/**
 * 内存库适配器：PDO + SQLite，真实执行 SQL
 *
 * 文件路径：app/share/Data/SqliteDb.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\SqliteDb
 * 引用方式：use Share\Data\SqliteDb;
 *
 * 用途
 *   MockDb 只能断言“传了什么参数”，验证不了 SQL 本身对不对、约束冲突怎么处理。
 *   本适配器默认连 :memory:，建表、写入、查询、事务全部真实执行，不需要
 *   Mysql，也不需要 .ut.config，适合在 CI 或本地无库环境跑集成用例。
 *
 * 与生产的差异
 *   1. 原生错误以 PDOException 抛出，而 UtDb 走框架数据层会把失败吞成 false；
 *      需要断言失败行为的用例请显式 try/catch。
 *   2. 只支持 SQLite 方言：Mysql 专有函数（ifnull/date_format 等）需自行替换。
 *   3. 表名、字段、条件仍走 Guard 白名单，与线上同一道关。
 */
namespace Share\Data;

class SqliteDb extends Adapter{

    /**
     * PDO 连接
     */
    private $pdo;

    /**
     * 最近一次写的受影响行数
     */
    private $affected=0;

    /**
     * @param string $dsn 默认内存库，也可传 sqlite:/绝对路径/xx.db
     */
    public function __construct($dsn="sqlite::memory:"){
        $this->pdo=new \PDO($dsn);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE,\PDO::FETCH_ASSOC);
    }

    /**
     * 适配器标识
     * @return string
     */
    public function Name(){
        return "sqlite";
    }

    /**
     * 底层连接，供建表、PRAGMA 等特殊操作使用
     * @return \PDO
     */
    public function Pdo(){
        return $this->pdo;
    }

    /**
     * 最近一次写的受影响行数
     * @return int
     */
    public function Affected(){
        return (int)$this->affected;
    }

    /**
     * 最近一次写入的自增 ID
     * @return int
     */
    public function LastId(){
        return (int)$this->pdo->lastInsertId();
    }

    protected function doSelect($table,$field,$where,array $param,$order,$limit){
        $sql="select ".$field." from ".$table;
        if($where!==""){
            $sql.=" where ".$where;
        }
        if($order!==""){
            $sql.=" order by ".$order;
        }
        if($limit!==""){
            $sql.=" limit ".$limit;
        }
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_values($param));
        return $stmt->fetchAll();
    }

    protected function doCount($table,$where,array $param){
        $sql="select count(*) as uvnum from ".$table.($where==="" ? "" : " where ".$where);
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_values($param));
        $row=$stmt->fetch();
        return empty($row) ? 0 : (int)$row["uvnum"];
    }

    protected function doInsert($table,array $data){
        $columns=array_keys($data);
        $sql="insert into ".$table." (".implode(",",$columns).") values (".implode(",",array_fill(0,count($columns),"?")).")";
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $this->affected=$stmt->rowCount();
        $id=(int)$this->pdo->lastInsertId();
        return $id>0 ? $id : true;
    }

    protected function doUpdate($table,array $data,$where,array $param){
        $sets=array();
        foreach(array_keys($data) as $column){
            $sets[]=$column."=?";
        }
        $sql="update ".$table." set ".implode(",",$sets)." where ".$where;
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_merge(array_values($data),array_values($param)));
        $this->affected=$stmt->rowCount();
        return true;
    }

    protected function doDelete($table,$where,array $param){
        $stmt=$this->pdo->prepare("delete from ".$table." where ".$where);
        $stmt->execute(array_values($param));
        $this->affected=$stmt->rowCount();
        return true;
    }

    protected function doIncrement($table,$field,$step,$where,array $param){
        $sql="update ".$table." set ".$field."=".$field."+? where ".$where;
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_merge(array($step),array_values($param)));
        $this->affected=$stmt->rowCount();
        return true;
    }

    protected function doQuery($sql,array $param){
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_values($param));
        return $stmt->fetchAll();
    }

    protected function doExecute($sql,array $param){
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(array_values($param));
        $this->affected=$stmt->rowCount();
        return true;
    }

    protected function begin(){
        $this->pdo->beginTransaction();
        return true;
    }

    protected function commit(){
        $this->pdo->commit();
        return true;
    }

    protected function rollback(){
        if($this->pdo->inTransaction()){
            $this->pdo->rollBack();
        }
        return true;
    }
}