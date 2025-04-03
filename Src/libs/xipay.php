<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
if(!defined("IN_SITE")) {
    exit("The Request Not Found");
}
class EpayCore
{
    private $pid;
    private $key;
    private $submit_url;
    private $mapi_url;
    private $api_url;
    private $sign_type = "MD5";
    public function __construct($config)
    {
        $this->pid = $config["pid"];
        $this->key = $config["key"];
        $this->submit_url = $config["apiurl"] . "submit.php";
        $this->mapi_url = $config["apiurl"] . "mapi.php";
        $this->api_url = $config["apiurl"] . "api.php";
    }
    public function pagePay($param_tmp, $button = "正在跳转")
    {
        $param = $this->buildRequestParam($param_tmp);
        $html = "<form id=\"dopay\" action=\"" . $this->submit_url . "\" method=\"post\">";
        foreach ($param as $k => $v) {
            $html .= "<input type=\"hidden\" name=\"" . $k . "\" value=\"" . $v . "\"/>";
        }
        $html .= "<input type=\"submit\" value=\"" . $button . "\"></form><script>document.getElementById(\"dopay\").submit();</script>";
        return $html;
    }
    public function getPayLink($param_tmp)
    {
        $param = $this->buildRequestParam($param_tmp);
        $url = $this->submit_url . "?" . http_build_query($param);
        return $url;
    }
    public function apiPay($param_tmp)
    {
        $param = $this->buildRequestParam($param_tmp);
        $response = $this->getHttpResponse($this->mapi_url, http_build_query($param));
        $arr = json_decode($response, true);
        return $arr;
    }
    public function verifyNotify()
    {
        if(empty($_GET)) {
            return false;
        }
        $sign = $this->getSign($_GET);
        if($sign === $_GET["sign"]) {
            $signResult = true;
        } else {
            $signResult = false;
        }
        return $signResult;
    }
    public function verifyReturn()
    {
        if(empty($_GET)) {
            return false;
        }
        $sign = $this->getSign($_GET);
        if($sign === $_GET["sign"]) {
            $signResult = true;
        } else {
            $signResult = false;
        }
        return $signResult;
    }
    public function orderStatus($trade_no)
    {
        $result = $this->queryOrder($trade_no);
        if($result["status"] == 1) {
            return true;
        }
        return false;
    }
    public function queryOrder($trade_no)
    {
        $url = $this->api_url . "?act=order&pid=" . $this->pid . "&key=" . $this->key . "&trade_no=" . $trade_no;
        $response = $this->getHttpResponse($url);
        $arr = json_decode($response, true);
        return $arr;
    }
    public function refund($trade_no, $money)
    {
        $url = $this->api_url . "?act=refund";
        $post = "pid=" . $this->pid . "&key=" . $this->key . "&trade_no=" . $trade_no . "&money=" . $money;
        $response = $this->getHttpResponse($url, $post);
        $arr = json_decode($response, true);
        return $arr;
    }
    private function buildRequestParam($param)
    {
        $mysign = $this->getSign($param);
        $param["sign"] = $mysign;
        $param["sign_type"] = $this->sign_type;
        return $param;
    }
    private function getSign($param)
    {
        ksort($param);
        reset($param);
        $signstr = "";
        foreach ($param as $k => $v) {
            if($k != "sign" && $k != "sign_type" && $v != "") {
                $signstr .= $k . "=" . $v . "&";
            }
        }
        $signstr = substr($signstr, 0, -1);
        $signstr .= $this->key;
        $sign = md5($signstr);
        return $sign;
    }
    private function getHttpResponse($url, $post = false, $timeout = 10)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

?>