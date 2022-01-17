<?php
use library\UsualToolInc\UTInc;
if(UTInc::SqlCheck($_GET['do'])=="del"){
    $img=UTInc::SqlCheck(str_replace("..","",$_GET['img']));
    if(in_array(substr($img,-4),array(".jpg",".png",".gif"))):
        $img=str_replace($config["APPURL"]."/app",APP_ROOT,$img);
        UTInc::UnlinkFile($img);
        echo json_encode(array("error"=>0));
    else:
        echo json_encode(array("error"=>1));
    endif;
}else{
    if(!empty($_POST['l'])){
        $l=str_replace("..","",$_POST['l']);
    }else{
        $l="upload";
    }
    $path = APP_ROOT."/assets/".$l."/";
    $file = $_FILES['file'];
    $name = $file['name'];
    $type = strtolower(substr($name,strrpos($name,'.')+1));
    $fname=date('Ymd').str_pad(mt_rand(1, 99999),5,'0',STR_PAD_LEFT).".".$type;
    $picurl = $path . $fname;
    $allow_type = array('jpg','jpeg','gif','png','zip','rar','mp4','mp3','m3u8','lrc','ico','doc','docx','xls','xlsx');
    if(!in_array($type, $allow_type)){
        echo json_encode(array("error"=>"The file format is incorrect!"));
    }elseif(!is_uploaded_file($file['tmp_name'])){
        echo json_encode(array("error"=>"Illegal source of documents!<br>|-|Illegal source of documents!"));
    }else{
        if(move_uploaded_file($file['tmp_name'],$picurl)){
            echo json_encode(array("error"=>"0","pic"=>str_replace(APP_ROOT,$config["APPURL"]."/app",$picurl),"name"=>$fname,"post"=>$config["APPURL"]));
        }else{
            echo json_encode(array("error"=>"File upload failed!"));
        }
    }
}