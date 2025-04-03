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
function createPaymentBakong($params)
{
    global $CMSNT;
    if(!isset($params["transaction_id"]) || !isset($params["amount"]) || !isset($params["success_url"])) {
        return json_encode(["status" => "error", "message" => "Thiếu tham số bắt buộc"]);
    }
    $my_payment_url = "https://raksmeypay.com/payment/request/" . $CMSNT->site("bakong_profile_id");
    $profile_key = $CMSNT->site("bakong_profile_key");
    $transaction_id = (int) $params["transaction_id"];
    $amount = (double) $params["amount"];
    $success_url = urlencode($params["success_url"]);
    $remark = isset($params["remark"]) ? $params["remark"] : "";
    $hash = sha1($profile_key . $transaction_id . $amount . $success_url . $remark);
    $parameters = ["transaction_id" => $transaction_id, "amount" => $amount, "success_url" => $success_url, "remark" => $remark, "hash" => $hash];
    $queryString = http_build_query($parameters);
    $payment_link_url = $my_payment_url . "?" . $queryString;
    return $payment_link_url;
}
function verifyPaymentBakong($transaction_id, $amount)
{
    global $CMSNT;
    $payment_verify_url = "https://raksmeypay.com/api/payment/verify/" . $CMSNT->site("bakong_profile_id");
    $profile_key = $CMSNT->site("bakong_profile_key");
    $hash = sha1($profile_key . $transaction_id);
    $data = ["transaction_id" => (int) $transaction_id, "hash" => $hash];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $payment_verify_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    if(!empty($response["payment_status"]) && strtoupper($response["payment_status"]) == "SUCCESS" && $response["payment_amount"] == $amount) {
        return ["status" => true, "message" => "Thanh toán thành công", "data" => $response];
    }
    if(!empty($response["payment_status"]) && strtoupper($response["payment_status"]) == "PENDING") {
        return ["status" => false, "message" => "Thanh toán đang chờ xử lý", "data" => $response];
    }
    if(!empty($response["err_msg"])) {
        return ["status" => false, "message" => $response["err_msg"], "data" => $response];
    }
    return ["status" => false, "message" => "Không thể xác thực giao dịch", "data" => $response];
}

?>