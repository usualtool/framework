<?php
namespace Model\Ut_frame;
use library\UsualToolInc\UTInc;
class Index{
    /**
     * 获取系统状态
     * @return array
     */
    public function getStatus(){
        $isDev = UTInc::InstallDev();
        return [
            'setup' => $isDev ? 1 : 0,
            'title' => 'Hello UsualTool Framework'
        ];
    }
}