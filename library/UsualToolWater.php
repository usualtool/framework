<?php
namespace library\UsualToolWater;
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
 * 水印
 * 文字:UTWater::MarkWater("text","1.jpg","usualtool.com",5,"#B5B5B5","14");
 * 图片:UTWater::MarkWater("image","1.jpg","water.png",8);
*/
class UTWater{
    /**
     * @param string $type 类型 值为:text或image
     * @param string $photo 需要加水印的图片
     * @param string $content 水印内容 当type为text时该值为文字,当type为image时该值为水印图片地址
     * @param int $waterpos 水印位置 0为随机,1为顶端居左,2为顶端居中,3为顶端居右,4为中部居左,5为中部居中,6为中部居右,7为底端居左,8为底端居中,9为底端居右
     * @param string $fontcolor 文字颜色,图片水印忽略
     * @param int $fontsize 文字大小,图片水印忽略
    */
    public static function MarkWater($type,$photo,$content,$waterpos,$fontcolor='#B5B5B5',$fontsize='14'){
        $fontcolor=UTWater::Hex2Rgb($fontcolor);
        $typeerroMsg = "Only support for jpg/png/gif format."; 
        if($type=="image" && file_exists($content)): 
            $water_info = getimagesize($content); 
            $water_w = $water_info[0];
            $water_h = $water_info[1];
            switch ($water_info[2]):
                case 1:$water_im = imagecreatefromgif($content);break;
                case 2:$water_im = imagecreatefromjpeg($content);break;
                case 3:$water_im = imagecreatefrompng($content);break;
                default:die($formatMsg); 
            endswitch; 
        endif; 
        if(!empty($photo) && file_exists($photo)):
            $ground_info = getimagesize($photo); 
            $ground_w = $ground_info[0];
            $ground_h = $ground_info[1];
            switch($ground_info[2]):
                case 1:$ground_im = imagecreatefromgif($photo);$photofrom="imagecreatefromgif";break;
                case 2:$ground_im = imagecreatefromjpeg($photo);$photofrom="imagecreatefromjpeg";break;
                case 3:$ground_im = imagecreatefrompng($photo);$photofrom="imagecreatefrompng";break;
                default:die($formatMsg); 
            endswitch;
        else:
            die("The picture does not exist.");
        endif;
        if($type=="image"):
            $w = $water_w; 
            $h = $water_h;
        else:
            $temp = imagettfbbox(14, 0, "../images/font/simhei.ttf", iconv("utf-8","utf-8",$content));
            $w = $temp[2] - $temp[6]; 
            $h = $temp[3] - $temp[7]; 
            unset($temp);
        endif;
        if (($ground_w < $w) || ($ground_h < $h)):
            echo "The length or width of the picture is smaller than the watermark area."; 
            return; 
        endif; 
        switch($waterpos):
            case 0:$posX = rand(0, ($ground_w - $w)); $posY = rand(0, ($ground_h - $h)); break; 
            case 1:$posX = 0; $posY = 0; break; 
            case 2:$posX = ($ground_w - $w) / 2; $posY = 0; break; 
            case 3:$posX = $ground_w - $w; $posY = 0; break; 
            case 4:$posX = 0; $posY = ($ground_h - $h) / 2; break; 
            case 5:$posX = ($ground_w - $w) / 2; $posY = ($ground_h - $h) / 2; break; 
            case 6:$posX = $ground_w - $w; $posY = ($ground_h - $h) / 2; break; 
            case 7:$posX = 0; $posY = $ground_h - $h; break; 
            case 8:$posX = ($ground_w - $w) / 2; $posY = $ground_h - $h; break; 
            case 9:$posX = $ground_w - $w - 10;$posY = $ground_h - $h - 10;break; 
            default: $posX = rand(0, ($ground_w - $w)); $posY = rand(0, ($ground_h - $h)); break; 
        endswitch;
        if($type=="text"):
            $back=$ground_im;
            $color=imagecolorallocate($back,$fontcolor['r'],$fontcolor['g'],$fontcolor['b']);
            imagettftext($back,$fontsize,0,$posX,$posY,$color,"../images/font/simhei.ttf",iconv("utf-8","utf-8",$content));
            imagejpeg($back,$photo);
            imagedestroy($back);
            elseif($type=="image"):
            $back=$ground_im;
            $water=$water_im;
            $w_w=imagesx($water);
            $w_h=imagesy($water);
            imagecopy($back,$water,$posX,$posY,0,0,$w_w,$w_h);
            imagejpeg($back,$photo);
            imagedestroy($back);
            imagedestroy($water);
        endif;
    }
    public static function Hex2Rgb($hex){
        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3):
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        else:
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        endif;
        return array('r'=>$r,'g'=>$g,'b'=>$b);
    }
}