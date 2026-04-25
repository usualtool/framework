<?php
namespace library\UsualToolPower;
use library\UsualToolInc\UTInc;
use library\UsualToolDebug\UTDebug;
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
 * 全局权限中间件
 */
class UTPower{
    protected $deve;
    protected $path;
    protected $page;
    /**
     * 构造函数
     * @param bool $deve 前/后端判断
     * @param string $path 模块路径 (MODULE_PATH)
     */
    public function __construct($deve,$path){
        $this->deve = $deve;
        $this->path = $path;
    }
    /**
     * 设置当前页面标识
     */
    public function SetPage($page){
        $this->page = $page;
        return $this;
    }
    /**
     * 执行权限检查
     * @return bool 返回 true 表示通过，false 表示被拦截
     */
    public function Check(){
        global $app,$m,$p,$config;
        //权限验证路径
        //格式：模块/端（front/admin）/页面名称（不含.php后缀）
        $power_page=$this->deve ? "ADMIN_POWER_PAGE" : "FRONT_POWER_PAGE";
        //路径白名单（直接通行）
        $power_out=$this->deve ? "ADMIN_POWER_OUT" : "FRONT_POWER_OUT";
        $_power_page_=$config[$power_page] ?? '';
        $_power_out_=$config[$power_out] ?? '';
        if(!empty($_power_page_) && !UTInc::Contain($this->page,$_power_out_)){
            $_auth_=$this->path."/".$_power_page_.".php";
            if(file_exists($_auth_)){
                require_once $_auth_;
            }else{
                UTDebug::Error("view",$this->path."/".$_power_page_);
            }
        }else{
            return true;
        }
    }
}