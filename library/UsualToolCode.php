<?php
namespace library\UsualToolCode;
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
 * 实例化验证码
 */
class UTCode{
    /**
     * 随机因子设置
     * 去除歧义字符
     */
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    /**
     * 验证码设置
     */
    private $code;
    private $codelen = 4;
    private $width = 130;
    private $height = 30;
    private $img;
    private $font;
    private $fontsize = 15;
    private $fontcolor;
    public function __construct(){
        session_start();
        $this->font = OPEN_ROOT. '/assets/fonts/captcha.ttf';
    }
    /**
     * 创建验证码
     */    
    private function CreateCode(){
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }
    /**
     * 创建背景
     */ 
    private function CreateBg(){
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }
    /**
    * 创建字符
    */ 
    private function CreateFont(){
        $_x = $this->width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }
    /**
    * 创建干扰元素
    */ 
    private function CreateLine(){
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }
    /**
    * 输出验证码
    */ 
    private function Output(){
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }
    /**
    * 生成接口
    */ 
    public function CreateImage(){
        $this->CreateBg();
        $this->CreateCode();
        $this->CreateLine();
        $this->CreateFont();
        $this->Output();
    }
    /**
    * 获取验证码
    */ 
    public function GetCode(){
        return strtolower($this->code);
    }
}