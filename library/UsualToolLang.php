<?php
namespace library\UsualToolLang;
use library\UsualToolInc\UTInc;
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
 * 解析本地化语言包
 */
class UTLang{
    /**
     * 获取语言文件列表
     * @return array
     */
    public static function GetLang($path=OPEN_ROOT."/lang/"){
        $langs = array();
        if(!is_dir($path)):
            return $langs;
        endif;
        $current_dir = opendir($path);
        while(($file = readdir($current_dir)) !== false):
            if($file == '.' || $file == '..'):
                continue;
            endif;
            $sub_dir=$path."/".$file;
            if(is_dir($sub_dir)):
                $sub_langs = self::GetLang($sub_dir);
                $langs = array_merge($langs, $sub_langs);
            elseif(UTInc::Contain("json", $file)):
                $langs[] = UTInc::StrSubstr("lg-", ".json", basename($file));
            endif;
        endwhile;
        closedir($current_dir);
        return $langs;
    } 
    /**
     * 获取单词对应语言包翻译，解析L部分
     * @param string $word 单词
     * @param string $type 语言
     * @return string
     */
    public static function LangData($word,$type=''){
        if(!empty($type)):
            $langdata=json_decode(file_get_contents(OPEN_ROOT."/lang/lg-".$type.".json"),true);
        else:
            $langdata=json_decode(file_get_contents(OPEN_ROOT."/lang/lg-".self::GetActiveLang().".json"),true);
        endif;
        if(array_key_exists($word,$langdata["l"])){
        $langword=$langdata["l"]["".$word.""];
            return $langword;
        }else{
            return $word;
        }
    }
    /**
     * 在模块中获取单词对应语言包翻译，解析L部分
     * @param string $word 单词
     * @return string
     */
    public static function ModLangData($word,$module=''){
        global$modpath;
        if(!empty($module)){
            $langdata=json_decode(file_get_contents(APP_ROOT."/modules/".$module."/lang/lg-".self::GetActiveLang().".json"),true);
        }else{
            $langdata=json_decode(file_get_contents($modpath."/lang/lg-".self::GetActiveLang().".json"),true);
        }
        if(array_key_exists($word,$langdata["l"])){
            $langword=$langdata["l"]["".$word.""];
            return $langword;
        }else{
            return $word;
        }
    }
    /**
     * 获取语言包设置参数部分，解析S部分
     * @param string $word 单词
     * @param string $type 语言
     * @return string
     */
    public static function LangSet($word,$type=''){
        if(!empty($type)):
            $langdata=json_decode(file_get_contents(OPEN_ROOT."/lang/lg-".$type.".json"),true);
        else:
            $langdata=json_decode(file_get_contents(OPEN_ROOT."/lang/lg-".self::GetActiveLang().".json"),true);
        endif;
        $langword=$langdata["s"]["".$word.""];
        return $langword;
    }
    /**
     * 获取语言包所有内容
     * @param string $path 语言包路径
     * @return array
     */
    public static function Lang($path=OPEN_ROOT.'/lang/'){
			  $lgfile=[];
        $current_dir = opendir($path);
        while(($file = readdir($current_dir)) !== false) {
            if(UTInc::Contain(".json",$file)!==false):
                $filename=explode(".json",$file);
                $lgfilename=str_replace("lg-","",str_replace($path,"",$filename[0]));
                $lgfile[]=array("speak"=>self::LangSet("speak",$lgfilename),"lgname"=>$lgfilename);
            endif;
        }
        return $lgfile;
    }
    /**
     * 当前语言
     */
    private static function GetActiveLang(){
        global $lang;
        return $lang ?? 'zh';
    }
}