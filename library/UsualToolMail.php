<?php
namespace library\UsualToolMail;
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
 * 实例化SMTP发送邮件
 */
class UTMail{
    public $port;
    public $timeout;
    public $hostname;
    public $logfile;
    public $ssl;
    public $host;
    public $debug;
    public $auth;
    public $user;
    public $pass;
    private $sock;
    function __construct($host,$port=25,$auth=false,$user,$pass){
        $this->ssl = 1;
        $this->debug = FALSE;
        $this->port = $port;
        $this->relayhost = $host;
        $this->timeout = 30;
        $this->auth = $auth;
        $this->user = $user;
        $this->pass = $pass;
        $this->hostname = "localhost";
        $this->logfile = "";
        $this->sock = FALSE;
    }
    function SendMail($to,$from,$subject="",$body="",$mailtype,$cc="",$bcc="",$additional_headers =""){
        $mailfrom = $this->GetAddress($this->StripComment($from));
        $body = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $body);
        $header = "MIME-Version:1.0\r\n";
        if($mailtype=="HTML"){
            $header .= "Content-Type:text/html\r\n";
        }
        $header .= "To: ".$to."\r\n";
        if ($cc != "") {
            $header .= "Cc: ".$cc."\r\n";
        }
        $header .= "From: $from<".$from.">\r\n";
        $header .= "Subject: ".$subject."\r\n";
        $header .= $additional_headers;
        $header .= "Date: ".date("r")."\r\n";
        $header .= "X-Mailer:By Redhat (PHP/".phpversion().")\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$mailfrom.">\r\n";
        $TO = explode(",", $this->StripComment($to));
        if ($cc != "") {
            $TO = array_merge($TO, explode(",", $this->StripComment($cc)));
        }
        if ($bcc != "") {
            $TO = array_merge($TO, explode(",", $this->StripComment($bcc)));
        }
        $sent = TRUE;
        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this->GetAddress($rcpt_to);
            if (!$this->SmtpCockopen($rcpt_to)) {
                $this->LogWrite("Error: Cannot send email to ".$rcpt_to."\n");
                $sent = FALSE;
                continue;
            }
            if ($this->SmtpSend($this->hostname, $mailfrom, $rcpt_to, $header, $body)) {
                $this->LogWrite("E-mail has been sent to <".$rcpt_to.">\n");
            } else {
                $this->LogWrite("Error: Cannot send email to <".$rcpt_to.">\n");
                $sent = FALSE;
            }
            fclose($this->sock);
            $this->LogWrite("Disconnected from remote host\n");
        }
        return $sent;
    }
    function SmtpSend($helo, $from, $to, $header, $body = ""){
        if (!$this->SmtpPutcmd("HELO", $helo)) {
            return $this->SmtpError("sending HELO command");
        }
        if($this->auth){
            if (!$this->SmtpPutcmd("AUTH LOGIN", base64_encode($this->user))) {
                return $this->SmtpError("sending HELO command");
            }
            if (!$this->SmtpPutcmd("", base64_encode($this->pass))) {
                return $this->SmtpError("sending HELO command");
            }
        }
        if (!$this->SmtpPutcmd("MAIL", "FROM:<".$from.">")) {
            return $this->SmtpError("sending MAIL FROM command");
        }
        if (!$this->SmtpPutcmd("RCPT", "TO:<".$to.">")) {
            return $this->SmtpError("sending RCPT TO command");
        }
        if (!$this->SmtpPutcmd("DATA")) {
            return $this->SmtpError("sending DATA command");
        }
        if (!$this->SmtpMessage($header, $body)) {
            return $this->SmtpError("sending message");
        }
        if (!$this->SmtpEom()) {
            return $this->SmtpError("sending <CR><LF>.<CR><LF> [EOM]");
        }
        if (!$this->SmtpPutcmd("QUIT")) {
            return $this->SmtpError("sending QUIT command");
        }
        return TRUE;
    }
    function SmtpCockopen($address){
        if ($this->relayhost == "") {
            return $this->SmtpCockopenMx($address);
        } else {
            return $this->SmtpCockopenRelay();
        }
    }
    function SmtpCockopenRelay(){
        $this->LogWrite("Trying to ".$this->relayhost.":".$this->port."\n");
        if($this->ssl==1){
            $this->sock = @fsockopen("ssl://".$this->relayhost, $this->port, $errno, $errstr, $this->timeout);
        }else{
            $this->sock = @fsockopen($this->relayhost, $this->port, $errno, $errstr, $this->timeout);
        }
        if (!($this->sock && $this->SmtpOk())) {
            $this->LogWrite("Error: Cannot connenct to relay host ".$this->relayhost."\n");
            $this->LogWrite("Error: ".$errstr." (".$errno.")\n");
            return FALSE;
        }
        $this->LogWrite("Connected to relay host ".$this->relayhost."\n");
        return TRUE;
    }
    function SmtpCockopenMx($address){
        $domain = preg_replace("/^.+@([^@]+)$/", "\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->LogWrite("Error: Cannot resolve MX \"".$domain."\"\n");
            return FALSE;
        }
        foreach ($MXHOSTS as $host) {
            $this->LogWrite("Trying to ".$host.":".$this->port."\n");
            $this->sock = @fsockopen($host, $this->port, $errno, $errstr, $this->timeout);
            if (!($this->sock && $this->SmtpOk())) {
                $this->LogWrite("Warning: Cannot connect to mx host ".$host."\n");
                $this->LogWrite("Error: ".$errstr." (".$errno.")\n");
                continue;
            }
            $this->LogWrite("Connected to mx host ".$host."\n");
            return TRUE;
        }
        $this->LogWrite("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n");
        return FALSE;
    }
    function SmtpMessage($header, $body){
        fputs($this->sock, $header."\r\n".$body);
        $this->SmtpDebug("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$body."\n> "));
        return TRUE;
    }
    function SmtpEom(){
        fputs($this->sock, "\r\n.\r\n");
        $this->SmtpDebug(". [EOM]\n");
        return $this->SmtpOk();
    }
    function SmtpOk(){
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        $this->SmtpDebug($response."\n");
        if (!preg_match("/^[23]/", $response)) {
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->LogWrite("Error: Remote host returned \"".$response."\"\n");
            return FALSE;
        }
        return TRUE;
    }
    function SmtpPutcmd($cmd, $arg = ""){
        if ($arg != "") {
            if($cmd=="") $cmd = $arg;
            else $cmd = $cmd." ".$arg;
        }
        fputs($this->sock, $cmd."\r\n");
        $this->SmtpDebug("> ".$cmd."\n");
        return $this->SmtpOk();
    }
    function SmtpError($string){
        $this->LogWrite("Error: Error occurred while ".$string.".\n");
        return FALSE;
    }
    function LogWrite($message){
        $this->SmtpDebug($message);
        if ($this->logfile == "") {
            return TRUE;
        }
        $message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;
        if (!@file_exists($this->logfile) || !($fp = @fopen($this->logfile, "a"))) {
            $this->SmtpDebug("Warning: Cannot open log file \"".$this->logfile."\"\n");
            return FALSE;
        }
        flock($fp, LOCK_EX);
        fputs($fp, $message);
        fclose($fp);
        return TRUE;
    }
    function StripComment($address){
        $comment = "/\([^()]*\)/";
        while (preg_match($comment, $address)) {
            $address = preg_replace($comment, "", $address);
        }
        return $address;
    }
    function GetAddress($address){
        $address = preg_replace("/([ \t\r\n])+/", "", $address);
        $address = preg_replace("/^.*<(.+)>.*$/", "\1", $address);
        return $address;
    }
    function SmtpDebug($message){
        if ($this->debug){
            echo $message;
        }
    }
}