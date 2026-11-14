<?php
/**
 * 结构校验与条件拼装
 *
 * 文件路径：app/share/Data/Guard.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\Guard
 * 引用方式：use Share\Data\Guard;
 * 
 * 条件数组语法
 *   ['id'=>1]                        → id=?
 *   ['num'=>['>=',10]]               → num>=?
 *   ['id'=>[1,2,3]]                  → id in (?,?,?)
 *   ['id'=>['in',[1,2]]]             → id in (?,?)
 *   ['age'=>['between',[18,30]]]     → age between ? and ?
 *   ['t'=>['like','%a%']]            → t like ?
 *   ['d'=>null] / ['d'=>['null']]    → d is null
 *   ['d'=>['not null']]              → d is not null
 */
namespace Share\Data;

class Guard{

    /**
     * 表名白名单：只允许字母、数字、下划线
     * @param string $table
     * @return string
     */
    public static function Table($table){
        $table=trim((string)$table);
        if(!preg_match('/^[A-Za-z0-9_]+$/',$table)){
            throw new \InvalidArgumentException("非法表名：".$table."（跨库前缀请改用 Query 或 Execute 直通）");
        }
        return $table;
    }

    /**
     * 字段列表白名单：支持 *、a.*、别名、逗号列表；不支持函数
     * @param string|array $field
     * @return string
     */
    public static function Field($field){
        if(is_array($field)){
            $field=implode(",",$field);
        }
        $field=trim((string)$field);
        if($field===""){
            return "*";
        }
        $unit='(?:\*|[A-Za-z0-9_]+(?:\.[A-Za-z0-9_]+|\.\*)?)';
        $re='/^\s*'.$unit.'(?:\s+(?:as\s+)?[A-Za-z0-9_]+)?(?:\s*,\s*'.$unit.'(?:\s+(?:as\s+)?[A-Za-z0-9_]+)?)*\s*$/i';
        if(!preg_match($re,$field)){
            $hint=strpos($field,"(")!==false ? "（聚合函数请用 Count 或 Query）" : "";
            throw new \InvalidArgumentException("非法字段：".$field.$hint);
        }
        return $field;
    }

    /**
     * 单个字段名白名单，不允许 * 与函数
     * @param string $column
     * @return string
     */
    public static function Column($column){
        $column=trim((string)$column);
        if(!preg_match('/^[A-Za-z0-9_]+(\.[A-Za-z0-9_]+)?$/',$column)){
            throw new \InvalidArgumentException("非法字段名：".$column);
        }
        return $column;
    }

    /**
     * 排序白名单：字段 + 可选 asc/desc，逗号分隔
     * @param string $order
     * @return string
     */
    public static function Order($order){
        $order=trim((string)$order);
        if($order===""){
            return "";
        }
        $unit='[A-Za-z0-9_.]+(?:\s+(?:asc|desc))?';
        if(!preg_match('/^\s*'.$unit.'(\s*,\s*'.$unit.')*\s*$/i',$order)){
            throw new \InvalidArgumentException("非法排序：".$order);
        }
        return $order;
    }

    /**
     * 条数白名单：数字或 “偏移,条数”
     * @param string|int $limit
     * @return string
     */
    public static function Limit($limit){
        if($limit==='' || $limit===null){
            return "";
        }
        $limit=is_int($limit) ? (string)$limit : trim((string)$limit);
        if(!preg_match('/^\d+(\s*,\s*\d+)?$/',$limit)){
            throw new \InvalidArgumentException("非法条数：".$limit);
        }
        return $limit;
    }

    /**
     * 条件归一化：字符串条件校验 ? 与绑定值数量，数组条件展开为 SQL + 值
     * @param string|array $where
     * @param array $param 仅配合字符串条件
     * @return array sql/param
     */
    public static function Where($where,array $param=array()){
        if(is_string($where)){
            $sql=trim($where);
            $param=array_values($param);
            $slots=substr_count($sql,"?");
            if($slots!==count($param)){
                throw new \InvalidArgumentException("条件里 ? 有 ".$slots." 个，绑定值有 ".count($param)." 个，数量必须一致");
            }
            return array("sql"=>$sql,"param"=>$param);
        }
        if(!is_array($where)){
            throw new \InvalidArgumentException("条件只接受字符串或数组");
        }
        if(!empty($param)){
            throw new \InvalidArgumentException("条件为数组时不要另传 param");
        }
        $parts=array();
        $values=array();
        foreach($where as $column=>$value){
            self::Cond(self::Column($column),$value,$parts,$values);
        }
        return array("sql"=>implode(" and ",$parts),"param"=>$values);
    }

    /**
     * 写入数据校验 + 布尔归一化
     * @param array $data
     * @return array
     */
    public static function Data(array $data){
        if(empty($data)){
            throw new \InvalidArgumentException("写入数据不能为空");
        }
        foreach(array_keys($data) as $column){
            self::Column($column);
        }
        return self::Normalize($data);
    }

    /**
     * 布尔值转 0/1，其余原样
     * @param array $data
     * @return array
     */
    public static function Normalize(array $data){
        foreach($data as $key=>$value){
            if(is_bool($value)){
                $data[$key]=(int)$value;
            }
        }
        return $data;
    }

    /**
     * 写操作必须带条件，空条件抛异常，避免误改全表
     * @param string $sql
     * @param string $action 动作名，用于异常提示
     * @return string
     */
    public static function Required($sql,$action){
        $sql=trim((string)$sql);
        if($sql===""){
            throw new \InvalidArgumentException($action."必须指定条件");
        }
        return $sql;
    }

    /**
     * 直通语句校验：非空 + ? 数量与绑定值一致
     * @param string $sql
     * @param array $param
     * @return array sql/param
     */
    public static function Params($sql,array $param=array()){
        $sql=trim((string)$sql);
        if($sql===""){
            throw new \InvalidArgumentException("语句不能为空");
        }
        $param=array_values($param);
        $slots=substr_count($sql,"?");
        if($slots!==count($param)){
            throw new \InvalidArgumentException("语句里 ? 有 ".$slots." 个，绑定值有 ".count($param)." 个，数量必须一致");
        }
        return array("sql"=>$sql,"param"=>$param);
    }

    /**
     * 判断是否为查询类语句
     * @param string $sql
     * @return bool
     */
    public static function Schema($sql){
        return preg_match('/^(select|with|show|desc|describe|explain|pragma)\b/i',trim((string)$sql))===1;
    }

    /**
     * 单字段条件展开
     */
    private static function Cond($column,$value,array &$parts,array &$values){
        if(is_array($value)){
            if($value===array()){
                $parts[]="1=0";
                return;
            }
            if(array_keys($value)!==array_keys(array_values($value))){
                throw new \InvalidArgumentException("字段 ".$column." 的条件写法有误，应为 [运算符,值] 或值列表");
            }
            $first=is_string($value[0]) ? strtolower(trim($value[0])) : null;
            if($first!==null && count($value)<=2 && in_array($first,self::Ops(),true)){
                self::CondOp($column,$first,count($value)>1 ? $value[1] : null,$parts,$values);
                return;
            }
            $list=array();
            foreach($value as $item){
                $list[]=is_bool($item) ? (int)$item : $item;
            }
            $parts[]=$column." in (".implode(",",array_fill(0,count($list),"?")).")";
            $values=array_merge($values,$list);
            return;
        }
        if($value===null){
            $parts[]=$column." is null";
            return;
        }
        $parts[]=$column."=?";
        $values[]=is_bool($value) ? (int)$value : $value;
    }

    /**
     * 运算符条件展开
     */
    private static function CondOp($column,$op,$value,array &$parts,array &$values){
        if($op==="=" || $op==="is"){
            if($value===null){
                $parts[]=$column." is null";
                return;
            }
            $parts[]=$column."=?";
            $values[]=is_bool($value) ? (int)$value : $value;
            return;
        }
        if($op==="!=" || $op==="<>" || $op==="is not"){
            if($value===null){
                $parts[]=$column." is not null";
                return;
            }
            $parts[]=$column."<>?";
            $values[]=is_bool($value) ? (int)$value : $value;
            return;
        }
        if($op===">" || $op===">=" || $op==="<" || $op==="<=" || $op==="like" || $op==="not like"){
            $parts[]=$column." ".$op." ?";
            $values[]=is_bool($value) ? (int)$value : $value;
            return;
        }
        if($op==="in" || $op==="not in"){
            if(!is_array($value)){
                throw new \InvalidArgumentException("字段 ".$column." 的 ".$op." 条件需要数组值");
            }
            if($value===array()){
                $parts[]=$op==="in" ? "1=0" : "1=1";
                return;
            }
            $list=array();
            foreach($value as $item){
                $list[]=is_bool($item) ? (int)$item : $item;
            }
            $parts[]=$column." ".$op." (".implode(",",array_fill(0,count($list),"?")).")";
            $values=array_merge($values,$list);
            return;
        }
        if($op==="between"){
            if(!is_array($value) || count($value)!==2){
                throw new \InvalidArgumentException("字段 ".$column." 的 between 条件需要两个值");
            }
            $items=array_values($value);
            $parts[]=$column." between ? and ?";
            $values[]=is_bool($items[0]) ? (int)$items[0] : $items[0];
            $values[]=is_bool($items[1]) ? (int)$items[1] : $items[1];
            return;
        }
        if($op==="null" || $op==="not null"){
            $parts[]=$op==="null" ? $column." is null" : $column." is not null";
            return;
        }
        throw new \InvalidArgumentException("字段 ".$column." 的条件运算符不支持：".$op);
    }

    /**
     * 条件运算符白名单
     */
    private static function Ops(){
        static $ops=array("=","!=","<>",">",">=","<","<=","like","not like","in","not in","between","is","is not","null","not null");
        return $ops;
    }
}