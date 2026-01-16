<?php
namespace library\UsualToolMysql;
use library\UsualToolInc;
/**
       * --------------------------------------------------------       
       *  |                  █   █ ▀▀█▀▀                    |           
       *  |                  █▄▄▄█   █                      |           
       *  |                                                 |           
       *  |    Author: Huang Hui                            |           
       *  |    Repository 1: https://gitee.com/usualtool    |           
       *  |    Repository 2: https://github.com/usualtool   |           
       *  |    Applicable to Apache 2.0 protocol.           |           
       * --------------------------------------------------------       
*/
/**
 * 以mysqli方法操作Mysql
 */
class UTMysql{
    /**
     * 获取配置
     */
    public static function GetConfig(){
        return UsualToolInc\UTInc::GetConfig();
    }
    /**
     * 连接Mysqli
     */    
    public static function GetMysql(){
        $config=UTMysql::GetConfig();
        $host=empty($config["MYSQL_PORT"]) ? $config["MYSQL_HOST"] : $config["MYSQL_HOST"].":".$config["MYSQL_PORT"];
        $db=new \mysqli($host,$config["MYSQL_USER"],$config["MYSQL_PASS"],$config["MYSQL_DB"]);
        if(!$db):
            return "Mysqli connection error.";
        else:
            $db->set_charset($config["MYSQL_CHARSET"]);
            return $db;
        endif;
    }
    /**
     * 测试Mysqli
     */    
    public static function TestDataBase($DBHOST,$DBPORT,$DBUSER,$DBPASS,$DBNAME){
        $db=new \mysqli($DBHOST.":".$DBPORT,$DBUSER,$DBPASS,$DBNAME);
        if(!$db):
            return false;
        else:
            $db->set_charset("utf8");
            return $db;
        endif;
    }
    /**
     * 判断表是否存在
     * @param string $table
     * @return bool
     */
    public static function ModTable($table){
        $db=UTMysql::GetMysql();
        if(mysqli_num_rows($db->query("SHOW TABLES LIKE '". $table."'"))==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * Mysqli批量任务
     * @param string $sql SQL语句
     * @return bool
     */
    public static function RunSql($sql){
        $db=UTMysql::GetMysql();
        if($db->multi_query($sql)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取单个或多个数据
     * @param string $table 被查询表名
     * @param string $field 查询字段，多个字段以‘,’分割
     * @param string $where 查询条件
     * @param string $order 排序方式，例：id desc/id asc
     * @param string|int $limit 数据显示数目，例：0,5/1
     * @param string $lang 是否开启语言识别
     * @return array 返回数组，例：array("querydata"=>array(),"curnum"=>0,"querynum"=>0)
     */
    public static function QueryData($table,$field='',$where='',$order='',$limit='',$lang='0'){
        global$language;
        $db=UTMysql::GetMysql();
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
        if(UTMysql::ModTable($table)):
            $sql="select ".$field." from `".$table."` ".$where." ".$order." ".$limit;
            $query=$db->query($sql);
            $curnum=mysqli_num_rows($query);
            $querynum=empty($limit) ? $curnum : UTMysql::QueryNum("select ".$field." from ".$table." ".$where." ".$order);
            $querydata=array(); 
            $xu=0;
            while($rows=mysqli_fetch_array($query,MYSQLI_ASSOC)):
                $xu=$xu+1;
                $count=count($rows);
                for($i=0;$i<$count;$i++):
                    unset($rows[$i]);
                endfor;
                $rows['xu']=$xu;
                array_push($querydata,$rows);
            endwhile;
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
        $db=UTMysql::GetMysql();
        $query=$db->query($sql);
        $curnum=mysqli_num_rows($query);
        $querynum=UTMysql::QueryNum($sql);
        $querydata=array(); 
        $xu=0;
        while($rows=mysqli_fetch_array($query,MYSQLI_ASSOC)):
            $xu=$xu+1;
            $count=count($rows);
            for($i=0;$i<$count;$i++):
                unset($rows[$i]);
            endfor;
            $rows['xu']=$xu;
            array_push($querydata,$rows);
        endwhile;
        return array("querydata"=>$querydata,"curnum"=>$curnum,"querynum"=>$querynum);
    }
    /**
     * SELECT预处理查询
     * @param string $sql 带?占位符的SELECT语句
     * @param array $param 参数值数组
     * @return array
     */
    public static function YuQuery($sql,$param=[]){
        $db=UTMysql::GetMysql();
        $stmt=$db->prepare($sql);
        if(!$stmt):
            return array("querydata"=>array(),"querynum"=>0);
        endif;
        if(!empty($param)):
            $type='';
            $bindParams=array();
            foreach($param as $par):
                if(is_int($par)):
                    $type.='i';
                elseif (is_float($par)):
                    $type.='d';
                else:
                    $type.='s';
                endif;
                $bindParams[] = $par;
            endforeach;
            $ref=array();
            foreach($bindParams as $key => &$value):
                $ref[$key] = &$value;
            endforeach;
            array_unshift($ref,$type);
            call_user_func_array([$stmt,'bind_param'],$ref);
        endif;
        if(!$stmt->execute()):
            $stmt->close();
            return array("querydata"=>array(),"querynum"=>0);
        endif;
        $result=$stmt->get_result();
        if(!$result):
            $stmt->close();
            return array("querydata"=>array(),"querynum"=>0);
        endif;
        $querydata=array();
        $xu=0;
        while($rows=$result->fetch_assoc()):
            $rows['xu']=++$xu;
            $querydata[]=$rows;
        endwhile;
        $querynum=count($querydata);
        $stmt->close();
        return array("querydata"=>$querydata,"querynum"=>$querynum);
    }
    /**
     * 添加数据
     * @param string $table 表名
     * @param array $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @return bool 当结果为真时返回最新添加的记录id
     */
    public static function InsertData($table,$data){
        $db=UTMysql::GetMysql();
        $sql="insert into `".$table."` (".implode(',',array_keys($data)).") values ('".implode("','",array_values($data))."')";
        $query=$db->query($sql);
        if($query):
            return mysqli_insert_id($db);
        else:
            return false;
        endif;
    }
    /**
     * 编辑数据
     * @param string $table 表名
     * @param array $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @param string $where 条件
     * @return bool
     */
    public static function UpdateData($table,$data,$where){
        $db=UTMysql::GetMysql();
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
        $sql="update `".$table."` set ".$updatestr." where ".$where;
        $query=$db->query($sql);
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
        $db=UTMysql::GetMysql();
        $sql="delete from `".$table."` where ".$where;
        $query=$db->query($sql);
        if($query):
            return true;
        else:
            return false;
        endif;
    }
    /**
     * 复制数据
     * @param string $table 表名
     * @param string $where 条件
     * @param string $autokey 自动编号字段
     * @return bool
     */
    public static function CopyData($table,$where,$autokey='id'){
        $db=UTMysql::GetMysql();
        $sql = "SELECT * FROM `".$table."` where ".$where;
        $query=$db->query($sql);
        $rows=mysqli_fetch_array($query,MYSQLI_ASSOC);
        unset($rows[$autokey]);
        $csql="insert into `".$table."` (".implode(',',array_keys($rows)).") values ('".implode("','",array_values($rows))."')";
        $cquery=$db->query($csql);
        if($cquery):
            return mysqli_insert_id($db);
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
        $db=UTMysql::GetMysql();
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
        if(UTMysql::ModTable($table)):
            $sql="select ".$field." from `".$table."` ".$where." ".$order;
            $tag=$db->query($sql);
            while($tagrow=$tag->fetch_row()):
                $tags="".$tags.",".$tagrow[0];
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
        $db=UTMysql::GetMysql();
        $where=empty($where) ? "" : "where ".$where;
        $limit=empty($limit) ? "" : "limit ".$limit;
        if(UTMysql::ModTable($table)):
            $sql="SELECT ".$field." from `".$table."` ".$where." ".$limit;
            $query=$db->query($sql);  
            $figuredata=array(); 
            while($rows=mysqli_fetch_array($query,MYSQLI_ASSOC)):
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
     * 搜索方法
	 * 可视化包预留
     * @param string $keyword 关键词
     * @return array 返回数组
     */
    public static function SearchData($keyword){
        $db=UTMysql::GetMysql();
        global$language;
		if(!empty($keyword)):
			$sql="SELECT * FROM `cms_search` WHERE keyword ='$keyword'";
			$sdata=mysqli_query($db,$sql);
			if(mysqli_num_rows($sdata)>0):
			    UTMysql::UpdateData("cms_search",array("hit"=>"hit+1"),"keyword ='$keyword' and lang='$language'");
			endif;
		endif;
		$data=array();
		$result=$db->query("select * from `cms_search_set`");
		while($row=mysqli_fetch_array($result)){
		    $data[]=array("db"=>$row["dbs"],"field"=>$row["fields"],"where"=>$row["wheres"],"page"=>$row["pages"]);
		}
		$table="select 'search' as thepage,id,'0' as title,'0' as content from `cms_search` where id<0";
		foreach($data as $key=>$val){
			if(UTMysql::ModTable($val["db"])){
				$table.=" union select '".$val["page"]."' as thepage,id,".$val["field"]." from ".$val["db"]." where ".str_replace("[keyword]","'%".$keyword."%'",$val["where"])."";
			}
		}
        $search=$db->query($table);
        $searchnum=mysqli_num_rows($search);
        if(!empty($keyword) && $searchnum>0 && mysqli_num_rows($sdata)<=0):
			UTMysql::InsertData("cms_search",array("lang"=>$language,"keyword"=>$keyword));
        endif;
        $searchdata=array(); 
        $xu=0;
        while($rows=mysqli_fetch_array($search,MYSQLI_ASSOC)):
            $xu=$xu+1;
            $count=count($rows);
            for($i=0;$i<$count;$i++):
                unset($rows[$i]);
            endfor;
            $rows['xu']=$xu;
            array_push($searchdata,$rows);
        endwhile;
        return array("searchdata"=>$searchdata,"searchnum"=>$searchnum);	
    }
    /**
     * 统计记录数目
     * @param string $sql SQL语句
     * @return int
     */
    public static function QueryNum($sql){
        $db=UTMysql::GetMysql();
        $query=$db->query($sql);
        return mysqli_num_rows($query);
    }
}
