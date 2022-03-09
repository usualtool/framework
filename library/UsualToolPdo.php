<?php
namespace library\UsualToolPdo;
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
class UTPdo{
  /**
   * PDO连接
   */    
  public static function GetPdo(){
    $config=UsualToolInc\UTInc::GetConfig();
    try {
       $db=new \PDO(str_replace("__","=",$config['PDO_DSN']),$config['PDO_USER'],$config['PDO_PASS']);
       $db->query("set names utf8");
    }catch(Exception $e){
      echo$e->getMessage();
      exit();
    }
    $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE,\PDO::FETCH_ASSOC);
    return $db;
  }
  /**
   * 查询数据
   * @param string $table 被表名
   * @param string $field 查询字段，多个字段以‘,’分割
   * @param string $where 查询条件
   * @param string $order 排序方式，例：id desc/id asc
   * @param string|int $limit 数据显示数目，例：10
   * @param string $lang 是否开启语言识别
   * @return array 返回数组，例：array("querydata"=>array(),"querynum"=>0)
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
      $sql="select ".$limit." ".$field." from ".$table." ".$where." ".$order;
      $db=UTPdo::GetPdo();
      $array = array();
      $res=$db->query($sql);
      $i=0;
      while($rows=$res->fetch()){
        $i++;
        $array[]=$rows;
      }
      $data=array("querydata"=>$array,"querynum"=>$i);
      return $data;
  }
  /**
   * 创建数据
   * @param string $table 被表名
   * @param string $data 字段及值的数组，例：array("字段1"=>"值1","字段2"=>"值2")
   * @return bool 
   */
  public static function InsertData($table,$data){
      $db=UTPdo::GetPdo();
      $sql="insert into ".$table." (".implode(',',array_keys($data)).") values ('".implode("','",array_values($data))."')";
      $db->query($sql);
      $query=$db->lastInsertId();
      if($query):
          return $query;
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
      $db=UTPdo::GetPdo();
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
      $query=$db->exec($sql);
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
      $db=UTPdo::GetPdo();
      $sql="delete from ".$table." where ".$where;
      $query=$db->exec($sql);
      if($query):
          return true;
      else:
          return false;
      endif;
  }
}