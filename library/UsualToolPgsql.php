<?php
namespace library\UsualToolPgsql;
use library\UsualToolInc;
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
 * 操作PostgreSQL
 */
class UTPgsql{   
    static $cursor=0;
    /**
     * 连接PostgreSQL
     */    
    public static function GetPgsql(){
        $config=UsualToolInc\UTInc::GetConfig();
        $db=@pg_connect("host=".$config["PGSQL_HOST"]." port=".$config["PGSQL_PORT"]." dbname=".$config["PGSQL_DB"]." user=".$config["PGSQL_USER"]." password=".$config["PGSQL_PASS"]);
        if(!$db){
            return false;
        }else{
            return $db;
        }
    }
    /**
     * 判断表是否存在
     * @param string $table
     * @return bool
     */
    public static function ModTable($table){
        $db=UTPgsql::GetPgsql();
        $result=@pg_field_table(@pg_query($db,"select * from ".$table),0,true);
        if(!$result){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 执行SQL语句
     * @param string $sql SQL语句
     * @return bool
     */
    public static function RunSql($sql){
		$db=UTPgsql::GetPgsql();
        $result = @pg_execute($db,$sql);
        if(!$result){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 查询数据
     * @param string $table 被表名
     * @param string $field 查询字段，多个字段以‘,’分割
     * @param string $where 查询条件
     * @param string $order 排序方式，例：id desc/id asc
     * @param string|int $limit 数据显示数目，例：10
     * @param string $lang 是否开启语言识别
     * @return array 返回数组，例：array("querydata"=>array(),"curnum"=>0,"querynum"=>0)
     */
    public static function QueryData($table,$field='',$where='',$order='',$limit='',$lang='0'){
        $field=empty($field) ? "*" : $field;
        if($lang!="0"):
            if(is_numeric($lang)):
                $where=empty($where) ? "where lang='$language'" : "where lang='$language' and ".$where;
            else:
                $where=empty($where) ? "where lang='$lang'" : "where lang='$lang' and ".$where;
            endif;
        else:
            $where=empty($where) ? "" : "where ".$where;
        endif;
        $order=empty($order) ? "" : "order by ".$order;
        $limit=empty($limit) ? "" : "limit ".$limit;
        $sql="select ".$field." from ".$table." ".$where." ".$order." ".$limit;
		$db=UTPgsql::GetPgsql();
        $array = array();
        $result = @pg_query($db,$sql);
        $curnum=@pg_num_rows($result);
        $querynum=empty($limit) ? $curnum : UTPgsql::QueryNum("select ".$field." from ".$table." ".$where." ".$order);
        while($rows=@pg_fetch_object($result)){
            $array[] = UTPgsql::ObjectToArray($rows);
        }
        $data=array("querydata"=>$array,"curnum"=>$curnum,"querynum"=>$querynum);
        return $data;
    }
    /**
     * 新增数据
     * @param string $table 被表名
     * @param string $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @return bool 
     */
    public static function InsertData($table,$data){
        $db=UTPgsql::GetPgsql();
        $query=@pg_insert($db,$table,$data);
        if($query):
            return true;
        else:
            return false;
        endif;
    }
    /**
     * 更新数据
     * @param string $table 表名
     * @param array $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @param string $where 条件
     * @return bool
     */
    public static function UpdateData($table,$data,$where){
        $db=UTPgsql::GetPgsql();
        $updatestr='';
        if(!empty($data)):
            foreach($data as $k=>$v):
                if(preg_match('/\+\d/is',$v)):
                    $updatestr.=$k."=".$v.",";
                else:
                    $updatestr.=$k."='".$v."',";
                endif;
            endforeach;
            $updatestr=rtrim($updatestr,',');
        endif;
        $sql="update ".$table." set ".$updatestr." where ".$where;
        $query=@pg_query($db,$sql);
        if($query):
            return true;
        else:
            return false;
        endif;
    }
    /**
     * 删除数据
     * @param string $table 表名
     * @param string $where 条件
     * @return bool
     */
    public static function DelData($table,$where){
        $db=UTPgsql::GetPgsql();
        $sql="delete from ".$table." where ".$where;
        $query=@pg_query($db,$sql);
        if($query):
            return true;
        else:
            return false;
        endif;
    }
    /**
     * 获取数据标签
     * @param string $table 表名
     * @param string $field 标签字段，只能为1个
     * @param string $where 条件
     * @param string $order 排序方式
     * @param string $lang 是否自动开启语言，默认0关闭
     * @return array 返回数组，例：array('tags'=>$taglist)
     */
    public static function TagData($table,$field='',$where='',$order='',$lang='0'){
        global$language;
        $db=UTPgsql::GetPgsql();
        $tags="";
        $field=empty($field) ? "*" : $field;
        if($lang!="0"):
            if(is_numeric($lang)):
                $where=empty($where) ? "where lang='$language'" : "where lang='$language' and ".$where."";
            else:
                $where=empty($where) ? "where lang='$lang'" : "where lang='$lang' and ".$where."";
            endif;
        else:
            $where=empty($where) ? "" : "where ".$where."";
        endif;
        $order=empty($order) ? "" : "order by ".$order."";
        if(UTPgsql::ModTable($table)):
            $sql="select ".$field." from ".$table." ".$where." ".$order;
            $tag = @pg_query($db,$sql);
            while($rows=@pg_fetch_object($tag)):
                $rows=UTPgsql::ObjectToArray($rows);
                $tags="".$tags.",".$rows[$field];
            endwhile;
            $taglist=join(',',array_unique(array_diff(explode(",",$tags),array(""))));
            $taglists[]=array('tags'=>$taglist);
            return $taglists;
        else:
            return array();
        endif;
    }
    /**
     * 获取数据首图
     * @param string $table 表名
     * @param string $field 检索字段，只能为1个
     * @param string $where 条件
     * @return array 返回数组，在其数组中返回指定字段的第一张图片imageurl
     */
    public static function FigureData($table,$field,$where='',$limit=''){
        $db=UTPgsql::GetPgsql();
        $where=empty($where) ? "" : "where ".$where;
        $limit=empty($limit) ? "" : "limit ".$limit;
        if(UTPgsql::ModTable($table)):
            $sql="SELECT ".$field." from ".$table." ".$where." ".$limit;
            $query = @pg_query($db,$sql);
            $figuredata=array(); 
            while($rows=@pg_fetch_object($query)):
                $rows=UTPgsql::ObjectToArray($rows);
                $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.bmp|\.png]))[\'|\"].*?[\/]?>/";
                preg_match_all($pattern,$rows[$field],$matchcontent);
                $rows['imageurl']=isset($matchcontent[1][0]) ? $matchcontent[1][0] : '';
                $count=count($rows);
                for($i=0;$i<$count;$i++):
                    unset($rows[$i]);
                endfor;
                array_push($figuredata,$rows);
            endwhile;
            return $figuredata;
        else:
            return array();
        endif;
    }
    /**
     * 对象转数组
     * @param string $obj 对象
     * @return array
     */
    public static function ObjectToArray($obj){
        $ret = array();
        foreach($obj as $key => $value){
            if(is_array($value) || is_object($value)){
                $ret[$key] = UTPgsql::ObjectToArray($value);
            }else{
                $ret[$key] = $value;
            }
        }
        return $ret;
    }
    /**
     * 统计记录数目
     * @param string $sql SQL语句
     * @return int
     */
    public static function QueryNum($sql){
        $db=UTPgsql::GetPgsql();
        $query = @pg_query($db,$sql);
        return @pg_num_rows($query);
    }
    /**
     * 释放查询（pg_query）内存，终止结果
     * @return string 
     * @return bool
     */
    public static function FreeRes($res){
        return @pg_free_result($res);
    }
    /**
     * 返回最近的出错记录
     * @return string 
     */
    public static function ErrorMsg(){
        $db=UTPgsql::GetPgsql();
        return @pg_last_error($db);
    }
    /**
     * 获取PostgreSQL版本号
     * @return array 
     */
    public static function Version(){
        $db=UTPgsql::GetPgsql();
        return @pg_version($db);
    }
}