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
require_once __DIR__ . "/../libs/database/users.php";
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
if($CMSNT->site("openpix_status") != 1) {
    exit("Cổng thanh toán này chưa được kích hoạt");
}
$headers = getallheaders();
$body = file_get_contents("php://input");
$data = json_decode($body, true);
if(json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit("Invalid JSON data");
}
$secretKeyOnOpenpixPlatform = "";
if(isset($data["event"])) {
    if($data["event"] == "OPENPIX:CHARGE_COMPLETED") {
        $secretKeyOnOpenpixPlatform = $CMSNT->site("openpix_HMAC_key_completed");
    } elseif($data["event"] == "OPENPIX:CHARGE_EXPIRED") {
        $secretKeyOnOpenpixPlatform = $CMSNT->site("openpix_HMAC_key");
    } else {
        http_response_code(400);
        exit("Unsupported event type");
    }
    $algorithm = "sha1";
    $hmac = base64_encode(hash_hmac($algorithm, $body, $secretKeyOnOpenpixPlatform, true));
    $CMSNT->insert("logs", ["user_id" => 0, "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "create_date" => gettime(), "action" => "OpenPix Webhook Signature: " . check_string($headers["x-openpix-signature"]) . " | Generated HMAC: " . check_string($hmac)]);
    if($hmac === $headers["x-openpix-signature"]) {
        if($data["event"] == "OPENPIX:CHARGE_COMPLETED") {
            $transactionID = check_string($data["charge"]["transactionID"]);
            $status = check_string($data["charge"]["status"]);
            $amount = check_string($data["charge"]["value"]);
            $userCorrelationID = check_string($data["charge"]["correlationID"]);
            if($status == "COMPLETED" && ($row = $CMSNT->get_row("SELECT * FROM `payment_openpix` WHERE `trans_id` = '" . $userCorrelationID . "' AND `status` = 0"))) {
                $user = new users();
                $isCong = $user->AddCredits($row["user_id"], $row["price"], __("Recharge OpenPix") . " #" . $userCorrelationID, "TOPUP_openpix_" . $userCorrelationID);
                if($isCong) {
                    $CMSNT->update("payment_openpix", ["status" => 1, "updated_at" => gettime()], " `id` = '" . $row["id"] . "' ");
                    $CMSNT->insert("deposit_log", ["user_id" => $row["user_id"], "method" => "OpenPix", "amount" => $amount, "received" => $row["price"], "create_time" => time(), "is_virtual" => 0]);
                    $my_text = $CMSNT->site("noti_recharge");
                    $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                    $my_text = str_replace("{username}", getRowRealtime("users", $row["user_id"], "username"), $my_text);
                    $my_text = str_replace("{method}", "OpenPix", $my_text);
                    $my_text = str_replace("{amount}", $amount, $my_text);
                    $my_text = str_replace("{price}", format_currency($row["price"]), $my_text);
                    $my_text = str_replace("{time}", gettime(), $my_text);
                    sendMessAdmin($my_text);
                }
            }
        } elseif($data["event"] == "OPENPIX:CHARGE_EXPIRED") {
            $transactionID = check_string($data["charge"]["transactionID"]);
            $status = check_string($data["charge"]["status"]);
            $amount = check_string($data["charge"]["value"]);
            $userCorrelationID = check_string($data["charge"]["correlationID"]);
            if($status == "EXPIRED" && ($row = $CMSNT->get_row("SELECT * FROM `payment_openpix` WHERE `trans_id` = '" . $userCorrelationID . "' AND `status` = 0"))) {
                $CMSNT->update("payment_openpix", ["status" => 2, "updated_at" => gettime()], " `id` = '" . $row["id"] . "' ");
            }
        }
        http_response_code(200);
    } else {
        echo "Invalid HMAC";
        $CMSNT->insert("logs", ["user_id" => 0, "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "create_date" => gettime(), "action" => "HMAC Error:  signature mismatch"]);
        http_response_code(200);
    }
} else {
    http_response_code(400);
    exit("Event field not found");
}

?>