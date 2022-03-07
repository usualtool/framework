<?php
namespace library\UsualToolMongo;
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
 * 以静态方法操作MongoDB
 */
class UTMongo {
    /**
     * 获取配置
     */
    public static function GetConfig(){
        return UsualToolInc\UTInc::GetConfig();
    } 
    /**
     * 连接MongoDB
     */
    public static function GetMongo() {
        $config=UTMongo::GetConfig();
        if(PHP_VERSION>=7){
            $db=new \MongoDB\Driver\Manager("mongodb://".$config["MONGO_USER"].":".$config["MONGO_PASS"]."@".$config["MONGO_HOST"].":".$config["MONGO_PORT"]."/".$config["MONGO_DB"]);
        }else{
            $db=new \MongoDB\Driver\Manager("mongodb://".$config["MONGO_HOST"].":".$config["MONGO_PORT"]."/".$config["MONGO_DB"]);
        }
        return $db;
    }
    /**
     * 获取数据
     * @param  string $table 表
     * @param  array  $filter 条件
     * @param  array  $writeOps 参数
     * @return array
     */
    public static function QueryData($table,array $filter,array $writeOps=[]){
        $cmd = [
            "find"=> $table,
            "filter"=> $filter
        ];
        $cmd += $writeOps;
        return UTMongo::Command($cmd);
    }
    /**
     * 插入数据
     * @param string $table 表
     * @param array  $documents 文档数据
     * @param array  $writeOps  参数
     * @return array
     */
    public static function InsertData($table,array $documents,array $writeOps=[]) {
        $cmd = [
            "insert"=> $table,
            "documents"=> $documents,
        ];
        $cmd += $writeOps;
        return UTMongo::Command($cmd);
    }
    /**
     * 删除数据
     * @param  string $table
     * @param  array  $deletes 删除条件
     * @param  array  $writeOps 参数
     * @return array
     */
    public static function DelData($table,array $deletes,array $writeOps=[]) {
        foreach($deletes as &$_){
            if(isset($_["q"]) && !$_["q"]){
                $_["q"] = (Object)[];
            }
            if(isset($_["limit"]) && !$_["limit"]){
                $_["limit"] = 0;
            }
        }
        $cmd = [
            "delete"=> $table,
            "deletes"=> $deletes,
        ];
        $cmd += $writeOps;
        return UTMongo::Command($cmd);
    }
    /**
     * 更新数据
     * @param  string $table writeOps
     * @param  array  $updates 条件
     * @param  array  $writeOps 参数
     * @return array
     */
    public static function UpdateData($table,array $updates,array $writeOps=[]) {
        $cmd = [
            "update"=> $table,
            "updates"=> $updates,
        ];
        $cmd += $writeOps;
        return UTMongo::Command($cmd);
    }
    /**
     * 执行MongoDB命令
     * @param array $param 命令
     * @return array
     */
    public static function Command(array $param) {
        $config=UTMongo::GetConfig();
        $db=UTMongo::GetMongo();
        $cmd = new \MongoDB\Driver\Command($param);
        $data=$db->executeCommand($config["MONGO_DB"],$cmd);
        return $data->toArray();
    }
    /**
     * 获取当前连接信息
     * @return array
     */
    public static function getMongoManager() {
        $db=UTMongo::GetMongo();
        return $db;
    }
}