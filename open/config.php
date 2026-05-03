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
require dirname(__DIR__).'/'.'autoload.php';
/**
 * 业务级全局路径
 */
//模块路径
defined('MODULE_PATH') or define('MODULE_PATH',APP_ROOT.'/modules');
//模板工程路径
defined('TEMPLATE_PATH') or define('TEMPLATE_PATH',APP_ROOT.'/template');
//插件路径
defined('PLUGIN_PATH') or define('PLUGIN_PATH',APP_ROOT.'/plugins');
//计划任务路径
defined('TASK_PATH') or define('TASK_PATH',APP_ROOT.'/task');
//语言包路径
defined('LANG_PATH') or define('LANG_PATH',OPEN_ROOT.'/lang');
/**
 * 其他自定义配置（业务变量）
 */
$custom=[
    //全局配置中未涉及配置
    //例如各类密钥等，未设置保留为空
];