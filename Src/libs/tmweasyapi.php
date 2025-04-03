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
function callMaemaneeApi(array $params)
{
    $apiUrl = "https://tmwallet.thaighost.net/api_mn.php";
    $queryString = http_build_query($params);
    $fullUrl = $apiUrl . "?" . $queryString;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) {
        return false;
    }
    $jsonData = json_decode($response, true);
    if(json_last_error() === JSON_ERROR_NONE) {
        return $jsonData;
    }
    return false;
}

?>