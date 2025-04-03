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
function korapayInitializeCharge($secretKey, $params)
{
    global $CMSNT;
    $url = "https://api.korapay.com/merchant/api/v1/charges/initialize";
    $headers = ["Authorization: Bearer " . $secretKey, "Content-Type: application/json", "Referer: https://korapay.com"];
    $payload = json_encode($params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $proxyString = $CMSNT->site("korapay_proxy");
    if(!empty($proxyString)) {
        $proxyParts = explode(":", $proxyString);
        if(2 <= count($proxyParts)) {
            list($proxy_ip, $proxy_port) = $proxyParts;
            curl_setopt($ch, CURLOPT_PROXY, $proxy_ip . ":" . $proxy_port);
            if(count($proxyParts) == 4) {
                list($proxy_user, $proxy_pass) = $proxyParts;
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ":" . $proxy_pass);
            }
        }
    }
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) {
        return $error;
    }
    return json_decode($response, true);
}
function korapayVerifyCharge($secretKey, $reference)
{
    global $CMSNT;
    $url = "https://api.korapay.com/merchant/api/v1/charges/" . urlencode($reference);
    $headers = ["Authorization: Bearer " . $secretKey, "Content-Type: application/json"];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    $proxyString = $CMSNT->site("korapay_proxy");
    if(!empty($proxyString)) {
        $proxyParts = explode(":", $proxyString);
        if(2 <= count($proxyParts)) {
            list($proxy_ip, $proxy_port) = $proxyParts;
            curl_setopt($ch, CURLOPT_PROXY, $proxy_ip . ":" . $proxy_port);
            if(count($proxyParts) == 4) {
                list($proxy_user, $proxy_pass) = $proxyParts;
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ":" . $proxy_pass);
            }
        }
    }
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) {
        return $error;
    }
    return json_decode($response, true);
}

?>