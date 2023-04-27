<?php 
namespace library\UsualToolPage;
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
 * 实例化分页
 * 示例:$Page=new UTPage($totalpage,$page,$pagenum,$pagelink);echo $Page->ShowPager();
 * 默认支持bootstrap样式
 */
class UTPage{
    var $nums;
    var $current_page;
    var $sub_pages;
    var $pagenums;
    var $each_num;
    var $page_array = array();
    var $subpage_link;
    var $subpage_type;
    var $_lang = array( 
        'index_page' => '<ut data-localize="l.firstpage">首页</ut>', 
        'pre_page' => '<ut data-localize="l.previouspage">上页</ut>', 
        'next_page' => '<ut data-localize="l.nextpage">下页</ut>', 
        'last_page' => '<ut data-localize="l.lastpage">尾页</ut>', 
        'current_page' => '', 
        'total_page' => '<ut data-localize="l.totalpage">总页数</ut>:', 
        'current_show' => '<ut data-localize="l.currentpage">当前显示</ut>:', 
        'total_record' => '<ut data-localize="l.totalnum">总记录数</ut>:'
    ); 
    function __construct($total_page,$current_page,$sub_pages=10,$subpage_link='',$subpage_type=2){ 
        $this->Pager($total_page,$current_page,$sub_pages,$subpage_link,$subpage_type); 
    }
    /**
     * 执行分页
     * @param int $total_page 总页数
     * @param int $current_page 当前页数
     * @param int $sub_pages 每页显示数
     * @param string $subpage_link 分页链接
     * @param int $subpage_type 分页类型，默认为2
     */
    function Pager($total_page,$current_page,$sub_pages=10,$subpage_link='',$subpage_type=2){ 
        if(!$current_page){ 
            $this->current_page=1; 
        }else{ 
            $this->current_page=intval($current_page); 
        } 
        $this->sub_pages=intval($sub_pages); 
        $this->pagenums=ceil($total_page); 
        if($subpage_link){ 
            if(strpos($subpage_link,'?page=') === false AND strpos($subpage_link,'&page=') === false){ 
                if(substr($subpage_link, -1)=="?" || substr($subpage_link, -1)=="&"){
                    $subpage_link .="page=";}
                else{
                    $subpage_link .= (strpos($subpage_link,'?') === false ? '?' : '&') . 'page='; 
                }
            } 
        } 
        $this->subpage_link=$subpage_link ? $subpage_link : $_SERVER['PHP_SELF'] . '?page='; 
        $this->subpage_type = $subpage_type; 
        $this->each_num=5;
    }
    /**
     * 展示分页
     */
    function ShowPager(){ 
        if($this->subpage_type == 1){ 
            return $this->PageListOne(); 
        }elseif ($this->subpage_type == 2){ 
            return $this->PageListTwo(); 
        } 
    }
    function InitArray(){ 
        for($i=0;$i<$this->each_num;$i++){ 
            $this->page_array[$i]=$i; 
        } 
        return $this->page_array; 
    }
    function ConstructNumPage(){ 
        if($this->pagenums < $this->each_num){
            $current_array=array();
            for($i=0;$i<$this->pagenums;$i++){
                $current_array[$i]=$i+1;
            }
        }else{
            $current_array=$this->InitArray();
            if($this->current_page <=3 && $this->current_page>0){
                for($i=0;$i<count($current_array);$i++){
                $current_array[$i]=$i+1;
                }
            }elseif($this->current_page <= $this->pagenums && $this->current_page > $this->pagenums - $this->each_num + 1 ){
                for($i=0;$i<count($current_array);$i++){
                $current_array[$i]=($this->pagenums)-($this->each_num)+1+$i;
                }
            }else{
                for($i=0;$i<count($current_array);$i++){
                $current_array[$i]=$this->current_page-2+$i;
                }
            }
        }
        return $current_array; 
    }
    /**
     * 分页类型1
     */
    function PageListOne(){ 
        $subPageCss1Str="<div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>"; 
        $subPageCss1Str."<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>". $this->_lang['current_page'] . $this->current_page." / " .$this->pagenums."</a></li>"; 
        if($this->current_page > 1){ 
            $firstPageUrl=$this->subpage_link."1"; 
            $prewPageUrl=$this->subpage_link.($this->current_page-1); 
            $subPageCss1Str.="<li class='paginate_button page-item'><a href='$firstPageUrl' class='page-link'>{$this->_lang['index_page']}</a></li>"; 
            $subPageCss1Str.="<li class='paginate_button page-item'><a href='$prewPageUrl' class='page-link'>{$this->_lang['pre_page']}</a></li>"; 
        }else { 
            $subPageCss1Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['index_page']}</a></li>"; 
            $subPageCss1Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['pre_page']}</a></li>"; 
        }   
        if($this->current_page < $this->pagenums){ 
            $lastPageUrl=$this->subpage_link.$this->pagenums; 
            $nextPageUrl=$this->subpage_link.($this->current_page+1); 
            $subPageCss1Str.="<li class='paginate_button page-item'><a href='$nextPageUrl' class='page-link'>{$this->_lang['next_page']}</a></li>"; 
            $subPageCss1Str.="<li class='paginate_button page-item'><a href='$lastPageUrl' class='page-link'>{$this->_lang['last_page']}</a></li>"; 
        }else{ 
            $subPageCss1Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['next_page']}</a></li>"; 
            $subPageCss1Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['last_page']}</a></li>"; 
        } 
        $subPageCss1Str.="</ul></div>";
        return $subPageCss1Str; 
    }
    /**
     * 分页类型2
     */
    function PageListTwo(){ 
	    $subPageCss2Str="<div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>"; 
	    $subPageCss2Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>".$this->_lang['current_page'] . $this->current_page."/" . $this->pagenums."</a></li>"; 
	    if($this->current_page > 1){ 
		    $firstPageUrl=$this->subpage_link."1"; 
		    $prewPageUrl=$this->subpage_link.($this->current_page-1); 
		    $subPageCss2Str.="<li class='paginate_button page-item'><a href='$firstPageUrl' class='page-link'>{$this->_lang['index_page']}</a></li>"; 
		    $subPageCss2Str.="<li class='paginate_button page-item'><a href='$prewPageUrl' class='page-link'>{$this->_lang['pre_page']}</a></li>"; 
	    }else { 
		    $subPageCss2Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['index_page']}</a></li>"; 
		    $subPageCss2Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['pre_page']}</a></li>"; 
	    } 
	    $a=$this->ConstructNumPage(); 
	    for($i=0;$i<count($a);$i++){ 
		    $s=$a[$i]; 
		    if($s == $this->current_page){ 
		        $subPageCss2Str.="<li class='paginate_button page-item active'><a class='page-link'>{$s}</a></li>"; 
		    }else{ 
		        $url=$this->subpage_link.$s; 
		        $subPageCss2Str.="<li class='paginate_button page-item'><a class='page-link' href='{$url}'>{$s}</a></li>"; 
		    } 
	    } 
	    if($this->current_page < $this->pagenums){ 
		    $lastPageUrl=$this->subpage_link.$this->pagenums; 
		    $nextPageUrl=$this->subpage_link.($this->current_page+1); 
		    $subPageCss2Str.="<li class='paginate_button page-item'><a class='page-link' href='$nextPageUrl'>{$this->_lang['next_page']}</a></li>"; 
		    $subPageCss2Str.="<li class='paginate_button page-item'><a class='page-link' href='$lastPageUrl'>{$this->_lang['last_page']}</a></li>"; 
	    }else{ 
		    $subPageCss2Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['next_page']}</a></li>"; 
	        $subPageCss2Str.="<li class='paginate_button page-item disabled'><a class='page-link ut-disabled'>{$this->_lang['last_page']}</a></li>"; 
	    } 
        $subPageCss2Str.="</ul></div>";
	    return $subPageCss2Str; 
    }
    /**
     * 析构函数
     */
    function __destruct(){
        unset($nums); 
        unset($current_page); 
        unset($sub_pages); 
        unset($pagenums); 
        unset($each_num);
        unset($page_array); 
        unset($subpage_link); 
        unset($subpage_type); 
    }
}