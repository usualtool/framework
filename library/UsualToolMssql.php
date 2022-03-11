<?php
namespace library\UsualToolMssql;
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
 * 以静态方法操作Sqlserver
 */
class UTMssql{
    /**
     * 连接Mssql
     */    
    public static function GetMssql(){
        $config=UsualToolInc\UTInc::GetConfig();
        $db=@sqlsrv_connect($config["MSSQL_HOST"].",".$config["MSSQL_PORT"],array(
            'UID'=>$config["MSSQL_USER"],
            'PWD'=>$config["MSSQL_PASS"],
            'Database'=>$config["MSSQL_DB"])
        );
        if(!$db){
            print_r("Error:".sqlsrv_errors());
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
        $db=UTMssql::GetMssql();
        if(sqlsrv_query($db,"select * from ".$table)===false){
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
		$db=UTMssql::GetMssql();
        $result = sqlsrv_query($db,$sql);
        if(!$result){
            print_r(sqlsrv_errors());
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
        $limit=empty($limit) ? "" : "top ".$limit;
        if(UTMssql::ModTable($table)):
            $sql="select ".$limit." ".$field." from ".$table." ".$where." ".$order;
            $db=UTMssql::GetMssql();
            $array = array();
            $result = sqlsrv_query($db,$sql);
            $curnum=0;
            while($rows=UTMssql::FetchArray($result)){
                $curnum++;
                if($field!="*"){
                    $key = $r[$field];
                    $array[$key] = $rows;
                }else{
                    $array[] = UTMssql::ObjectToArray($rows);
                }
            }
            $querynum=empty($limit) ? $curnum : UTMssql::QueryNum("select ".$field." from ".$table." ".$where." ".$order);
            return array("querydata"=>$array,"curnum"=>$curnum,"querynum"=>$querynum);
        else:
            return array("querydata"=>array(),"curnum"=>$curnum,"querynum"=>0);
        endif;
    }
    /**
     * 新增数据
     * @param string $table 被表名
     * @param string $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
     * @return bool 
     */
    public static function InsertData($table,$data){
        $db=UTMssql::GetMssql();
        $sql="insert into ".$table." (".implode(',',array_keys($data)).") values ('".implode("','",array_values($data))."')";
        $query=UTMssql::RunSql($sql);
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
        $db=UTMssql::GetMssql();
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
        $query=UTMssql::RunSql($sql);
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
        $db=UTMssql::GetMssql();
        $sql="delete from ".$table." where ".$where;
        $query=UTMssql::RunSql($sql);
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
        $db=UTMssql::GetMssql();
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
        if(UTMssql::ModTable($table)):
            $sql="select ".$field." from ".$table." ".$where." ".$order;
            $tag = sqlsrv_query($db,$sql);
            while($rows=UTMssql::FetchArray($tag)):
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
        $db=UTMssql::GetMssql();
        $where=empty($where) ? "" : "where ".$where;
        $limit=empty($limit) ? "" : "top ".$limit;
        if(UTMssql::ModTable($table)):
            $sql="SELECT ".$limit." ".$field." from ".$table." ".$where;
            $query = sqlsrv_query($db,$sql);
            $figuredata=array(); 
            while($rows=UTMssql::FetchArray($query)):
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
     * 获取结果集数组
     * @param string $obj 对象
     * @return array
     */
    public static function FetchArray($query,$type=SQLSRV_FETCH_ASSOC){
        $cursor=0;
        if(is_resource($query)) return sqlsrv_fetch_array($query,$type);
            if($cursor<count($query)){
                return $query[$cursor++];
            }
        return false;
    }
    /**
     * 对象转数组
     * @param string $obj 对象
     * @return array
     */
    public static function ObjectToArray($obj){
        $ret = array();
        foreach($obj as $key => $value){
            if(is_array($value)){
                $ret[$key] = UTMssql::ObjectToArray($value);
            }elseif(is_object($value)){
                $value=(array)$value;
                $ret[$key] = UTMssql::ObjectToArray($value);
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
        $db=UTMssql::GetMssql();
        $query=sqlsrv_query($db,$sql,array(),array("Scrollable"=>'static'));
        return sqlsrv_num_rows($query);
    }
    /**
     * 将GBK转UTF-8
     */  
	public static function ConvertUtf8($str){
        return iconv("gbk","utf-8",$str);
    }
    /**
     * 将UTF-8转GBK
     */  
    public static function ConvertGbk($str){
        return iconv("utf-8","gbk",$str);
    }
    /**
     * 关闭Mssql连接
     */  
	public static function Close(){
        $db=UTMssql::GetMssql();
		sqlsrv_close($db);
    }
    /**
     * 获取版本号
     */ 
	public static function Ver(){
        $db=UTMssql::GetMssql();
        return sqlsrv_server_info($db);
    }
}