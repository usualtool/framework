<?php
namespace library\UsualToolData;
use library\UsualToolInc;
use library\UsualToolRedis;
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
 * 以静态方法操作Mysqli
 */
class UTData{
    /**
     * 获取配置
     */
    public static function GetConfig(){
        return UsualToolInc\UTInc::GetConfig();
    }    
    /**
     * 连接Mysqli
     */    
    public static function GetDataBase(){
        $config=UTData::GetConfig();
        $db=new \mysqli($config["DBHOST"].":".$config["DBPORT"],$config["DBUSER"],$config["DBPASS"],$config["DBNAME"]);
        if(!$db):
            return "Mysqli connection error.";
        else:
            $db->set_charset($config["DBCHARSET"]);
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
     * 判断模块是否存在
     * @param string $table
     * @return bool
     */
    public static function ModTable($table){
        $db=UTData::GetDataBase();
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
        $db=UTData::GetDataBase();
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
     * @param string $lang 是否开启语言识别，默认0关闭，当需要开启时，该参数填写>0的数字，自动获取全局的语言参数，也可以直接填写语言参数zh/en/ja等
     * @param string $cache 是否开启redis缓存，默认0关闭，需要开启时，该参数填写key名称
     * @return array 返回数组，例：array("querydata"=>array(),"querynum"=>0)
     */
    public static function QueryData($table,$field='',$where='',$order='',$limit='',$lang='0',$cache='0'){
        global$language;
        $db=UTData::getDataBase();
        $config=UTData::GetConfig();
        $fields=empty($field) ? "*" : $field;
        if($lang!="0"):
            if(is_numeric($lang)):
                $wheres=empty($where) ? "where lang='$language'" : "where lang='$language' and ".$where."";
            else:
                $wheres=empty($where) ? "where lang='$lang'" : "where lang='$lang' and ".$where."";
            endif;
        else:
            $wheres=empty($where) ? "" : "where ".$where."";
        endif;
        $orders=empty($order) ? "" : "order by ".$order."";
        $limits=empty($limit) ? "" : "limit ".$limit."";
        if(UTData::ModTable($table)):
            if($config["REDIS"]=="1" && $cache!="0"):
                $redis=new UsualToolRedis\UTRedis();
                if(!$redis->Exists("UT-".$cache."")):
                    $sql="select * from `".$table."` ".$wheres."";
                    $list=$db->query($sql);
                    $listnum=mysqli_num_rows($list);
                    $sqls="select ".$fields." from `".$table."` ".$wheres." ".$orders." ".$limits."";
                    $querys=$db->query($sqls);
                    $querydata=array(); 
                    $xu=0;
                    while($rows=mysqli_fetch_array($querys,MYSQLI_ASSOC)):
                        $xu=$xu+1;
                        $count=count($rows);
                        for($i=0;$i<$count;$i++):
                            unset($rows[$i]);
                        endfor;
                        $rows['xu']=$xu;
                        array_push($querydata,$rows);
                    endwhile;
                    $redis->Set("UT-".$cache."",json_encode(array("querydata"=>$querydata,"querynum"=>$listnum)),$config["REDIS_TIME"]);
                    return array("querydata"=>$querydata,"querynum"=>$listnum);
                else:
                    return json_decode($redis->Get("UT-".$cache.""),true);
                endif;
            else:
                $sql="select * from `".$table."` ".$wheres."";
                $list=$db->query($sql);
                $listnum=mysqli_num_rows($list);
                $sqls="select ".$fields." from `".$table."` ".$wheres." ".$orders." ".$limits."";
                $querys=$db->query($sqls);
                $querydata=array(); 
                $xu=0;
                while($rows=mysqli_fetch_array($querys,MYSQLI_ASSOC)):
                    $xu=$xu+1;
                    $count=count($rows);
                    for($i=0;$i<$count;$i++):
                        unset($rows[$i]);
                    endfor;
                    $rows['xu']=$xu;
                    array_push($querydata,$rows);
                endwhile;
                return array("querydata"=>$querydata,"querynum"=>$listnum);
            endif;
        else:
            return array("querydata"=>array(),"querynum"=>0);
        endif;
    }
    /**
     * 添加数据
     * @param string $table 表名
     * @param array $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @return bool 当结果为真时返回最新添加的记录id
     */
    public static function InsertData($table,$data){
        $db=UTData::GetDataBase();
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
        $db=UTData::GetDataBase();
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
        $sql="update `".$table."` set ".$updatestr." where ".$where."";
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
        $db=UTData::GetDataBase();
        $sql="delete from `".$table."` where ".$where."";
        $query=$db->query($sql);
        if($query):
            return true;
        else:
            return false;
        endif;
    }
    /**
     * 自定义获取标签集合
     * @param string $table 表名
     * @param string $field 标签字段，只能为1个
     * @param string $where 条件
     * @param string $order 排序方式
     * @param string $lang 是否自动开启语言，默认0关闭
     * @return array 返回数组，例：array('tags'=>$taglist)
     */
    public static function TagData($table,$field='',$where='',$order='',$lang='0'){
        global$language;
        $db=UTData::GetDataBase();
        $config=UTData::GetConfig();
        $tags="";
        $fields=empty($field) ? "*" : $field;
        if($lang!="0"):
            if(is_numeric($lang)):
                $wheres=empty($where) ? "where lang='$language'" : "where lang='$language' and ".$where."";
            else:
                $wheres=empty($where) ? "where lang='$lang'" : "where lang='$lang' and ".$where."";
            endif;
        else:
            $wheres=empty($where) ? "" : "where ".$where."";
        endif;
        $orders=empty($order) ? "" : "order by ".$order."";
        if(UTData::ModTable($table)):
            $sql="select ".$fields." from `".$table."` ".$wheres." ".$orders."";
            $tag=$db->query($sql);
            while($tagrow=$tag->fetch_row()):
                $tags="".$tags.",".$tagrow[0]."";
            endwhile;
            $taglist=join(',',array_unique(array_diff(explode(",",$tags),array(""))));
            $taglists[]=array('tags'=>$taglist);
            return $taglists;
        else:
            return array();
        endif;
    }
    /**
     * 获取指定字段内容的第一张图片集合
     * @param string $table 表名
     * @param string $field 标签字段，只能为1个
     * @param string $where 条件
     * @return array 返回数组，在其数组中返回指定字段内容的第一张图片imageurl
     */
    public static function FigureData($table,$field,$where='',$limit=''){
        $db=UTData::GetDataBase();
        $wheres=empty($where) ? "" : "where ".$where."";
        $limits=empty($limit) ? "" : "limit ".$limit."";
        if(UTData::ModTable($table)):
            $sql="SELECT * from `".$table."` ".$wheres." ".$limits."";
            $querys=$db->query($sql);  
            $figuredata=array(); 
            while($rows=mysqli_fetch_array($querys,MYSQLI_ASSOC)):
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
     * @param string $keyword 关键词
     * @return array 返回数组
     */
    public static function SearchData($keyword){
        $db=UTData::GetDataBase();
        global$language;
		if(!empty($keyword)):
			$sql="SELECT * FROM `cms_search` WHERE keyword ='$keyword'";
			$sdata=mysqli_query($db,$sql);
			if(mysqli_num_rows($sdata)>0):
			    UTData::UpdateData("cms_search",array("hit"=>"hit+1"),"keyword ='$keyword' and lang='$language'");
			endif;
		endif;
		$data=array();
		$result=$db->query("select * from `cms_search_set`");
		while($row=mysqli_fetch_array($result)){
		    $data[]=array("db"=>$row["dbs"],"field"=>$row["fields"],"where"=>$row["wheres"],"page"=>$row["pages"]);
		}
		$table="select 'search' as thepage,id,'0' as title,'0' as content from `cms_search` where id<0";
		foreach($data as $key=>$val){
			if(UTData::ModTable($val["db"])){
				$table.=" union select '".$val["page"]."' as thepage,id,".$val["field"]." from ".$val["db"]." where ".str_replace("[keyword]","'%".$keyword."%'",$val["where"])."";
			}
		}
        $search=$db->query($table);
        $searchnum=mysqli_num_rows($search);
        if(!empty($keyword) && $searchnum>0 && mysqli_num_rows($sdata)<=0):
			UTData::InsertData("cms_search",array("lang"=>$language,"keyword"=>$keyword));
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
}