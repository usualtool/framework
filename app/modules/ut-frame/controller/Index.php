<?php
namespace Controller\Ut_frame;
use library\UsualToolInc\UTInc;
use Model\Ut_frame\Index as UtIndex; 
class Index{
    private $app;
    public function __construct() {
        global $app;
        $this->app = $app;
    }
    /**
     * 默认动作 (对应index或空action)
     */
    public function index(){
        // 1. 实例化模型
        $model = new UtIndex(); 
        // 2. 获取数据
        $data = $model->getStatus();
        // 3. 注入数据
        $this->app->Runin(array("setup", "title"), array($data['setup'], $data['title']));
        // 4. 渲染模板
        $this->app->Open("index.cms");
    }
}