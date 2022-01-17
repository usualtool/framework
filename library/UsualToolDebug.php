<?php
namespace library\UsualToolDebug;
use library\UsualToolInc;
/**
 * 以静态方法执行Debug捕获错误及调试
 */
class UTDebug{
    static function Debug($mode='0'){
        $debug=error_get_last();
        if(isset($debug)){
            UTDebug::WriteDebug($mode,$debug);
        }
    }
    static function WriteDebug($mode,$debug){
        $type=$debug["type"];
        $message=$debug["message"];
        $file=str_replace(UTF_ROOT,"",$debug["file"]);
        $line=$debug["line"];
        if($type==1 || $type==16 || $type==64 || $type==256 || $type==4096){
            $typetext="致命错误";
        }elseif($type==2|| $type==32 || $type==128 || $type==512){
            $typetext="运行警告";
        }elseif($type==4){
            $typetext="解析错误";
        }elseif($type==8){
            $typetext="运行通知";
        }else{
            $typetext="其他错误";
        }
        if($mode==1){
            echo"<div class='pt-2 pb-2 bg-dark text-white text-center' style='width:100%;position:fixed;left:0px;z-index:105;bottom:0px;'>";
            echo"调试结果：".$file." 第".$line."行".$typetext."：".$message;
            echo"</div>";
        }
        $thisbug=array(
            "time"=>date('Y-m-d H:i:s',time()),
            "type"=>$typetext,
            "file"=>$file,
            "line"=>$line,
            "message"=>$message);
        $old=file_get_contents(PUB_PATH."/debug/app_debug.log");
        if(!empty($old)){
            $arr[]=$thisbug;
            $old_data=json_decode($old,true);
            foreach($old_data as $val){
                $arr[]=$val;
            }
            $string=json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }else{
            $string=json_encode(array($thisbug),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }
        file_put_contents(PUB_PATH."/debug/app_debug.log",$string);
    }
}