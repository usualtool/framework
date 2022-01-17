<?php
namespace library\UsualToolWechat;
use library\UsualToolData\UTData;
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
class UTWechat{
    var $appline;
    var $appid;
    var $appsecret;
    var $token;
    function __construct($appline,$appid,$appsecret,$token=''){
        $this->appline=$appline;
        $this->appid=$appid;
        $this->appsecret=$appsecret;
        $this->token=$token;
    }
    function GetToken(){
        $file = file_get_contents(PUB_PATH."/token/wechat.token.json",true);
        $result = json_decode($file,true);
        if(time() > $result['expires']):
            $data = array();
            $data['access_token'] = $this->GetNewToken();
            $data['expires']=time()+7000;
            $jsonStr =  json_encode($data);
            $fp = fopen(PUB_PATH."/token/wechat.token.json","w");
            fwrite($fp, $jsonStr);
            fclose($fp);
            return $data['access_token'];
        else:
            return $result['access_token'];
        endif;
    }
    function GetNewToken(){
        $url = "https://{$this->appline}/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";
        $access_token_Arr =  $this->GetData($url);
        return $access_token_Arr['access_token'];
    }
    function GetData($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output,true);
    }
    function PostData($url,$data=''){         
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        if(!empty($data)){  
            curl_setopt($ch, CURLOPT_POST, TRUE);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
        }  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $output = curl_exec($ch);  
        curl_close($ch);  
        if(is_null(json_decode($output))){
            return "data:image/png;base64,".base64_encode($output);
        }else{
            return json_decode($output,true);
        }        
    }
    function GetUserInfo(){
        $url = "https://{$this->appline}/cgi-bin/user/get?access_token={$this->GetToken()}";
        $res = $this->GetData($url);
        $userInfoList = $res['data']['openid'];
        return $userInfoList;
    }
    function SendMsgToAll($pushtitle,$pushcontent,$pushlink,$pushpic){
        $userInfoList = $this->GetUserInfo();
        $url = "https://{$this->appline}/cgi-bin/message/custom/send?access_token={$this->GetToken()}";
        foreach($userInfoList as $val){
            $data = '{
            "touser":"'.$val.'",
            "msgtype":"link",
            "link":{
            "title":"'.$pushtitle.'",
            "description":"'.$pushcontent.'",
            "url":"'.$pushlink.'",
            "thumb_url": "'.$pushpic.'"
            }';
            $this->PostData($url,$data);
        }
    }
    function DelMenu(){
        $url = "https://{$this->appline}/cgi-bin/menu/delete?access_token={$this->GetToken()}";
        $res = $this->GetData($url);
        $rerult = $res['errmsg'];
        return $rerult;
    }
    function CreatMenu($data){
        $url = "https://{$this->appline}/cgi-bin/menu/create?access_token={$this->GetToken()}";
        $res = $this->PostData($url,$data);
        $rerult=$res['errmsg'];
        return $rerult;
    }
    public function Valid(){
        $echoStr = $_GET["echostr"];
        if($this->CheckSignature()){
            echo $echoStr;
        exit;
        }
    }
    private function CheckSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    public function ResponseMsg(){
        $postStr = file_get_contents("php://input");
        if(!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            $msgname=$postObj->FromUserName;
            $msgtype=$RX_TYPE;
            if($msgtype=="event"):
                if($postObj->Event=="subscribe"):
                $msgcontent="已关注公众号。";
                endif;
            elseif($msgtype=="text"):
                $msgcontent=$postObj->Content;
            elseif($msgtype=="image"):
                $msgcontent=$postObj->PicUrl;
            elseif($msgtype=="voice"):
                $msgcontent=$postObj->MediaId;
                $this->GetLocal("voice",$msgcontent);
            elseif($msgtype=="video"):
                $msgcontent=$postObj->MediaId;
                $this->GetLocal("video",$msgcontent);
            elseif($msgtype=="location"):
                $msgcontent="纬度：".$postObj->Location_X."，经度：".$postObj->Location_Y."，缩放级别：".$postObj->Scale."，位置：".$postObj->Label."";
            elseif($msgtype=="link"):
                $msgcontent="<a href='".$postObj->Url."'><b>".$postObj->Title."</b><br>".$postObj->Description."</a>";
            endif;
            $msgtime=$postObj->CreateTime;
            //数据操作示例
            if(!empty($msgcontent) && UTData::ModTable("cms_wechat_message")):
                //用户OPENID$msgname
                //接收到的数据$msgcontent
                    //根据OPENID解析用户数据
                    $userinfodata=$this->GetUser($msgname);
                    $userinfo=explode("|usualtool|",$userinfodata);
                    $unionid=$userinfo[0];
                    $nickname=$userinfo[1];
                    $sex=$userinfo[2];
                    $headpic=$userinfo[3];
                    $data=UTData::QueryData("cms_wechat_message","","msgname ='$msgname'","","");
                    if($data["querynum"]==1):
                        $qrow=$data["querydata"][0];
                        $oldcontent=$qrow['msgcontent'];
                        $oldmsgtype=$qrow['msgtype'];
                        $newcontent=$oldcontent."|usualtool|".$msgcontent;
                        $newmsgtype=$oldmsgtype."|usualtool|".$msgtype;
                        UTData::UpdateData("cms_wechat_message",array(
                            "msgtype"=>$newmsgtype,
                            "msgcontent"=>$newcontent,
                            "msgtime"=>$msgtime),"msgname='$msgname'");
                    else:
                        UTData::InsertData("cms_wechat_message",array(
                            "msgname"=>$msgname,
                            "unionid"=>$unionid,
                            "nickname"=>$nickname,
                            "sex"=>$sex,
                            "headpic"=>$headpic,
                            "msgtype"=>$msgtype,
                            "msgcontent"=>$msgcontent,
                            "msgtime"=>$msgtime));
                    endif;
            endif;
            switch ($RX_TYPE){
                case "event":
                $result = $this->ReceiveEvent($postObj);
                break;
                case "text":
                echo "success";
                exit;
                break;
                case "image":
                echo "success";
                exit;
                break;
                case "voice":
                echo "success";
                exit;
                break;
                case "video":
                echo "success";
                exit;
                break;
                case "location":
                echo "success";
                exit;
                break;
                case "link":
                echo "success";
                exit;
                break;
                default:
                echo "success";
                exit;
                break;
            }
            echo $result;
            exit;
        }else{
            echo "";
            exit;
        }
    }
    private function ReceiveEvent($object){
        if($object->Event=="subscribe"):
            $content = "感谢您的关注。";
            $result = $this->TransmitText($object, $content);
            return $result;
        else:
            echo "success";
            exit;
        endif;
    }
    private function ReceiveText($object){
        $content = "success";
        $result = $this->TransmitText($object, $content);
        return $result;
    }
    private function ReceiveImage($object){
        $content = "success";
        $result = $this->TransmitText($object, $content);
        return $result;
    }
    private function ReceiveVoice($object){
        $content = "success";
        $result = $this->TransmitText($object, $content);
        return $result;
    }
    private function ReceiveVideo($object){
        $content = "success";
        $result = $this->TransmitText($object, $content);
        return $result;
    }
    private function ReceiveLocation($object){
        $content = "success";
        $result = $this->TransmitText($object, $content);
        return $result;
    }
    private function ReceiveLink($object){
        $content = "success";
        $result = $this->TransmitText($object, $content);
        return $result;
    }
    private function TransmitText($object, $content){
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    function SendMsgToOne($touser,$content){
        $url = "https://{$this->appline}/cgi-bin/message/custom/send?access_token={$this->GetToken()}";
        $data = '{
        "touser":"'.$touser.'",
        "msgtype":"text",
        "text":{
        "content":"'.$content.'"
        }
        }';
        $this->PostData($url,$data);
    }
    function GetUser($openid){
        $url = "https://{$this->appline}/cgi-bin/user/info?access_token={$this->GetToken()}&openid={$openid}&lang=zh_CN";
        $res = $this->GetData($url);
        $userinfo = "usualtool|usualtool|".$res['nickname']."|usualtool|".$res['sex']."|usualtool|".$res['headimgurl']."";
        return $userinfo;
    }
    function UserBlack(){
        $url = "https://{$this->appline}/cgi-bin/tags/members/getblacklist?access_token={$this->GetToken()}";
        $data='{"begin_openid":""}';
        $res = $this->PostData($url,$data);
        $resx=json_encode($res);
        if(strpos($resx,'errmsg')!==false){
            return $res["errmsg"];
        }else{
            return $res['data']['openid'];
        }
    }
    function UserSetBlack($openid){
        $url = "https://{$this->appline}/cgi-bin/tags/members/batchblacklist?access_token={$this->GetToken()}";
        $data='{
        "openid_list":["'.$openid.'"]
        }';
        $res = $this->PostData($url,$data);
        return $res["errmsg"];
    }
    function UserUnBlack($openid){
        $url = "https://{$this->appline}/cgi-bin/tags/members/batchunblacklist?access_token={$this->GetToken()}";
        $data='{
        "openid_list":["'.$openid.'"]
        }';
        $res = $this->PostData($url,$data);
        return $res["errmsg"];
    }
    function GetVoice($voiceid){
        $url = "https://{$this->appline}/cgi-bin/media/voice/queryrecoresultfortext?access_token={$this->GetToken()}&voice_id={$voiceid}&lang=zh_CN";
        $res = $this->PostData($url);
        $result =$res['result'];
        return $result;
    }
    function GetLocal($type,$mediaid){
        $mediaurl = "https://{$this->appline}/cgi-bin/media/get?access_token={$this->GetToken()}&media_id={$mediaid}";
        $res = file_get_contents($mediaurl);
        if($type=="voice"){
        $respath = APP_ROOT ."/assets/upload/other/".$mediaid.".amr";
        }elseif($type=="video"){
        $respath = APP_ROOT ."/assets/upload/other/".$mediaid.".mp4";
        }
        file_put_contents($respath,$res);
    }
    function GetMsg($stime,$etime,$msgid,$limit){
        $url = "https://{$this->appline}/customservice/msgrecord/getmsglist?access_token={$this->GetToken()}";
        $data = '{
        "starttime":{$stime},
        "endtime":{$etime},
        "msgid":{$msgid},
        "number":{$limit} 
        }';
        $res = $this->PostData($url,$data);
        return $res;
    }
    function GetNewQrcode($arr){
        $url = "https://{$this->appline}/cgi-bin/wxaapp/createwxaqrcode?access_token={$this->GetNewToken()}";
        $data = json_encode($arr);
        $res = $this->PostData($url,$data);
        return $res;
    }
    function DecryptData($sessionKey,$encryptedData,$iv,&$data){
		$aesKey=base64_decode($sessionKey);
		$aesIV=base64_decode($iv);
		$aesCipher=base64_decode($encryptedData);
		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
		$dataObj=json_decode($result);
		if($dataObj==NULL){
			return "-41003";
		}
		if($dataObj->watermark->appid != $this->appid){
			return "-41003";
		}
		$data = $result;
		return 0;
	}
}