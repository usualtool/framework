<?php
namespace library\UsualToolTree;
use library\UsualToolData\UTData;
/**
       * --------------------------------------------------------       
       *  |                  █   █ ▀▀█▀▀                    |           
       *  |                  █▄▄▄█   █                      |           
       *  |                                                 |           
       *  | Author:HuangDou   Email:292951110@qq.com        |           
       *  | QQ-Group:583610949                              |           
       *  | Applicable to Apache 2.0 protocol.              |           
       * --------------------------------------------------------       
*/
/**
 * 实例化树型结构
 */
class UTTree{
    public $arr = array();
    public $icon = array('&nbsp;&nbsp;<span style="color:#A9A9A9;">┊</span> ','&nbsp;&nbsp;┣┈┈ ','&nbsp;&nbsp;┗┈┈ ','&nbsp;&nbsp;┞┈┈ ');
    public $nbsp = " ";
    public $ret = '';
    /**
     * 初始化传入数组
     * 固定结构：array('id'=>'1','bid'=>'2','name'=>'3','lang'=>'zh')
     * @param array $arr
     */
    public function Init($arr=array()){
        $this->arr = $arr;
        $this->ret = '';
        return is_array($arr);
    }
    /**
     * 计算父级数组
     * @param int $id 父级序号
     * @return array
     */
    public function GetParent($id){
        $newarr = array();
        if(!isset($this->arr[$id])) return false;
        $pid = $this->arr[$id]['bid'];
        $pid = $this->arr[$pid]['bid'];
        if(is_array($this->arr)){
            foreach($this->arr as $tid => $a){
                if($a['bid'] == $pid) $newarr[$tid] = $a;
            }
        }
        return $newarr;
    }
    /**
     * 计算子级数组
     * @param int $myid 子级序号
     * @return array
     */
    public function GetChild($id){
        $a = $newarr = array();
        if(is_array($this->arr)){
            foreach($this->arr as $tid => $a){
                if($a['bid'] == $id) $newarr[$tid] = $a;
            }
        }
        return $newarr ? $newarr : false;
    }
    /**
     * 计算当前的数组
     * @param int $id 序号
     * @return array
     */
    public function GetPos($id,&$newarr){
        $a = array();
        if(!isset($this->arr[$id])) return false;
        $newarr[] = $this->arr[$id];
        $pid = $this->arr[$id]['bid'];
        if(isset($this->arr[$pid])){
            $this->GetPos($pid,$newarr);
        }
        if(is_array($newarr)){
            krsort($newarr);
            foreach($newarr as $v){
                $a[$v['id']] = $v;
            }
        }
        return $a;
    }
    /**
     * 样式一：得出树型结构模型（单选）
     * @param int $id 序号
     * @param string $str 树代码样式
     * @param int $sid 被选中的序号
     * @param int $did 被选中的序号
     * @param string $adds 修饰前缀
     * @param string $str_group 间隔
     * @return array
     */
    public function GetTree($id,$str,$sid=0,$did=0,$adds='',$str_group=''){
        $number=1;
        $child = $this->GetChild($id);
        if(is_array($child)){
            $total = count($child);
            foreach($child as $tid=>$value){
                $j=$k='';
                if($number==$total){
                    $j .= $this->icon[2];
                    $k = $adds ? $this->icon[0] : '';
                }else{
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer = $adds ? $adds.$j : '';
                $selected = $tid==$sid ? 'selected' : '';
                $disabled = $tid==$did ? 'disabled' : '';
                @extract($value);
                $bid == 0 && $str_group ? eval("\$nstr = \"$str_group\";") : eval("\$nstr = \"$str\";");
                $this->ret .= $nstr;
                $nbsp = $this->nbsp;
                $this->GetTree($tid,$str,$sid,$did,$adds.$k.$nbsp,$str_group);
                $number++;
            }
        }
        return $this->ret;
    }
    /**
     * 样式二：得出树型结构模型（多选）
     * @param int $id 序号
     * @param string $str 树代码样式
     * @param int $sid 被选中的序号
     * @param string $adds 修饰前缀
     * @return array
     */
    public function GetTreeMulti($id, $str, $sid = 0, $adds = ''){
        $number=1;
        $child = $this->GetChild($id);
        if(is_array($child)){
            $total = count($child);
        foreach($child as $tid=>$a){
            $j=$k='';
            if($number==$total){
                $j .= $this->icon[2];
            }else{
                $j .= $this->icon[1];
                $k = $adds ? $this->icon[0] : '';
            }
            $spacer = $adds ? $adds.$j : '';
            $selected = $this->Have($sid,$tid) ? 'selected' : '';
            @extract($a);
            eval("\$nstr = \"$str\";");
            $this->ret .= $nstr;
            $this->GetTreeMulti($tid, $str, $sid, $adds.$k.' ');
            $number++;
        }
        }
        return $this->ret;
    }
    /**
     * 样式三：按指定ID得出树型结构模型
     * @param int $id 序号
     * @param string $str 第一种树代码样式
     * @param string $str 第二种树代码样式
     * @param int $sid 被选中的序号
     * @param string $adds 修饰前缀
     * @return string
     */
    public function GetTreeCategory($id, $str, $str2, $sid = 0, $adds = ''){
        $number=1;
        $child = $this->GetChild($id);
        if(is_array($child)){
            $total = count($child);
        foreach($child as $tid=>$a){
            $j=$k='';
            if($number==$total){
                $j .= $this->icon[2];
            }else{
                $j .= $this->icon[1];
                $k = $adds ? $this->icon[0] : '';
            }
            $spacer = $adds ? $adds.$j : '';

            $selected = $this->Have($sid,$tid) ? 'selected' : '';
            @extract($a);
            if (empty($html_disabled)) {
                eval("\$nstr = \"$str\";");
            } else {
                eval("\$nstr = \"$str2\";");
            }
            $this->ret .= $nstr;
            $this->GetTreeCategory($tid, $str, $str2, $sid, $adds.$k.' ');
            $number++;
        }
        }
        return $this->ret;
    }
    /**
     * 样式四：JQ TreeView插件得出伸缩型树型结构
     * @param int $id 序号
     * @param int $effected_id TreeView目录数ID
     * @param string $str 末级
     * @param string $str2 目录级
     * @param int $showlevel 层级
     * @param string $style 目录样式CLASS
     * @param int $currentlevel 当前层级
     * @return string
     */
    function GetTreeView($id,$effected_id='USUALTOOL',$str="<span class='file'>\$name</span>", $str2="<span class='folder'>\$name</span>" ,$showlevel = 0 ,$style='filetree' , $currentlevel = 1,$recursion=FALSE) {
        $child = $this->GetChild($id);
        if(!defined('EFFECTED_INIT')){
            $effected = ' id="'.$effected_id.'"';
            define('EFFECTED_INIT', 1);
        } else {
            $effected = '';
        }
        $placeholder = '<ul><li><span class="placeholder"></span></li></ul>';
        if(!$recursion) $this->str .='<ul'.$effected.'  class="'.$style.'">';
        foreach($child as $tid=>$a) {
            @extract($a);
            if($showlevel > 0 && $showlevel == $currentlevel && $this->GetChild($tid)) $folder = 'hasChildren'; //如设置显示层级模式@2011.07.01
            $floder_status = isset($folder) ? ' class="'.$folder.'"' : '';
            $this->str .= $recursion ? '<ul><li'.$floder_status.' id=\''.$tid.'\'>' : '<li'.$floder_status.' id=\''.$tid.'\'>';
            $recursion = FALSE;
            if($this->get_child($tid)){
                eval("\$nstr = \"$str2\";");
                $this->str .= $nstr;
                if($showlevel == 0 || ($showlevel > 0 && $showlevel > $currentlevel)) {
                    $this->GetTreeView($tid, $effected_id, $str, $str2, $showlevel, $style, $currentlevel+1, TRUE);
                } elseif($showlevel > 0 && $showlevel == $currentlevel) {
                    $this->str .= $placeholder;
                }
            } else {
                eval("\$nstr = \"$str\";");
                $this->str .= $nstr;
            }
            $this->str .=$recursion ? '</a></li></ul>': '</a></li>';
        }
        if(!$recursion)  $this->str .='</ul>';
        return $this->str;
    }
    /**
     * 样式五：两级列表展示
     * @param int $id 序号
     * @param string $str 代码样式
     * @return string
     */
    public function SubClass($id='0',$link=''){
        if(strpos($link,'?')!==false){
            $classlink=$link."&";
        }else{
            $classlink=$link."?";
        }
        $cats = $this->GetChild($id);
        $data="";
        $data.="<ul style='padding-left:0;margin-bottom:0;'>";
            foreach($cats as $a){
                $data.="<li style='display:block;margin-bottom:10px;'>";
                    $data.="<a href='".$classlink."cid=".$a["id"]."' style='color:#666;'><strong>".$a["name"]."</strong></a>";
                    $data.= $this->ChildClass($a["id"],$classlink);
                $data.="</li>";
            }
        $data.="</ul>";
        return $data;
    }
    public function ChildClass($id,$link){
        $cats = $this->GetChild($id);
        $data="";
        $data.="<ul style='padding-left:0;margin-top:5px;'>";
            foreach($cats as $b){
                $data.="<li style='display:inline;margin-right:15px;'>";
                $data.="<a style='color:#666;' href='".$link."cid=".$b["id"]."'>".$b["name"]."</a>";
                $data.="</li>";
            }
        $data.="</ul>";
        return $data;
    }
    /**
     * 样式六：无限分类附加内容列表
     * @param int $id 序号
     * @param string $catlink 分类链接前缀
     * @param string $table 内容表
     * @param string $catfield 内容表分类字段
     * @param string $titfield 内容表标题字段
     * @param int    $querynum 提取最新内容数量
     * @param string $titlink 内容链接前缀
     * @return string
     */
    public function SubClassList($id='0',$catlink,$table,$catfield,$titfield,$querynum='5',$titlink=''){
        if(strpos($catlink,'?')!==false){
            $catlink=str_replace("&&&","&",str_replace("&&","&",$catlink."&"));
        }else{
            $catlink=str_replace("&&&","&",str_replace("&&","&",$catlink."?"));
        }
        if(strpos($titlink,'?')!==false){
            $titlink=str_replace("&&&","&",str_replace("&&","&",$titlink."&"));
        }else{
            $titlink=str_replace("&&&","&",str_replace("&&","&",$titlink."?"));
        }
        $cats = $this->GetChild($id);
        $data="";
        $data.="<ul id='cat'>";
            foreach($cats as $a){
                if($a["bid"]==0){
                    $data.="<li style='line-height:30px;' id='catlist'>";
                }else{
                    $data.="<li style='display:block;line-height:30px;' id='catlist'>";
                }
                    $data.="<a href='".$catlink."cid=".$a["id"]."' style='color:#666;'><strong>".$a["name"]."</strong></a><ul>";
                    $thedata=UTData::QueryData($table,"",$catfield."=".$a["id"],"id desc","0,".$querynum)["querydata"];
                    foreach($thedata as $rows){  
                        $data.="<li id='titlist'><a href='".$titlink."id=".$rows["id"]."'>".$rows[$titfield]."</a></li>";
                    }
                    $data.= $this->SubClassList($a["id"],$catlink,$table,$catfield,$titfield,$querynum,$titlink);
                $data.="</ul></li>";
            }
        $data.="</ul>";
        return $data;
    }
    /**
     * 样式七：顶级分类下所有记录（含本级和子级）
     * @param int $id 分类序号
     * @param string $table 内容表
     * @param string $catfield 内容表分类字段
     * @param string $titfield 内容表标题字段
     * @param int    $querynum 提取最新内容数量
     * @param string $titlink 内容链接前缀
     * @return string
     */
    public function SubDataList($id='0',$table,$catfield,$titfield,$querynum='5',$titlink=''){
        if(strpos($titlink,'?')!==false){
            $titlink=str_replace("&&&","&",str_replace("&&","&",$titlink."&"));
        }else{
            $titlink=str_replace("&&&","&",str_replace("&&","&",$titlink."?"));
        }
        $cats = $this->GetChild($id);
        $data="";
            foreach($cats as $a){
                    $thedata=UTData::QueryData($table,"",$catfield."=".$a["id"],"id desc","0,".$querynum)["querydata"];
                    foreach($thedata as $rows){  
                        $data.="<p id='titlist'><a href='".$titlink."id=".$rows["id"]."'>".$rows[$titfield]."</a></p>";
                    }
                    $data.= $this->SubDataList($a["id"],$table,$catfield,$titfield,$querynum,$titlink);
            }
        return $data;
    }
    /**
     * 判断是否包含某个ID
     * @param string $list 一个ID集合字符串
     * @param string $item 要查询的ID字符串
     * @return bool
     */
    private function Have($list,$item){
        return(strpos(',,'.$list.',',','.$item.','));
    }
}