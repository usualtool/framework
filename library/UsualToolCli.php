<?php
namespace library\UsualToolCli;
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
     * 以静态方法执行Cli
     */
class UTCli{
    /**
     * 运行命令并返回结果
     * @param string $cmd
     * @return string
     */
    static function execute($cmd){
        $cmd=str_replace("%26","",str_replace("%7C","",str_replace("&","",str_replace("|","",$cmd))));
        if(substr($cmd,0,2)=="cd" || substr($cmd,0,3)=="php" || substr($cmd,0,5)=="nohup" ||substr($cmd,0,8)=="composer"){
            $results=shell_exec($cmd);
        }else{
            $results="Not Supported.";
        }
        return $results;
    }
}