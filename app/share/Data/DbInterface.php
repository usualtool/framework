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
     * 写入一行
     * @return int|bool 自增 ID（驱动不支持时为 true），失败 false
     */
    public function Insert($table,array $data);

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