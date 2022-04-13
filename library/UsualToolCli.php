<?php
namespace library\UsualToolCli;
use library\UsualToolInc;
use library\UsualToolData;
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
     * 以静态方法执行Cli命令
     */
class UTCli{
    public static $cli;
    /**
     * 初始化命令控制
     * @param int $num
     * @param array $array
     */
    public static function Run($array){
        if(count($array)>1):
            $cli=ucwords($array[1]);
        else:
            $cli="help";
        endif;
        UTCli::$cli($array);
    }
    /**
     * 打印参数
     * @param array $array
     * @return string
     */
    public static function Print($array){
        foreach($array as $key=>$val):
            if($key>1):
                $n=$key-1;
                echo"第".$n."个参数：".$val."\r\n";
            endif;
        endforeach;
    }
    /**
     * 运行命令并返回结果
     * @param string $cmd
     * @return string
     */
    public static function Execute($cmd){
        $cmd=str_replace("%26","",str_replace("%7C","",str_replace("&","",str_replace("|","",$cmd))));
        if(substr($cmd,0,2)=="cd" || substr($cmd,0,3)=="php" || substr($cmd,0,5)=="nohup" ||substr($cmd,0,8)=="composer"){
            $results=shell_exec($cmd);
        }else{
            $results="Not Supported.";
        }
        return $results;
    }
    /**
     * 创建模块
     * @param array $array
     * @return string
     */
    public static function Module($array){
        if(count($array)>2):
            $module=$array[2];
            $m1=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module,0777);
            $m2=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module."/admin",0777);
            $m3=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module."/cache",0777);
            $m4=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module."/front",0777);
            $m5=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module."/skin",0777);
            $m6=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module."/skin/admin",0777);
            $m7=UsualToolInc\UTInc::MakeDir(APP_ROOT."/modules/".$module."/skin/front",0777);
            if($m1 && $m2 && $m3 && $m4 && $m5 && $m6 && $m7):
                $c="<?xml version='1.0' encoding='UTF-8'?>\r\n";
                $c.="<mod>\r\n";
                $c.="<id>".$module."</id>\r\n";
                $c.="<modtype>2</modtype>\r\n";
                $c.="<auther>NULL</auther>\r\n";
                $c.="<title>".$module."</title>\r\n";
                $c.="<modname>".$module."</modname>\r\n";
                $c.="<ver>1.0</ver>\r\n";
                $c.="<description>NULL</description>\r\n";
                $c.="<itemid>1</itemid>\r\n";
                $c.="<ordernum>1</ordernum>\r\n";
                $c.="<modurl>index.php</modurl>\r\n";
                $c.="<befoitem>NULL</befoitem>\r\n";
                $c.="<backitem>NULL</backitem>\r\n";
                $c.="<installsql><![CDATA[0]]></installsql>\r\n";
                $c.="<uninstallsql><![CDATA[0]]></uninstallsql>\r\n";
                $c.="</mod>";
                file_put_contents(APP_ROOT."/modules/".$module."/usualtool.config",$c);
                $iphp='<?php $app->Open("index.cms");';
                file_put_contents(APP_ROOT."/modules/".$module."/front/index.php",$iphp);
                $icms.='Hello UT';
                file_put_contents(APP_ROOT."/modules/".$module."/skin/front/index.cms",$icms);
                echo"模块创建成功，请注意给该模块设置写入权限\r\n";
                echo"模块路径：/app/modules/".$module."/\r\n";
                echo"访问路径：/?m=".$module."\r\n";
            else:
                echo"模块创建失败\r\n";
            endif;
        else:
            echo"命令参数错误\r\n";
        endif;
    }
    /**
     * 创建插件
     * @param array $array
     * @return string
     */
    public static function Plugin($array){
        if(count($array)>2):
            $plugin=$array[2];
            $p=UsualToolInc\UTInc::MakeDir(APP_ROOT."/plugins/".$plugin,0777);
            if($p):
                $c="<?xml version='1.0' encoding='UTF-8'?>\r\n";
                $c="<?xml-stylesheet type='text/css' href='http://frame.usualtool.com/image/css/xml.css'?>\r\n";
                $c.="<hook>\r\n";
                $c.="<id>".$plugin."</id>\r\n";
                $c.="<type>Free</type>\r\n";
                $c.="<plugintype>1</plugintype>\r\n";
                $c.="<price>0.00</price>\r\n";
                $c.="<auther>NULL</auther>\r\n";
                $c.="<title>".$plugin."</title>\r\n";
                $c.="<pluginname>".$plugin."</pluginname>\r\n";
                $c.="<ver>1.0</ver>\r\n";
                $c.="<description>NULL</description>\r\n";
                $c.="<installsql><![CDATA[0]]></installsql>\r\n";
                $c.="<uninstallsql><![CDATA[0]]></uninstallsql>\r\n";
                $c.="<plugincode><![CDATA[?><?php echo'这里是插件后端代码部分';?>]]></plugincode>\r\n";
                $c.="</hook>";
                file_put_contents(APP_ROOT."/plugins/".$plugin."/usualtool.config",$c);
                $i="这里是插件前段代码部分";
                file_put_contents(APP_ROOT."/plugins/".$plugin."/index.php",$i);
                echo"插件创建成功\r\n";
                echo"插件路径：/app/plugins/".$plugin."/\r\n";
            else:
                echo"插件创建失败\r\n";
            endif;
        else:
            echo"命令参数错误\r\n";
        endif;
    }
    /**
     * 验证UT令牌合法性
     * @param array $array
     * @return string
     */
    public static function Key(){
        $config=UsualToolInc\UTInc::GetConfig();
        $key=$config["UTCODE"];
        echo UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"key")."\r\n";
    }
    /**
     * 安装命令
     * @param array $array
     * @return string
     */
    public static function Install($array){
        $config=UsualToolInc\UTInc::GetConfig();
        if(count($array)>2):
            $type=$array[2];
            $name=$array[3];
            $number=$array[4];
            if($type=="module"):
                echo"模块安装中...\r\n";
                if($number!="-2"):  
                    if($number=="-1"):
                        $down=UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"module-".$name);
                    elseif($number=="-3"):  
                        $down=UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"moduleorder-".$name);
                    endif;
                    $downurl=UsualToolInc\UTInc::StrSubstr("<downurl>","</downurl>",$down);
                    $filename=basename($downurl);
                    $res=UsualToolInc\UTInc::SaveFile($downurl,APP_ROOT."/modules",$filename,1);
                    if(!empty($res)):
                        UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"moduledel-".str_replace(".zip","",$filename)."");
                        $zip=new \ZipArchive;
                        if($zip->open(APP_ROOT."/modules/".$filename)===TRUE): 
                            $zip->extractTo(APP_ROOT."/modules/");
                            $zip->close();
                            unlink(APP_ROOT."/modules/".$filename);
                        else:
                            echo "modules目录775权限不足\r\n";
                           exit();
                        endif;
                    else:
                        echo "安装权限不足\r\n";
                        exit();
                    endif;
                endif;
                $modconfig=APP_ROOT."/modules/".$name."/usualtool.config";
                $mods=file_get_contents($modconfig);
                $modname=UsualToolInc\UTInc::StrSubstr("<modname>","</modname>",$mods);
                $ordernum=UsualToolInc\UTInc::StrSubstr("<ordernum>","</ordernum>",$mods);
                $modurl=UsualToolInc\UTInc::StrSubstr("<modurl>","</modurl>",$mods);
                $befoitem=UsualToolInc\UTInc::StrSubstr("<befoitem>","</befoitem>",$mods);
                $backitem=UsualToolInc\UTInc::StrSubstr("<backitem>","</backitem>",$mods);
                $itemid=UsualToolInc\UTInc::StrSubstr("<itemid>","</itemid>",$mods);
                $installsql=UsualToolInc\UTInc::StrSubstr("<installsql><![CDATA[","]]></installsql>",$mods);
                if(UsualToolData\UTData::ModTable("cms_admin_role") && UsualToolData\UTData::ModTable("cms_module")):
                    $role=UsualToolData\UTData::QueryData("cms_admin_role","","","","")["querydata"];
                    foreach($role as $rows):
                        $role_range=UsualToolData\UTData::QueryData("cms_admin_role","","id='".$rows["id"]."'","","")["querydata"][0]["module"];
                        $new_range=$role_range.",".$name;
                        UsualToolData\UTData::UpdateData("cms_admin_role",array("module"=>$new_range),"id='".$rows["id"]."'");
                    endforeach;
                    if(UsualToolData\UTData::QueryData("cms_module","","mid='$name'","","1")["querynum"]>0):
                        UsualToolData\UTData::UpdateData("cms_module",array(
                            "bid"=>$itemid,
                            "modname"=>$modname,
                            "modurl"=>$modurl,
                            "befoitem"=>$befoitem,
                            "backitem"=>$backitem),"mid='$name'");
                    else:
                        UsualToolData\UTData::InsertData("cms_module",array(
                            "bid"=>$itemid,
                            "mid"=>$name,
                            "modname"=>$modname,
                            "modurl"=>$modurl,
                            "isopen"=>1,
                            "look"=>1,
                            "ordernum"=>$ordernum,
                            "befoitem"=>$befoitem,
                            "backitem"=>$backitem));
                    endif;
                endif;
                if($installsql=='0'):
                    echo"成功安装模块\r\n";
                else:
                    if(UsualToolData\UTData::RunSql($installsql)):
                        echo"成功安装模块\r\n";
                    else:
                        echo"模块安装失败\r\n";
                    endif;   
                endif;
            elseif($type=="plugin"):
                echo"插件安装中...\r\n";
                if($number!="-2"):
                    if($number=="-1"):
                        $down=UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"plugin-".$name);
                    elseif($number=="-3"):  
                        $down=UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"pluginorder-".$name);
                    endif;
                    $downurl=UsualToolInc\UTInc::StrSubstr("<downurl>","</downurl>",$down);
                    $filename=basename($downurl);
                    $res=UsualToolInc\UTInc::SaveFile($downurl,APP_ROOT."/plugins",$filename,1);
                    if(!empty($res)):
                        UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"plugindel-".str_replace(".zip","",$filename)."");
                        $zip=new \ZipArchive;
                        if($zip->open(APP_ROOT."/plugins/".$filename)===TRUE): 
                            $zip->extractTo(APP_ROOT."/plugins/");
                            $zip->close();
                            unlink(APP_ROOT."/plugins/".$filename);
                        else:
                           echo "plugins目录775权限不足\r\n";
                           exit();
                        endif;
                    else:
                        echo "安装权限不足\r\n";
                        exit();
                    endif;
                endif;    
                $pconfig=APP_ROOT."/plugins/".$name."/usualtool.config";
                $plugins=file_get_contents($pconfig);
                $type=UsualToolInc\UTInc::StrSubstr("<type>","</type>",$plugins);
                $auther=UsualToolInc\UTInc::StrSubstr("<auther>","</auther>",$plugins);
                $title=UsualToolInc\UTInc::StrSubstr("<title>","</title>",$plugins);
                $ver=UsualToolInc\UTInc::StrSubstr("<ver>","</ver>",$plugins);
                $description=UsualToolInc\UTInc::StrSubstr("<description>","</description>",$plugins);
                $installsql=UsualToolInc\UTInc::StrSubstr("<installsql><![CDATA[","]]></installsql>",$plugins);
                if(UsualToolData\UTData::ModTable("cms_plugin")):
                    if(UsualToolData\UTData::QueryData("cms_plugin","","pid='$name'","","1")["querynum"]>0):
                        UsualToolData\UTData::UpdateData("cms_plugin",array(
                            "type"=>$type,
                            "auther"=>$auther,
                            "title"=>$title,
                            "ver"=>$ver,
                            "description"=>$description),"pid='$name'");
                    else:
                        UsualToolData\UTData::InsertData("cms_plugin",array(
                            "pid"=>$name,
                            "type"=>$type,
                            "auther"=>$auther,
                            "title"=>$title,
                            "ver"=>$ver,
                            "description"=>$description));
                    endif;
                endif;
                if($installsql=='0'):
                    echo"成功安装插件\r\n";
                else:
                    if(UsualToolData\UTData::RunSql($installsql)):
                        echo"成功安装插件\r\n";
                    else:
                        echo"插件安装失败\r\n";
                    endif;   
                endif;
            elseif($type=="formwork"):
                echo"整站模板工程安装中...\r\n";
                if($number!="-2"):
                    if($number=="-1"):
                        $down=UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"temp_".$name);
                    elseif($number=="-3"):  
                        $down=UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"temporder_".$name);
                    endif;
                    $downurl=UsualToolInc\UTInc::StrSubstr("<downurl>","</downurl>",$down);
                    $filename=basename($downurl);
                    $res=UsualToolInc\UTInc::SaveFile($downurl,APP_ROOT."/formwork",$filename,1);
                    if(!empty($res)):
                        UsualToolInc\UTInc::Auth($config["UTCODE"],$config["UTFURL"],"tempdel_".str_replace(".zip","",$filename)."");
                        $zip=new \ZipArchive;
                        if($zip->open(APP_ROOT."/formwork/".$filename)===TRUE): 
                            $zip->extractTo(APP_ROOT."/formwork/");
                            $zip->close();
                            unlink(APP_ROOT."/formwork/".$filename);
                        else:
                           echo "formwork目录775权限不足\r\n";
                           exit();
                        endif;
                    else:
                        echo "安装权限不足\r\n";
                        exit();
                    endif;
                endif;
                UsualToolInc\UTInc::MoveDir(APP_ROOT."/formwork/".$name."/move",UTF_ROOT);
                $pconfig=APP_ROOT."/formwork/".$name."/usualtool.config";
                $formwork=file_get_contents($pconfig);
                $id=UsualToolInc\UTInc::StrSubstr("<id>","</id>",$formwork);
                $type=UsualToolInc\UTInc::StrSubstr("<type>","</type>",$formwork);
                $lang=UsualToolInc\UTInc::StrSubstr("<lang>","</lang>",$formwork);
                $auther=UsualToolInc\UTInc::StrSubstr("<auther>","</auther>",$formwork);
                $title=UsualToolInc\UTInc::StrSubstr("<title>","</title>",$formwork);
                $ver=UsualToolInc\UTInc::StrSubstr("<ver>","</ver>",$formwork);
                $description=UsualToolInc\UTInc::StrSubstr("<description>","</description>",$formwork);
                $installsql=UsualToolInc\UTInc::StrSubstr("<installsql><![CDATA[","]]></installsql>",$formwork);
                if(UsualToolData\UTData::ModTable("cms_template")):
                    if(UsualToolData\UTData::QueryData("cms_template","","tid='$name'","","1")["querynum"]>0):
                        UsualToolData\UTData::UpdateData("cms_template",array("tid"=>$name,"lang"=>$lang,"title"=>$title),"tid='$name'");
                    else:
                        UsualToolData\UTData::InsertData("cms_template",array("tid"=>$name,"lang"=>$lang,"title"=>$title));
                    endif;
                endif;
                if($installsql=='0'):
                    echo"成功安装模板\r\n";
                else:
                    if(UsualToolData\UTData::RunSql($installsql)):
                        echo"成功安装模板\r\n";
                    else:
                        echo"模板安装失败\r\n";
                    endif;   
                endif;
            endif;
        else:
            echo"命令参数错误\r\n";
        endif;
    }
    /**
     * Swoole命令
     * @param array $array
     * @return string
     */
    public static function Swoole($array){
        require_once UTF_ROOT.'/'.'vendor/autoload.php';
        if(count($array)>2):
            $server=$array[2];
            $host=$array[3];
            $port=$array[4];
            if($server=="http"):
                $server=new \usualtool\Swoole\Http($host,$port);
            elseif($server=="proxy"):
                $server=new \usualtool\Swoole\Proxy($host,$port,$array[5]);
            elseif($server=="websocket"):
                $server=new \usualtool\Swoole\Websocket($host,$port);
            elseif($server=="pool"):
                $server=new \usualtool\Swoole\Pool($host,$port);
            elseif($server=="queue"):
                $server=new \usualtool\Swoole\Queue($host,$port);
            endif;
            $server->Run();
        else:
            echo"Swoole命令错误\r\n";
            echo"该命令在安装ut-swoole依赖后生效\r\n";
        endif;
    }
    /**
     * 帮助
     * @return string
     */
    public static function Help(){
        echo"usualtool命令列表\r\n";
        echo"1个中括号代表整1个参数，实际命令中不需要加中括号\r\n";
        echo"php usualtool 命令帮助\r\n";
        echo"php usualtool help 命令帮助\r\n";
        echo"php usualtool key 验证UT令牌的合法性\r\n";
        echo"php usualtool version 获取当前UT框架版本号\r\n";
        echo"php usualtool print [param] [param] ... 打印参数\r\n";
        echo"php usualtool module [name] 创建模块\r\n";
        echo"php usualtool plugin [name] 创建插件\r\n";
        echo"php usualtool install module [name] [1/2/3] 安装模块\r\n";
        echo"php usualtool install plugin [name] [1/2/3] 安装插件\r\n";
        echo"php usualtool install formwork [name] [1/2/3] 安装整站模板工程\r\n";
        echo"php usualtool swoole [name] [host] [port] ... swoole协程命令\r\n";
    }
    /**
     * 获取当前UT版本号
     * @return int
     */
    public static function Version(){
        echo file_get_contents(UTF_ROOT."/UTVER.ini")."\r\n";
    }
}