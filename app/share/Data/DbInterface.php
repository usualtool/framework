<?php
/**
 * 数据库适配层契约
 *
 * 文件路径：app/share/Data/DbInterface.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\DbInterface
 * 引用方式：use Share\Data\DbInterface;
 */
namespace Share\Data;

interface DbInterface{

    /**
     * 链式查询入口，返回绑定到本适配器的构造器
     *   $db->table("cms_goods")->field("id,title")->where(array("status"=>1))->order("id desc")->limit(10)->get();
     * @param string $table 表名
     * @return Query
     */
    public function table($table);

    /**
     * 查询多行
     * @param string $table 表名
     * @param string|array $field 字段
     * @param string|array $where 条件串（可含 ?）或条件数组
     * @param array $param 条件串的绑定值
     * @param string $order 排序
     * @param string|int $limit 条数或 “偏移,条数”
     * @return array 行数组
     */
    public function Select($table,$field,$where,$param,$order,$limit);

    /**
     * 统计条数
     * @return int
     */
    public function Count($table,$where,$param);

    /**
     * 查询单行，无数据返回 null
     * @return array|null
     */
    public function First($table,$field='*',$where='',$param=array(),$order='');

    /**
     * 取单行单列的值，$field 只允许单个字段名
     * @return mixed|null
     */
    public function Value($table,$field,$where='',$param=array(),$order='');

    /**
     * 取单列的一维数组
     * @return array
     */
    public function Column($table,$field,$where='',$param=array(),$order='',$limit='');

    /**
     * 是否存在符合条件的记录
     * @return bool
     */
    public function Exists($table,$where='',$param=array());

    /**
     * 分页查询，$page 从 1 开始，$size 超出 1-1000 会夹到边界
     * @return array rows/total/page/size/pages
     */
    public function page($table,$where='',$param=array(),$page=1,$size=20,$field='*',$order='');

    /**
     * 写入一行
     * @return int|bool 自增 ID（驱动不支持时为 true），失败 false
     */
    public function Insert($table,array $data);

    /**
     * 批量写入：单条 SQL 多值，整体走事务
     * @param array $rows 二维数组，列名以首行为准，后续行缺列按 NULL
     * @return int|bool 写入条数，失败 false
     */
    public function BatchInsert($table,array $rows);

    /**
     * 更新
     * @return bool
     */
    public function Update($table,array $data,$where,$param);

    /**
     * 删除
     * @return bool
     */
    public function Delete($table,$where,$param);

    /**
     * 字段自增，$step 为负数即自减
     * @return bool
     */
    public function Increment($table,$field,$step,$where,$param);

    /**
     * 字段自减
     * @return bool
     */
    public function Decrement($table,$field,$step=1,$where='',$param=array());

    /**
     * 查询类语句直通（JOIN、聚合、information_schema）
     * @return array
     */
    public function Query($sql,array $param);

    /**
     * 写类语句直通（DDL、批量插入）
     * @return int|bool 自增 ID 或 true，失败 false
     */
    public function Execute($sql,array $param);

    /**
     * 事务包裹：回调返回 false 回滚，抛异常回滚后继续抛出
     * @return mixed
     */
    public function Transaction(callable $fn);

    /**
     * 适配器标识：mysql / pdo / mssql / pgsql / sqlite / mock
     * @return string
     */
    public function Name();
}