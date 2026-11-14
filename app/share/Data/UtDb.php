<?php
/**
 * 生产适配器：转发 usualtool\Lib\Data
 *
 * 文件路径：app/share/Data/UtDb.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\UtDb
 * 引用方式：use Share\Data\UtDb;
 */
namespace Share\Data;

use usualtool\Lib\Data;

class UtDb extends Adapter{

    /**
     * 适配器标识
     * @return string
     */
    public function Name(){
        return (string)Data::GetDb();
    }

    protected function doSelect($table,$field,$where,array $param,$order,$limit){
        return self::Rows(Data::QueryData($table,$field,$where,$order,$limit,array_values($param)));
    }

    protected function doCount($table,$where,array $param){
        $sql="select count(*) as uvnum from ".$table.($where==="" ? "" : " where ".$where);
        $rows=self::Rows(Data::RunYu($sql,array_values($param)));
        return empty($rows) ? 0 : (int)$rows[0]["uvnum"];
    }

    protected function doInsert($table,array $data){
        return Data::InsertData($table,$data);
    }

    protected function doUpdate($table,array $data,$where,array $param){
        return Data::UpdateData($table,$data,$where,array_values($param));
    }

    protected function doDelete($table,$where,array $param){
        return Data::DelData($table,$where,array_values($param));
    }

    protected function doIncrement($table,$field,$step,$where,array $param){
        $sql="update ".$table." set ".$field."=".$field."+? where ".$where;
        return Data::RunYu($sql,array_merge(array($step),array_values($param)));
    }

    protected function doQuery($sql,array $param){
        return self::Rows(Data::RunYu($sql,array_values($param)));
    }

    protected function doExecute($sql,array $param){
        return Data::RunYu($sql,array_values($param));
    }

    protected function begin(){
        $db=Data::GetDatabase();
        if($db instanceof \PDO){
            $db->beginTransaction();
            return true;
        }
        if($db instanceof \mysqli){
            $db->begin_transaction();
            return true;
        }
        Data::RunYu("begin");
        return true;
    }

    protected function commit(){
        $db=Data::GetDatabase();
        if($db instanceof \PDO){
            $db->commit();
            return true;
        }
        if($db instanceof \mysqli){
            $db->commit();
            return true;
        }
        Data::RunYu("commit");
        return true;
    }

    protected function rollback(){
        $db=Data::GetDatabase();
        if($db instanceof \PDO){
            $db->rollBack();
            return true;
        }
        if($db instanceof \mysqli){
            $db->rollback();
            return true;
        }
        Data::RunYu("rollback");
        return true;
    }

    /**
     * 统一返回口径：Data 的 querydata 结构或驱动直返的行数组都接受
     */
    private static function Rows($data){
        if(!is_array($data)){
            return array();
        }
        if(isset($data["querydata"]) && is_array($data["querydata"])){
            return $data["querydata"];
        }
        return $data;
    }
}