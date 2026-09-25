<?php
namespace Model\Ut_frame;
use usualtool\Lib\Inc;
class Index{
    /**
     * 获取系统状态
     * @return array
     */
    public function getStatus(){
        $isDev = Inc::InstallDev();
        return [
            'setup' => $isDev ? 1 : 0,
            'title' => 'Hello UsualTool Framework'
        ];
    }
}