<?php
namespace library\UsualToolSqlite;
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
 * 操作Sqlite
 */
class UTSqlite{
    /**
     * 连接Sqlite
     */    
    public static function GetSqlite(){
        $config=UsualToolInc\UTInc::GetConfig();
        $db=new \SQLite3($config["SQLITE_DB"]);
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
        $db=UTSqlite::GetSqlite();
        $result=$db->query("select count(*)  from sqlite_master where type='table' and name = '".$table."';");
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
		$db=UTSqlite::GetSqlite();
        $result = $db->exec($sql);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取单个或多个数据
     * @param string $table 被表名
     * @param string $field 查询字段，多个字段以‘,’分割
     * @param string $where 查询条件
     * @param string $order 排序方式，例：id desc/id asc
     * @param string|int $limit 数据显示数目，例：10
     * @param string $lang 是否开启语言识别
     * @return array 返回数组，例：array("querydata"=>array(),"curnum"=>0,"querynum"=>0)
     */
    public static function QueryData($table,$field='',$where='',$order='',$limit='',$lang='0'){
        global$language;
        $db=UTSqlite::GetSqlite();
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
        if(UTSqlite::ModTable($table)):
            $sql="select ".$field." from ".$table." ".$where." ".$order." ".$limit;
            $query=$db->query($sql);
            $querydata=array(); 
            $xu=0;
            while($rows=$query->fetchArray(SQLITE3_ASSOC)):
                $xu=$xu+1;
                $count=count($rows);
                for($i=0;$i<$count;$i++):
                    unset($rows[$i]);
                endfor;
                $rows['xu']=$xu;
                array_push($querydata,$rows);
            endwhile;
            $curnum=$xu;
            $querynum=empty($limit) ? $curnum : UTSqlite::QueryNum("select count(*) from ".$table." ".$where." ".$order);
            return array("querydata"=>$querydata,"curnum"=>$curnum,"querynum"=>$querynum);
        else:
            return array("querydata"=>array(),"curnum"=>0,"querynum"=>0);
        endif;
    }
    /**
     * 执行SQL并返回结果集
     * @param string $sql SQL语句
     * @return array 返回数组，例：array("querydata"=>array(),"querynum"=>0)
     */
    public static function JoinQuery($sql){
		$db=UTSqlite::GetSqlite();
		$query=$db->query($sql);
        $querydata=array(); 
		$curnum=0;
        while($rows=$query->fetchArray(SQLITE3_ASSOC)):
		    $curnum++;
            array_push($querydata,$rows);
        endwhile;
		$querynum=UTSqlite::QueryNum($sql);
        return array("querydata"=>$querydata,"curnum"=>$curnum,"querynum"=>$querynum);
    }
    /**
     * 新增数据
     * @param string $table 被表名
     * @param string $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @return bool 
     */
    public static function InsertData($table,$data){
        $db=UTSqlite::GetSqlite();
        $sql="insert into ".$table." (".implode(',',array_keys($data)).") values ('".implode("','",array_values($data))."')";
        $query=UTSqlite::RunSql($sql);
        if($query):
            return $db->lastInsertRowID();
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
        $db=UTSqlite::GetSqlite();
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
        $query=UTSqlite::RunSql($sql);
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
        $db=UTSqlite::GetSqlite();
        $sql="delete from ".$table." where ".$where;
        $query=UTSqlite::RunSql($sql);
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
        $db=UTSqlite::GetSqlite();
        $tags="";
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
        if(UTSqlite::ModTable($table)):
            $sql="select ".$field." from ".$table." ".$where." ".$order;
            $tag=$db->query($sql);
            while($rows=$tag->fetchArray(SQLITE3_ASSOC)):
                $tags=$tags.",".$rows[$field];
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
        $db=UTSqlite::GetSqlite();
        $where=empty($where) ? "" : "where ".$where;
        $limit=empty($limit) ? "" : "limit ".$limit;
        if(UTMysql::ModTable($table)):
            $sql="SELECT ".$field." from ".$table." ".$where." ".$limit;
            $query=$db->query($sql);  
            $figuredata=array(); 
            while($rows=$query->fetchArray(SQLITE3_ASSOC)):
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
     * 统计记录数目
     * @param string $sql SQL语句
     * @return int
     */
    public static function QueryNum($sql){
        $db=UTSqlite::GetSqlite();
        $num=$db->querySingle($sql);
        return $num;
    }
    /**
     * 在内存中打开一个数据库
     * @param string $data 数据库
     */
    public static function Open($data){
        $sqlite=new \SQLite3();
        $db=$sqlite->open($data);
        return $db;
    }
    /**
     * 返回最近的出错记录
     * @return string 
     */
    public static function ErrorMsg(){
        $db=UTSqlite::GetSqlite();
        return $db->lastErrorMsg();
    }
    /**
     * 获取SQLITE版本号
     * @return array 
     */
    public static function Version(){
        $db=UTSqlite::GetSqlite();
        return $db->version();
    }
}