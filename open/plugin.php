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
(($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') || (http_response_code(403) && exit);
(in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) || (http_response_code(405) && exit);
$origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
$origin && ($oh = parse_url($origin, PHP_URL_HOST)) && ($ah = parse_url($config["APPURL"], PHP_URL_HOST)) && ($oh === $ah || str_ends_with($oh, '.' . $ah)) || (http_response_code(403) && exit);
$c = $_GET["c"] ?? "";
$f = $_GET["f"] ?? "index";
($c === '' || preg_match("/^[a-z0-9\-_]+$/i", $c)) || (http_response_code(400) && exit);
$c && UTInc::Plugin($c,$f);