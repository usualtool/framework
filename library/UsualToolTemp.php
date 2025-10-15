<?php
namespace library\UsualToolTemp;
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
use library\UsualToolLang\UTLang;
use library\UsualToolTree\UTTree;
use library\UsualToolDebug\UTDebug;
/**
       * --------------------------------------------------------       
       *  |    ░░░░░░░░░     █   █░▀▀█▀▀░    ░░░░░░░░░      |           
       *  |  ░░░░░░░         █▄▄▄█   █                      |            
       *  |                                                 |            
       *  | Author:HuangDou   Email:292951110@qq.com        |            
       *  | QQ-Group:583610949                              |           
       *  | WebSite:http://www.UsualTool.com             |            
       *  | UT Framework is suitable for Apache2 protocol.  |            
       * --------------------------------------------------------                
 */
/**
 * 实例化模板引擎
 */
class UTTemp{
    var $mode;
    var $tempdir;
    var $cachedir;
    function __construct($mode,$tempdir,$cachedir){
        $this->tempdir=rtrim($tempdir,'/').'/';
        $this->cachedir=rtrim($cachedir,'/').'/';
        $this->mode=trim($mode);
        $this->tplvars=array();
    }
    /**
     * 向模板写入数据
     * @param string $tplvar
     * @param string|array $value 字符或数组
     */
    function Runin($tplvar,$value){
        if(is_array($tplvar)){
            foreach($tplvar as $key=>$values){
                $this->tplvars[$values] =$value[$key];
            } 
            unset($tplvar);
        }else{
            $this->tplvars[$tplvar] = $value;
        }
    }
    /**
     * 打开模板
     * @param string $filename 模板文件
     */
    function Open($filename){
        $tplfile=$this->tempdir.$filename;
        if(!file_exists($tplfile)){
            UTDebug::Error("view",str_replace(APP_ROOT."/modules","",$this->tempdir).$filename);
        }
        UTInc::MakeDir($this->cachedir);
        $comfilename=$this->cachedir."cache_".basename($tplfile);
        if($this->mode==1){
            $repcontent=$this->TempReplace(file_get_contents($tplfile));
            $handle=fopen($comfilename, 'w+');
            fwrite($handle,$repcontent);
            fclose($handle);
            unset($repcontent);
        }
        require_once($comfilename);
    }
    /**
     * 模板变量替换
     * 开发者可自定义增加方法，运算符号规范说明
     * “=>”用于表示连接，严格用于自定义方法与正则连接符号
     * “->”用于表示成员或键名，一般用于数组表示中括号[]
     * “,”逗号用于表示间隔连接，承上启下
     * @param string $content 模板内容
     */
    function TempReplace($content){
        $pattern=array(
		'/<\{\s*nav\s*=>\s*(.+?),(.+?)\s*\}>/i',
		'/<\{\s*item\s*=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*),\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}>/i',
		'/<\{\s*plugin\s*=>\s*(.+?)\s*\}>/i',
		'/<\{\s*split=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*),"(.+?)",([0-9]*)\s*\}>/i',
		'/<\{\s*split=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*),"(.+?)",all\s*\}>(.+?)<\{\s*\/split\s*\}>/is',
		'/<\{\s*split=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->([a-zA-Z0-9_\x7f-\xff]*),"(.+?)",([0-9]*)\s*\}>/i',
		'/<\{\s*split=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->([a-zA-Z0-9_\x7f-\xff]*),"(.+?)",all\s*\}>(.+?)<\{\s*\/split\s*\}>/is',
		'/<\{\s*substr\s*=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->(.+?),([0-9]*),([0-9]*)\s*\}>/i',
		'/<\{\s*substr\s*=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*),([0-9]*),([0-9]*)\s*\}>/i',
		'/<\{\s*(loop|foreach)\s*=>\s*\$(\S+)(\s*|\s*as\s*)\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}>(.+?)<\{\s*\/(loop|foreach)\s*\}>/is',
		'/<\{\s*(loop|foreach)\s*=>\s*\$(\S+)(\s*|\s*as\s*)\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->\$(\S+)\s*\}>(.+?)<\{\s*\/(loop|foreach)\s*\}>/is',
		'/<\{\s*datatree\s*=>\s*([0-9]*),\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*),(.+?)(\s*|\s*^[^,]*(?=,)\s*)\s*\}>/is',
        '/<\{\s*lang\s*=>\s*set->(.+?),(.+?)\s*\}>/is',
        '/<\{\s*lang\s*=>\s*(.+?)\s*\}>/i',
		'/<\{\s*lang\s*=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->(.+?)\s*\}>/i',
		'/<\{\s*modlang\s*=>\s*(.+?)\s*\}>/i',
		'/<\{\s*modlang\s*=>\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->(.+?)\s*\}>/i',
		'/<\{\s*page\s*=>\s*(.+?),(.+?),(.+?),(.+?)\s*\}>/i',
		'/<\{\s*pager\s*=>\s*(.+?),(.+?),(.+?),(.+?),([0-9]*)\s*\}>/i',
        '/<\{\s*eval\s*=>\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}>/i',
        '/<\{\s*(get|GET)\s*=>\s*(.+?)\s*\}>/is',
        '/<\{\s*(post|POST)\s*=>\s*(.+?)\s*\}>/is',
		'/<\{\s*php\s*=>\s*(.+?)\s*\}>/is',
		'/\s*return=>\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)->([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/i',
		'/\s*return=>\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/i',
		'/<\{\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*->\s*(.+?)\s*\}>/i',
		'/<\{\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}>/i',
		'/<\{\s*else\s*\}>/i',
		'/<\{\s*\/if\s*\}>/i'
        );
        $replacement=array(
        '<?php if("${1}"=="null"):if(rtrim(library\UsualToolInc\UTInc::CurPageUrl(),"/")==rtrim($GLOBALS["config"]["APPURL"],"/")):echo"${2}";endif;else:if(library\UsualToolInc\UTInc::Contain("${1}",library\UsualToolInc\UTInc::CurPageUrl())):echo"${2}";endif;endif;?>',
        '<?php echo"<div class=\"nav-item dropdown\"><a class=\"nav-link dropdown-toggle\" data-toggle=dropdown><i class=\"fa fa-link\"></i> Column</a><div class=\"dropdown-menu\">";$item=explode(",",$this->tplvars["${2}"]);for($i=0;$i<count($item);$i++):echo"<a class=\"dropdown-item\" href=?m=".$this->tplvars["${1}"]."&p=".explode(":",$item[$i])[1].">".explode(":",$item[$i])[0]."</a>";endfor;echo"</div></div>";?>',
		'<?php if(library\UsualToolInc\UTInc::Contain(",","${1}")):$pluginfile=explode(",","${1}");$HOOKPATH=APP_ROOT."/plugins/$pluginfile[0]/";if(is_dir($HOOKPATH)):if(library\UsualToolInc\UTInc::Contain(".php","$pluginfile[1]")):include_once $HOOKPATH.$pluginfile[1];else:echo"<iframe src=$HOOKPATH.$pluginfile[1] frameborder=0 id=external-frame></iframe><style>iframe{width:100%;margin:0 0 1em;border:0;}</style><script src=assets/js/autoheight.js></script>";endif;endif;else:$HOOKPATH=APP_ROOT."/plugins/${1}/";if(is_dir($HOOKPATH)):include_once $HOOKPATH."index.php";endif;endif;?>',
		'<?php $split=explode("${2}",$this->tplvars["${1}"]);echo $split[${3}];?>',
		'<?php $${1}=explode("${2}",$this->tplvars["${1}"]);for($i=0;$i<count($${1});$i++){?>${3}<?php }?>',
        '<?php $split=explode("${3}",$this->tplvars["${1}"]["${2}"]);echo $split[${4}];?>',
		'<?php $${1}=explode("${3}",$this->tplvars["${1}"]["${2}"]);for($i=0;$i<count($${1});$i++){?>${4}<?php }?>',
        '<?php echo library\UsualToolInc\UTInc::CutSubstr(library\UsualToolInc\UTInc::DeleteHtml($this->tplvars["${1}"]["${2}"]),${3},${4}); ?>',
		'<?php echo library\UsualToolInc\UTInc::CutSubstr(library\UsualToolInc\UTInc::DeleteHtml($this->tplvars["${1}"]),${2},${3}); ?>',
		'<?php if(empty($this->tplvars["${2}"])!=true){foreach($this->tplvars["${2}"] as $this->tplvars["${4}"]) { ?>${5}<?php }}?>',
        '<?php if(empty($this->tplvars["${2}"])!=true){foreach($this->tplvars["${2}"] as $this->tplvars["${4}"] => $this->tplvars["${5}"]) { ?>${6}<?php }}?>', 
		'<?php $tree=new library\UsualToolTree\UTTree();$tree->Init($this->tplvars["${2}"]);if(${1}==0):echo$tree->SubClass(${3},${4});elseif(${1}==1):$string="<option value=\\\$id \\\$selected \\\$disabled>\\\$spacer\\\$name</option>";echo$tree->GetTree(0,$string,${3},${4});elseif(${1}==2):$url=library\UsualToolInc\UTInc::ClearParam("id",library\UsualToolInc\UTInc::ClearParam("do",$_SERVER["QUERY_STRING"]));$string="<div class=row style=margin-bottom:15px;font-size:14px;><div class=col-9 data-id=\\\$id data-name=\\\$name>\\\$spacer\\\$name</div><div class=col-3><a id=\'tree-mod\' class=\'mr-2\' href=?".$url."&id=\\\$id&do=mon>Edit</a> <a id=\'tree-del\' href=?".$url."&id=\\\$id&do=del>Del</a></div></div>";echo$tree->GetTree(0,$string,${3});endif;?>',
        '<?php echo library\UsualToolLang\UTLang::LangSet("${1}",${2});?>',
        '<?php echo library\UsualToolLang\UTLang::LangData("${1}");?>',
		'<?php echo library\UsualToolLang\UTLang::LangData($this->tplvars["${1}"]["${2}"]);?>',
		'<?php if(library\UsualToolInc\UTInc::Contain(",","${1}")):$langdata=explode(",","${1}");echo library\UsualToolLang\UTLang::ModLangData($langdata[0],$langdata[1]);else:echo library\UsualToolLang\UTLang::ModLangData("${1}");endif;?>',
		'<?php echo library\UsualToolLang\UTLang::ModLangData($this->tplvars["${1}"]["${2}"]);?>',
		'<?php $Page=new library\UsualToolPage\UTPage($this->tplvars["${1}"],$this->tplvars["${2}"],$this->tplvars["${3}"],$this->tplvars["${4}"],2);echo$Page->ShowPager();?>',
		'<?php $Page=new library\UsualToolPage\UTPage($this->tplvars["${1}"],$this->tplvars["${2}"],$this->tplvars["${3}"],$this->tplvars["${4}"],${5});echo$Page->ShowPager();?>',
		'<?php eval($this->tplvars["${1}"]);?>',
        '<?php echo$_GET["${2}"];?>',
        '<?php echo$_POST["${2}"];?>',
        '<?php ${1}?>',
		'$this->tplvars["${1}"]["${2}"]',
		'$this->tplvars["${1}"]',
		'<?php echo $this->tplvars["${1}"]["${2}"]; ?>',
		'<?php echo $this->tplvars["${1}"]; ?>',
		'<?php }else{?>',
		'<?php }?>'        
        );
        $content=preg_replace_callback(
            "/<\{\s*include\s+[\"\'](.+?)[\"\']?\s*\}>/i",
            function($matches){
                $rule='/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/i';
                if(UTInc::Contain("$",$matches[1])){
                    preg_match($rule,$matches[1],$matche);
                    $tempurl=preg_replace($rule,$this->tplvars[$matche[1]],$matches[1]);
                    return file_get_contents($tempurl);
                }else{
                    return file_get_contents($this->tempdir."$matches[1]");
                }
            },
            $content
        );
        $content=preg_replace_callback(
            "/<\{\s*(db|inc|url)\s*=>\s*(.+?)\s*\}>/i",
            function($matches){
                if(UTInc::Contain("inc",$matches[1])){
                    return $this->StripTags("<?php echo library\UsualToolInc\UTInc::$matches[2];?>");
                }elseif(UTInc::Contain("db",$matches[1])){
                    return $this->StripTags("<?php echo library\UsualToolData\UTData::$matches[2];?>");
                }elseif(UTInc::Contain("url",$matches[1])){
                    return $this->StripTags("<?php echo library\UsualToolRoute\UTRoute::$matches[2];?>");
                }
            },
            $content
        );
        $content=preg_replace_callback(
            "/<\{\s*if\s*(.+?)\s*\}>/i",
            function($matches){
                return $this->StripTags("<?php if($matches[1]){?>");
            },
            $content
        );
        $content=preg_replace_callback(
            "/<\{\s*else\s*if\s*(.+?)\s*\}>/i",
            function($matches){
                return $this->StripTags("<?php }elseif($matches[1]){?>");
            },
            $content
        );
        $repcontent=preg_replace($pattern,$replacement,$content);
        if(preg_match('/<\{([^(\}>)]{1,})\}>/',$repcontent)){
            $repcontent=$this->TempReplace($repcontent);
        }
        return $repcontent;
    }
    /**
     * 标签转换
     * @param string $expr 转换标签
     */
    function StripTags($expr,$statement=''){
        $var_pattern='/\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*/is';
        $expr = preg_replace($var_pattern, '$this->tplvars["${1}"]', $expr);
        $expr = str_replace("\\\"", "\"", $expr);
        $statement = str_replace("\\\"", "\"", $statement);
        return $expr.$statement;
    }
    /**
     * 模板管理器
     * @return array
     */
    function GetTempFile($path=APP_ROOT.'/modules/'){
        $file_arr=UTInc::GetDir($path);
        $admin=array();
        $fornt=array();
        foreach($file_arr as $file){
            if(is_dir(APP_ROOT."/modules/".$file."/skin/front/")){
                $dirx = scandir(APP_ROOT."/modules/".$file."/skin/front/");
                foreach ($dirx as $valuex){
                    if($valuex == '.' || $valuex == '..'){
                            continue;
                      }else{
                            $front[$file][] = $valuex;
                      }
                }
            }   
            if(is_dir(APP_ROOT."/modules/".$file."/skin/admin/")){
                $dir = scandir(APP_ROOT."/modules/".$file."/skin/admin/");
                foreach ($dir as $value){
                    if($value == '.' || $value == '..'){
                            continue;
                      }else{
                            $admin[$file][] = $value;
                      }
                }
            }            
        }
        return array("admin"=>$admin,"front"=>$front);
    }    
    /**
     * 选用函数：自定义生成静态
     * index.php?$m=demo&p=tests&cat=0&page=1 => $m/$p-$cat_$page.html  => demo/tests-0_1.html
     * index.php?$m=demo&p=tests&page=1       => $m/$p_$page.html       => demo/tests_1.html
     * index.php?$m=demo&p=tests&cat=0        => $m/$p-$cat.html        => demo/tests-0.html
     * index.php?$m=demo&p=tests              => $m/$p.html             => demo/tests.html
     * index.php?$m=demo&p=test&id=1          => $m/$p-$id.html         => demo/test-1.html
     * @param string $filename 模板文件
     * @param string $htmlname 保存静态文件
     * @param string $weburl 应用地址
     * @param int $rewrite 伪静态状态
     */
    function MakeHtml($filename,$htmlname,$weburl,$rewrite='0'){
        $tplfile=$this->tempdir.$filename;
        if(!file_exists($tplfile)){
            exit();
        }
        $comfilename=$this->cachedir."cache_".basename($tplfile);
        if($this->mode==1){
            if(!file_exists($filename) || filemtime($comfilename) < filetime($tplfile)){
                $repcontent=$this->TempReplace(file_get_contents($tplfile));
            }
            $handle=fopen($comfilename, 'w+');
            fwrite($handle, $repcontent);
            fclose($handle);
            unset($repcontent);
        }
        ob_start();
        require_once($comfilename);
        $content = ob_get_contents();
        ob_end_clean();
        $fp = fopen($htmlname,"w");
        $content=str_replace('\'','"',$content);
        if($rewrite=="0"):
            $content=preg_replace('/(href|HREF)="index.php\?m\=([a-zA-Z0-9]*)&p\=([a-zA-Z0-9]*)&catid\=([0-9]*)&page\=([0-9]*)"(.*?)/is','$1="html/$2/$3-$4_$5.html"$6',$content);
            $content=preg_replace('/(href|HREF)="index.php\?m\=([a-zA-Z0-9]*)&p\=([a-zA-Z0-9]*)&page\=([0-9]*)"(.*?)/is','$1="html/$2/$3_$4.html"$5',$content);
            $content=preg_replace('/(href|HREF)="index.php\?m\=([a-zA-Z0-9]*)&p\=([a-zA-Z0-9]*)&catid\=([0-9]*)"(.*?)/is','$1="html/$2/$3-$4.html"$5',$content);
            $content=preg_replace('/(href|HREF)="index.php\?m\=([a-zA-Z0-9]*)&p\=([a-zA-Z0-9]*)"(.*?)/is','$1="html/$2/$3.html"$4',$content);
            $content=preg_replace('/(href|HREF)="index.php\?m\=([a-zA-Z0-9]*)&p\=([a-zA-Z0-9]*)&id\=([0-9]*)"(.*?)/is','$1="html/$2/$3-$4.html"$5',$content);
        else:
            $content=preg_replace('/(href|HREF)="([a-zA-Z0-9]*)/([a-zA-Z0-9]*)\.html\?catid\=([0-9]*)&page\=([0-9]*)"(.*?)/is','$1="html/$2/$3-$4_$5.html"$6',$content);
            $content=preg_replace('/(href|HREF)="([a-zA-Z0-9]*)/([a-zA-Z0-9]*)\.html\?page\=([0-9]*)"(.*?)/is','$1="html/$2/$3_$4.html"$5',$content);
            $content=preg_replace('/(href|HREF)="([a-zA-Z0-9]*)/([a-zA-Z0-9]*)\.html\?catid\=([0-9]*)"(.*?)/is','$1="html/$2/$3-$4.html"$5',$content);
            $content=preg_replace('/(href|HREF)="([a-zA-Z0-9]*)/([a-zA-Z0-9]*)\.html"(.*?)/is','$1="html/$2/$3.html"$4',$content);
            $content=preg_replace('/(href|HREF)="([a-zA-Z0-9]*)/([a-zA-Z0-9]*)-([0-9]*)\.html"(.*?)/is','$1="html/$2/$3-$4.html"$5',$content);
        endif;
            $content=preg_replace('/(href|src|HREF|SRC)="(?!http)(.*?)"(.*?)/is','$1="'.$weburl.'/$2"$3',$content);
        fwrite($fp,$content);
        fclose($fp);
    }
}