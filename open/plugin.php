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
require_once __DIR__.'/'.'config.php';
use library\UsualToolInc\UTInc;
$c=$_GET["c"] ?? "";
$f=$_GET["f"] ?? "index";
$c && UTInc::Plugin($c,$f);