<?php
namespace library\UsualToolSockets;
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
 * 实例化websockets通信
 */
class UTSockets{
    const LISTEN_SOCKET_NUM = 9;
    private $sockets=[];
    private $master;
    public function __construct($host='127.0.0.1',$port='8080') {
        try {
            $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1);
            socket_bind($this->master,$host,$port);
            socket_listen($this->master, self::LISTEN_SOCKET_NUM);
            echo"UT WebSockets Connected.\r\n";
        } catch (\Exception $e) {
            $err_code = socket_last_error();
            $err_msg = socket_strerror($err_code);
            $this->error([
                'error_init_server',
                $err_code,
                $err_msg
            ]);
            echo$err_msg."\r\n";
        }
        $this->sockets[0] = ['resource' => $this->master];
        if(strpos(php_uname(),"Windows")!==false){
        $pid = get_current_user();
        }else{
        $pid = posix_getpid();
        }
        $this->Debug(array("server"=>$this->master,"pid"=>$pid));

        while (true) {
            try {
                $this->DoServer();
            } catch (\Exception $e) {
                $this->Error([
                    'error_do_server',
                    $e->getCode(),
                    $e->getMessage()
                ]);
            }
        }
    }
    private function DoServer() {
        $write = $except = NULL;
        $sockets = array_column($this->sockets, 'resource');
        $read_num = socket_select($sockets, $write, $except, NULL);
        if (false === $read_num) {
            $this->Error([
                'error_select',
                $err_code = socket_last_error(),
                socket_strerror($err_code)
            ]);
            return;
        }
        foreach ($sockets as $socket) {
            if ($socket == $this->master) {
                $client = socket_accept($this->master);
                if (false === $client) {
                    $this->error([
                        'err_accept',
                        $err_code = socket_last_error(),
                        socket_strerror($err_code)
                    ]);
                    continue;
                } else {
                    self::Connect($client);
                    continue;
                }
            } else {
                $bytes = @socket_recv($socket, $buffer, 2048, 0);
                if ($bytes < 9) {
                    $recv_msg = $this->DisConnect($socket);
                } else {
                    if (!$this->sockets[(int)$socket]['handshake']) {
                        self::Handshake($socket, $buffer);
                        continue;
                    } else {
                        $recv_msg = self::parse($buffer);
                    }
                }
                array_unshift($recv_msg, 'receive_msg');
                $msg = self::DealMsg($socket, $recv_msg);

                $this->Broadcast($msg);
            }
        }
    }
    /*
     * 将socket添加到已连接列表,但握手状态留空;
     */
    public function Connect($socket) {
        socket_getpeername($socket, $ip, $port);
        $socket_info = [
            'resource' => $socket,
            'uname' => '',
            'handshake' => false,
            'ip' => $ip,
            'port' => $port,
        ];
        $this->sockets[(int)$socket] = $socket_info;
        $this->Debug(array_merge(['socket_connect'], $socket_info));
    }
    /*
     * 客户端关闭连接
     * @return array
     */
    private function DisConnect($socket) {
        $recv_msg = [
            'type' => 'ut-out',
            'content' => $this->sockets[(int)$socket]['uname'],
        ];
        unset($this->sockets[(int)$socket]);
        return $recv_msg;
    }
    /*
     * 算法握手
     * @return bool
     */
    public function Handshake($socket, $buffer) {
        $line_with_key = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);
        $key = trim(substr($line_with_key, 0, strpos($line_with_key, "\r\n")));
        $upgrade_key = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));
        $upgrade_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $upgrade_message .= "Upgrade: websocket\r\n";
        $upgrade_message .= "Sec-WebSocket-Version: 13\r\n";
        $upgrade_message .= "Connection: Upgrade\r\n";
        $upgrade_message .= "Sec-WebSocket-Accept:" . $upgrade_key . "\r\n\r\n";
        socket_write($socket, $upgrade_message, strlen($upgrade_message));
        $this->sockets[(int)$socket]['handshake'] = true;
        socket_getpeername($socket, $ip, $port);
        $this->Debug([
            'hand_shake',
            $socket,
            $ip,
            $port
        ]);
        $msg = [
            'type' => 'handshake',
            'content' => 'done',
        ];
        $msg = $this->Build(json_encode($msg));
        socket_write($socket, $msg, strlen($msg));
        return true;
    }
    /*
     * 解析数据
     * @return bool|string
     */
    private function Parse($buffer) {
        $decoded = '';
        $len = ord($buffer[1]) & 127;
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return json_decode($decoded, true);
    }
    /*
     * 组装socket数据帧
     * @return string
     */
    private function Build($msg) {
        $frame = [];
        $frame[0] = '81';
        $len = strlen($msg);
        if ($len < 126) {
            $frame[1] = $len < 16 ? '0' . dechex($len) : dechex($len);
        } else if ($len < 65025) {
            $s = dechex($len);
            $frame[1] = '7e' . str_repeat('0', 4 - strlen($s)) . $s;
        } else {
            $s = dechex($len);
            $frame[1] = '7f' . str_repeat('0', 16 - strlen($s)) . $s;
        }
        $data = '';
        $l = strlen($msg);
        for ($i = 0; $i < $l; $i++) {
            $data .= dechex(ord($msg[$i]));
        }
        $frame[2] = $data;
        $data = implode('', $frame);
        return pack("H*", $data);
    }
    /*
     * 拼装信息
     * @param $socket
     * @param $recv_msg
     * @return string
     */
    private function DealMsg($socket, $recv_msg) {
        $msg_type = $recv_msg['type'];
        $msg_content = $recv_msg['content'];
        $response = [];
        switch ($msg_type) {
            //登陆消息
            case 'ut-login':
                $this->sockets[$socket]['uname'] = $msg_content;
                $response['type'] = 'ut-login';
                $response['content'] = $msg_content;
                $response['user_list'] = array_column($this->sockets, 'uname');
                $response['sendtime'] = date('Y-m-d H:i:s',time());
                break;
            //退出消息
            case 'ut-out':
                $response['type'] = 'ut-out';
                $response['content'] = $msg_content;
                $response['user_list'] = array_column($this->sockets, 'uname');
                $response['sendtime'] = date('Y-m-d H:i:s',time());
                break;
            //单对多消息
            case 'ut-room':
                $response['type'] = 'ut-room';
                $response['roomid'] = $recv_msg['roomid'];
                $response['from'] = $this->sockets[$socket]['uname'];
                $response['content'] = $msg_content;
                $response['sendtime'] = date('Y-m-d H:i:s',time());
                if(!empty($recv_msg['item']) && !empty($recv_msg['roomid'])){
                    file_put_contents(APP_ROOT.'/'.$recv_msg['item'].'/room-'.$recv_msg['roomid'].'-'.date("Ymd").'.utlog', json_encode($response) . "\r\n", FILE_APPEND);
                }
                break;
            //单对单消息
            case 'ut-chat':
                $response['type'] = 'ut-chat';
                $response['startuid'] = $recv_msg['startuid'];
                $response['enduid'] = $recv_msg['enduid'];
                $response['from'] = $this->sockets[$socket]['uname'];
                $response['content'] = $msg_content;
                $response['sendtime'] = date('Y-m-d H:i:s',time());
                if(!empty($recv_msg['item'])){
                    if(strpos($recv_msg['startuid'],'u')!==false){
                    file_put_contents(APP_ROOT.'/'.$recv_msg['item'].'/chat-'.$recv_msg['startuid'].'-'.$recv_msg['enduid'].'.utlog', json_encode($response) . "\r\n", FILE_APPEND);
                    }else{
                    file_put_contents(APP_ROOT.'/'.$recv_msg['item'].'/chat-'.$recv_msg['enduid'].'-'.$recv_msg['startuid'].'.utlog', json_encode($response) . "\r\n", FILE_APPEND);
                    }
                }
                break;
        }
        return $this->Build(json_encode($response));
    }
    /*
     * 广播消息
     */
    private function Broadcast($data) {
        foreach ($this->sockets as $socket) {
            if ($socket['resource'] == $this->master) {
                continue;
            }
            socket_write($socket['resource'], $data, strlen($data));
            echo$data."\r\n";
        }
    }
    /*
     * debug信息
     */
    private function Debug(array $info) {
        $time = date('Y-m-d H:i:s');
        array_unshift($info, $time);
        $info = array_map('json_encode', $info);
        file_put_contents(APP_ROOT.'/log/socket.log', implode(' | ', $info) . "\r\n", FILE_APPEND);
    }
    /*
     * 记录错误信息
     */
    private function Error(array $info) {
        $time = date('Y-m-d H:i:s');
        array_unshift($info, $time);
        $info = array_map('json_encode', $info);
        file_put_contents(APP_ROOT.'/log/socket.log', implode(' | ', $info) . "\r\n", FILE_APPEND);
    }
}
