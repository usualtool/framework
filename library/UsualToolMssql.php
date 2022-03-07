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
    static $cursor=0;
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
     * 获取单个或多个数据
     * @param string $table 被表名
     * @param string $field 查询字段，多个字段以‘,’分割
     * @param string $where 查询条件
     * @param string $order 排序方式，例：id desc/id asc
     * @param string|int $limit 数据显示数目，例：10
     * @return array 返回数组，例：array("querydata"=>array(),"querynum"=>0)
     */
    public static function QueryData($table,$field='',$where='',$order='',$limit=''){
        $field=empty($field) ? "*" : $field;
        $where=empty($where) ? "" : "where ".$where;
        $order=empty($order) ? "" : "order by ".$order;
        $limit=empty($limit) ? "" : "top ".$limit;
        $sql="select ".$limit." ".$field." from ".$table." ".$where." ".$order;
		$db=UTMssql::GetMssql();
        $array = array();
        $result = sqlsrv_query($db,$sql);
        $i=0;
        while($r=UTMssql::FetchArray($result)){
            $i++;
            if($field!="*"){
                $key = $r[$field];
                $array[$key] = $r;
            }else{
                $array[] = UTMssql::ObjectToArray($r);
            }
        }
        $data=array("querydata"=>$array,"querynum"=>$i);
        return $data;
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
     * 获取结果集数组
     * @param string $obj 对象
     * @return array
     */
    public static function FetchArray($query,$type=SQLSRV_FETCH_ASSOC){
        if(is_resource($query)) return sqlsrv_fetch_array($query,$type);
            if(self::$cursor<count($query)){
                return $query[self::$cursor++];
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
            if(is_array($value) || is_object($value)){
                $ret[$key] = UTMssql::ObjectToArray($value);
            }else{
                $ret[$key] = $value;
            }
        }
        return $ret;
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
}