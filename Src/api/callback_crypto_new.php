<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/../libs/db.php";
require_once __DIR__ . "/../libs/lang.php";
require_once __DIR__ . "/../libs/helper.php";
require_once __DIR__ . "/../libs/database/users.php";
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
$request_data = $_GET;
if(!(isset($request_data["request_id"]) && isset($request_data["merchant_id"]) && isset($request_data["api_key"]) && isset($request_data["received"]) && isset($request_data["status"]))) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Thiếu tham số callback."]);
    exit;
}
$request_id = sanitize_input($request_data["request_id"]);
$merchant_id = sanitize_input($request_data["merchant_id"]);
$api_key = sanitize_input($request_data["api_key"]);
$received = sanitize_input($request_data["received"]);
$status = sanitize_input($request_data["status"]);
$from_address = isset($request_data["from_address"]) ? sanitize_input($request_data["from_address"]) : NULL;
$transaction_id = isset($request_data["transaction_id"]) ? sanitize_input($request_data["transaction_id"]) : NULL;
$expected_merchant_id = $CMSNT->site("crypto_merchant_id");
$expected_api_key = $CMSNT->site("crypto_api_key");
if($merchant_id !== $expected_merchant_id || $api_key !== $expected_api_key) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Merchant ID hoặc API Key không hợp lệ."]);
    exit;
}
if(!($row = $CMSNT->get_row(" SELECT * FROM `payment_crypto` WHERE `request_id` = '" . $request_id . "' "))) {
    echo json_encode(["status" => "error", "message" => "Hóa đơn không tồn tại"]);
    exit;
}
$amount = $row["received"];
$received = checkPromotion($amount);
$getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '" . $row["user_id"] . "' ");
if($row["status"] == "completed") {
    echo json_encode(["status" => "error", "message" => "Hoá đơn này đã được xử lý rồi"]);
    exit;
}
switch ($status) {
    case "waiting":
    case "expired":
        $CMSNT->update("payment_crypto", ["status" => "expired", "update_gettime" => gettime()], " `id` = '" . $row["id"] . "' ");
        break;
    case "completed":
        $isUpdate = $CMSNT->update("payment_crypto", ["status" => "completed", "update_gettime" => gettime()], " `id` = '" . $row["id"] . "' ");
        if($isUpdate) {
            $User = new users();
            $isCong = $User->AddCredits($row["user_id"], $received, "Crypto Recharge #" . $row["trans_id"], "TOPUP_CRYPTO_" . $row["trans_id"]);
            if($isCong) {
                $my_text = $CMSNT->site("noti_recharge");
                $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                $my_text = str_replace("{username}", $getUser["username"], $my_text);
                $my_text = str_replace("{method}", "Crypto", $my_text);
                $my_text = str_replace("{amount}", format_currency($amount), $my_text);
                $my_text = str_replace("{price}", format_currency($received), $my_text);
                $my_text = str_replace("{time}", gettime(), $my_text);
                sendMessAdmin($my_text);
                $CMSNT->insert("deposit_log", ["user_id" => $getUser["id"], "method" => "USDT", "amount" => $amount, "received" => $received, "create_time" => time(), "is_virtual" => 0]);
            }
        }
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Callback đã được xử lý thành công."]);
        break;
    default:
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Trạng thái giao dịch không hợp lệ."]);
        exit;
}
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");
    return $data;
}

?>