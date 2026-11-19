<?php
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
use usualtool\Lib\Kernel;
use usualtool\Lib\Request;
require_once __DIR__.'/config.php';
/**
 * 路由分发
 */
try{
    Kernel::Dispatch();
}catch(Request $e){
    Kernel::Terminate($e);
}