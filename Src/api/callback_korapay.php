<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/../libs/db.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../libs/lang.php";
require_once __DIR__ . "/../libs/helper.php";
require_once __DIR__ . "/../libs/korapay.php";
require_once __DIR__ . "/../libs/database/users.php";
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
if($CMSNT->site("korapay_status") != 1) {
    exit("Cổng thanh toán này chưa được kích hoạt");
}
header("Content-Type: application/json");
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if(empty($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid payload"]);
    exit;
}
if(!isset($data["event"]) || !isset($data["data"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}
$event = $data["event"];
$payloadData = $data["data"];
if($event === "charge.success") {
    $reference = isset($payloadData["reference"]) ? $payloadData["reference"] : NULL;
    $currency = isset($payloadData["currency"]) ? $payloadData["currency"] : NULL;
    $amount = isset($payloadData["amount"]) ? $payloadData["amount"] : NULL;
    $fee = isset($payloadData["fee"]) ? $payloadData["fee"] : NULL;
    $status = isset($payloadData["status"]) ? $payloadData["status"] : NULL;
    $paymentMethod = isset($payloadData["payment_method"]) ? $payloadData["payment_method"] : NULL;
    $paymentReference = isset($payloadData["payment_reference"]) ? $payloadData["payment_reference"] : NULL;
    $secretKey = $CMSNT->site("korapay_secretKey");
    $verification = korapayVerifyCharge($secretKey, $reference);
    if(!$verification || !isset($verification["status"]) || $verification["status"] !== true) {
        http_response_code(400);
        echo json_encode(["error" => "Transaction verification failed"]);
        exit;
    }
    if($verification["data"]["status"] == "success" && ($row = $CMSNT->get_row(" SELECT * FROM `payment_korapay` WHERE `trans_id` = '" . $reference . "' AND `status` =  0 "))) {
        $user = new users();
        $isCong = $user->AddCredits($row["user_id"], $row["price"], __("Recharge Korapay") . " #" . $reference, "TOPUP_korapay_" . $reference);
        if($isCong) {
            $CMSNT->update("payment_korapay", ["status" => 1, "updated_at" => gettime()], " `id` = '" . $row["id"] . "'  ");
            $CMSNT->insert("deposit_log", ["user_id" => $row["user_id"], "method" => __("Korapay Africa"), "amount" => $amount, "received" => $row["price"], "create_time" => time(), "is_virtual" => 0]);
            $my_text = $CMSNT->site("noti_recharge");
            $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
            $my_text = str_replace("{username}", getRowRealtime("users", $row["user_id"], "username"), $my_text);
            $my_text = str_replace("{method}", __("Recharge Korapay"), $my_text);
            $my_text = str_replace("{amount}", $amount, $my_text);
            $my_text = str_replace("{price}", format_currency($row["price"]), $my_text);
            $my_text = str_replace("{time}", gettime(), $my_text);
            sendMessAdmin($my_text);
        }
    }
    if(($verification["data"]["status"] == "failed" || $verification["data"]["status"] == "expired") && ($row = $CMSNT->get_row(" SELECT * FROM `payment_korapay` WHERE `trans_id` = '" . $reference . "' AND `status` =  0 "))) {
        $CMSNT->update("payment_korapay", ["status" => 2, "updated_at" => gettime()], " `id` = '" . $row["id"] . "'  ");
    }
    http_response_code(200);
    echo json_encode(["received" => true]);
    exit;
}
http_response_code(200);
echo json_encode(["received" => true, "message" => "Event not processed"]);
exit;

?>