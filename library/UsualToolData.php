<?php
namespace library\UsualToolData;
use library\UsualToolInc;
use library\UsualToolPdo;
use library\UsualToolMysql;
use library\UsualToolMssql;
use library\UsualToolPgsql;
use library\UsualToolSqlite;
use library\UsualToolMongo;
use library\UsualToolRedis;
use library\UsualToolMemcache;
/**
       * --------------------------------------------------------       
       *  |    ░░░░░░░░░     █   █░▀▀█▀▀░    ░░░░░░░░░      |           
       *  |  ░░░░░░░         █▄▄▄█   █                      |            
       *  |                                                 |            
       *  | Author:HuangDou   Email:292951110@qq.com        |            
       *  | QQ-Group:583610949                              |           
       *  | WebSite:http://www.UsualTool.com                |            
       *  | UT Framework is suitable for Apache2 protocol.  |            
       * --------------------------------------------------------                
 */
/**
 * 统一操作数据
 */
class UTData{
    /**
     * 获取主数据库
     */
    public static function GetDb(){
        $config=UsualToolInc\UTInc::GetConfig();
        return $config["DBTYPE"];
    }
    /**
     * 连接数据库
     */
    public static function GetDatabase(){
        if(UTData::GetDb()=="pdo"){
            return UsualToolPdo\UTPdo::GetPdo();
        }elseif(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::GetMysql();
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMssql\UTMssql::GetMssql();
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::GetPgsql();
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::GetSqlite();
        }else{
            return false;
        }
    }
    /**
     * 判断表是否存在
     * @param string $table
     * @return bool
     */
    public static function ModTable($table){
        if(UTData::GetDb()=="pdo"){
            return UsualToolPdo\UTPdo::ModTable($table);
        }elseif(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::ModTable($table);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMssql\UTMssql::ModTable($table);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::ModTable($table);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::ModTable($table);
        }else{
            return false;
        }
    }
    /**
     * 执行SQL或命令
     * @param string $sql SQL语句/命令
     * @return bool
     */
    public static function RunSql($sql){
        if(UTData::GetDb()=="pdo"){
            return UsualToolPdo\UTPdo::RunSql($sql);
        }elseif(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::RunSql($sql);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMysql\UTMssql::RunSql($sql);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::RunSql($table);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::RunSql($table);
        }else{
            return false;
        }
    }
    /**
     * 查询数据
     * @param string $table 被查询表名
     * @param string $field 查询字段，多个字段以‘,’分割
     * @param string $where 查询条件
     * @param string $order 排序方式，例：id desc/id asc
     * @param string|int $limit 数据显示数目，例：0,5/1
     * @param string $lang 是否开启语言识别，默认0关闭，当需要开启时，该参数填写>0的数字，自动获取全局的语言参数，也可以直接填写语言参数zh/en/ja等
     * @param string $cache 是否开启缓存，默认0关闭，需要开启时，该参数填写key名称
     * @return array 返回数组，例：array("querydata"=>array(),"curnum"=>0,"querynum"=>0)
     */
    public static function QueryData($table,$field='',$where='',$order='',$limit='',$lang='0',$cache='0'){
        if($cache==0){
            if(UTData::GetDb()=="pdo"){
                $data=UsualToolPdo\UTPdo::QueryData($table,$field,$where,$order,$limit,$lang);
            }elseif(UTData::GetDb()=="mysql"){
                $data=UsualToolMysql\UTMysql::QueryData($table,$field,$where,$order,$limit,$lang);
            }elseif(UTData::GetDb()=="mssql"){
                $data=UsualToolMssql\UTMssql::QueryData($table,$field,$where,$order,$limit,$lang);
            }elseif(UTData::GetDb()=="pgsql"){
                $data=UsualToolPgsql\UTPgsql::QueryData($table,$field,$where,$order,$limit,$lang);
            }elseif(UTData::GetDb()=="sqlite"){
                $data=UsualToolSqlite\UTSqlite::QueryData($table,$field,$where,$order,$limit,$lang);
            }else{
                $data=array();
            }
            return $data;
        }else{
            UTData::GetCache($table,$field,$where,$order,$limit,$lang,$cache);
        }
    }
    /**
     * 创建数据
     * @param string $table 表名
     * @param array $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @return bool 当结果为真时返回最新添加的记录id
     */
    public static function InsertData($table,$data){
        if(UTData::GetDb()=="pdo"){
            return UsualToolPdo\UTPdo::InsertData($table,$data);
        }elseif(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::InsertData($table,$data);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMysql\UTMssql::InsertData($table,$data);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::InsertData($table,$data);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::InsertData($table,$data);
        }else{
            return false;
        }
    }
    /**
     * 更新数据
     * @param string $table 表名
     * @param array $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @param string $where 条件
     * @return bool
     */
    public static function UpdateData($table,$data,$where){
        if(UTData::GetDb()=="pdo"){
            return UsualToolPdo\UTPdo::UpdateData($table,$data,$where);
        }elseif(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::UpdateData($table,$data,$where);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMysql\UTMssql::UpdateData($table,$data,$where);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::UpdateData($table,$data,$where);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::UpdateData($table,$data,$where);
        }else{
            return false;
        }
    }
    /**
     * 删除数据
     * @param string $table 表名
     * @param string $where 条件
     * @return bool
     */
    public static function DelData($table,$where){
        if(UTData::GetDb()=="pdo"){
            return UsualToolPdo\UTPdo::DelData($table,$where);
        }elseif(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::DelData($table,$where);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMysql\UTMssql::DelData($table,$where);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::DelData($table,$where);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::DelData($table,$where);
        }else{
            return false;
        }
    }
    /**
     * 获取数据标签
     * @param string $table 表名
     * @param string $field 标签字段，只能为1个
     * @param string $where 条件
     * @param string $order 排序方式
     * @param string $lang 是否自动开启语言，默认0关闭
     * @param string $cache 是否开启redis缓存，默认0关闭，需要开启时，该参数填写key名称
     * @return array 返回数组，例：array('tags'=>$taglist)
     */
    public static function TagData($table,$field='',$where='',$order='',$lang='0'){
        if(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::TagData($table,$field,$where,$order,$lang);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMysql\UTMssql::TagData($table,$field,$where,$order,$lang);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::TagData($table,$field,$where,$order,$lang);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::TagData($table,$field,$where,$order,$lang);
        }else{
            return array();
        }
    }
    /**
     * 获取数据首图
     * @param string $table 表名
     * @param string $field 检索字段，只能为1个
     * @param string $where 条件
     * @param string $cache 是否开启redis缓存，默认0关闭，需要开启时，该参数填写key名称
     * @return array 返回数组，在其数组中返回指定字段的第一张图片imageurl
     */
    public static function FigureData($table,$field,$where='',$limit=''){
        if(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::FigureData($table,$field,$where,$limit);
        }elseif(UTData::GetDb()=="mssql"){
            return UsualToolMysql\UTMssql::FigureData($table,$field,$where,$limit);
        }elseif(UTData::GetDb()=="pgsql"){
            return UsualToolPgsql\UTPgsql::FigureData($table,$field,$where,$limit);
        }elseif(UTData::GetDb()=="sqlite"){
            return UsualToolSqlite\UTSqlite::FigureData($table,$field,$where,$limit);
        }else{
            return array();
        }
    }
    /**
     * 搜索数据
     * @param string $keyword 关键词
     * @return array 返回数组
     */
    public static function SearchData($keyword){
        if(UTData::GetDb()=="mysql"){
            return UsualToolMysql\UTMysql::SearchData($keyword);
        }else{
            return array();
        }	
    }
    /**
     * 获取及更新缓存
     * @param string $cache 键或元素
     * @param string $data 数据
     * @return array
     */
    public static function GetCache($table,$field,$where,$order,$limit,$lang,$cache){
        $config=UsualToolInc\UTInc::GetConfig();
        $dbcache=$config["DBCACHE"];
        if($dbcache=="redis"):
            if(UsualToolRedis\UTRedis::ModTable($cache)):
                return UsualToolRedis\UTRedis::QueryData($cache);
            else:
                $data=UTData::QueryData($table,$field,$where,$order,$limit,$lang,0);
                UsualToolRedis\UTRedis::InsertData($cache,$data,1);
                return $data;
            endif;
        elseif($dbcache=="mongo"):
            if(UsualToolMongo\UTMongo::ModTable($cache)):
                return UsualToolMongo\UTMongo::QueryData($cache);
            else:
                $data=UTData::QueryData($table,$field,$where,$order,$limit,$lang,0);
                UsualToolMongo\UTMongo::InsertData($cache,$data);
                return $data;
            endif;
        elseif($dbcache=="memcache"):
            if(UsualToolMemcache\UTMemcache::ModTable($cache)):
                return UsualToolMemcache\UTMemcache::QueryData($cache);
            else:
                $data=UTData::QueryData($table,$field,$where,$order,$limit,$lang,0);
                UsualToolMemcache\UTMemcache::InsertData($cache,$data,1);
                return $data;
            endif;
        else:
            return array();
        endif;
    }
    /**
     * 获取记录数目
     * @param string $sql SQL语句
     * @return int 
     */
    public static function QueryNum($sql){
            if(UTData::GetDb()=="pdo"){
                $data=UsualToolPdo\UTPdo::QueryNum($sql);
            }elseif(UTData::GetDb()=="mysql"){
                $data=UsualToolMysql\UTMysql::QueryNum($sql);
            }elseif(UTData::GetDb()=="mssql"){
                $data=UsualToolMssql\UTMssql::QueryNum($sql);
            }elseif(UTData::GetDb()=="pgsql"){
                $data=UsualToolPgsql\UTPgsql::QueryNum($sql);
            }elseif(UTData::GetDb()=="sqlite"){
                $data=UsualToolSqlite\UTSqlite::QueryNum($sql);
            }else{
                $data=array();
            }
            return $data;
    }
}