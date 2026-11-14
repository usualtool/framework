<?php
/**
 * 数据库适配层静态入口
 *
 * 文件路径：app/share/Data/Db.php
 * 命名空间：Share\Data
 * 完整类名：Share\Data\Db
 * 引用方式：use Share\Data\Db;
 *
 * 用法
 *   $id  = Db::Insert("cms_goods", array("title"=>$title,"price"=>9.9));
 *   $rows= Db::Select("cms_goods","id,title",array("status"=>1),"","id desc","0,10");
 *   $page= Db::table("cms_goods")->where(array("status"=>1))->page($page,20);
 *   注入场景：$db->table("cms_goods")->where(array("status"=>1))->get();  // 实例上直接链式
 *   测试：Db::Use(new MockDb()); ... Db::Reset();
 */
namespace Share\Data;

use usualtool\Lib\App;

class Db{

    /**
     * 显式注入的适配器，null 表示按解析顺序取默认
     */
    private static $db=null;

    /**
     * 兜底适配器（UtDb），仅在容器与显式注入都未命中时使用
     */
    private static $default=null;

    /**
     * 显式注入适配器
     * @param DbInterface $db
     */
    public static function Use(DbInterface $db){
        self::$db=$db;
    }

    /**
     * 还原默认解析
     */
    public static function Reset(){
        self::$db=null;
        self::$default=null;
    }

    /**
     * 临时注入执行一段逻辑，结束后自动还原（含异常），测试隔离首选
     * @param DbInterface $db
     * @param callable $fn 可接收适配器参数
     * @return mixed
     */
    public static function With(DbInterface $db,callable $fn){
        $previous=self::$db;
        self::$db=$db;
        try{
            return $fn($db);
        }finally{
            self::$db=$previous;
        }
    }

    /**
     * 当前适配器
     * @return DbInterface
     */
    public static function Instance(){
        if(self::$db!==null){
            return self::$db;
        }
        if(class_exists("usualtool\\Lib\\App") && App::Has(DbInterface::class)){
            return App::Make(DbInterface::class);
        }
        if(self::$default===null){
            self::$default=new UtDb();
        }
        return self::$default;
    }

    /**
     * 适配器标识
     * @return string
     */
    public static function Driver(){
        return (string)self::Instance()->Name();
    }

    /**
     * 链式构造器入口
     * @param string $table
     * @return Query
     */
    public static function table($table){
        return new Query(self::Instance(),$table);
    }

    /**
     * 查询多行
     * @return array
     */
    public static function Select($table,$field='*',$where='',$param=array(),$order='',$limit=''){
        return self::Instance()->Select($table,$field,$where,$param,$order,$limit);
    }

    /**
     * Select 的别名
     * @return array
     */
    public static function Get($table,$field='*',$where='',$param=array(),$order='',$limit=''){
        return self::Instance()->Select($table,$field,$where,$param,$order,$limit);
    }

    /**
     * 查询单行
     * @return array|null
     */
    public static function First($table,$field='*',$where='',$param=array(),$order=''){
        return self::Instance()->First($table,$field,$where,$param,$order);
    }

    /**
     * 取单值
     * @return mixed|null
     */
    public static function Value($table,$field,$where='',$param=array(),$order=''){
        return self::Instance()->Value($table,$field,$where,$param,$order);
    }

    /**
     * 取整列
     * @return array
     */
    public static function Column($table,$field,$where='',$param=array(),$order='',$limit=''){
        return self::Instance()->Column($table,$field,$where,$param,$order,$limit);
    }

    /**
     * 统计条数
     * @return int
     */
    public static function Count($table,$where='',$param=array()){
        return self::Instance()->Count($table,$where,$param);
    }

    /**
     * 是否存在
     * @return bool
     */
    public static function Exists($table,$where='',$param=array()){
        return self::Instance()->Exists($table,$where,$param);
    }

    /**
     * 分页
     * @return array
     */
    public static function page($table,$where='',$param=array(),$page=1,$size=20,$field='*',$order=''){
        return self::Instance()->page($table,$where,$param,$page,$size,$field,$order);
    }

    /**
     * 写入一行
     * @return int|bool
     */
    public static function Insert($table,array $data){
        return self::Instance()->Insert($table,$data);
    }

    /**
     * 批量写入
     * @return int|bool
     */
    public static function BatchInsert($table,array $rows){
        return self::Instance()->BatchInsert($table,$rows);
    }

    /**
     * 更新
     * @return bool
     */
    public static function Update($table,array $data,$where='',$param=array()){
        return self::Instance()->Update($table,$data,$where,$param);
    }

    /**
     * 删除
     * @return bool
     */
    public static function Delete($table,$where='',$param=array()){
        return self::Instance()->Delete($table,$where,$param);
    }

    /**
     * 字段自增
     * @return bool
     */
    public static function Increment($table,$field,$step=1,$where='',$param=array()){
        return self::Instance()->Increment($table,$field,$step,$where,$param);
    }

    /**
     * 字段自减
     * @return bool
     */
    public static function Decrement($table,$field,$step=1,$where='',$param=array()){
        return self::Instance()->Decrement($table,$field,$step,$where,$param);
    }

    /**
     * 查询类语句直通
     * @return array
     */
    public static function Query($sql,array $param=array()){
        return self::Instance()->Query($sql,$param);
    }

    /**
     * 写类语句直通
     * @return int|bool
     */
    public static function Execute($sql,array $param=array()){
        return self::Instance()->Execute($sql,$param);
    }

    /**
     * 直通，按语句首词自动分派：select/with/show/desc/explain 走查询，其余走执行
     * @param string $sql
     * @param array $param
     * @return array|int|bool
     */
    public static function Raw($sql,array $param=array()){
        return Guard::Schema($sql) ? self::Instance()->Query($sql,$param) : self::Instance()->Execute($sql,$param);
    }

    /**
     * 事务
     * @return mixed
     */
    public static function Transaction(callable $fn){
        return self::Instance()->Transaction($fn);
    }
}