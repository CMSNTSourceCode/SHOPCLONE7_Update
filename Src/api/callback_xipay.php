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
require_once __DIR__ . "/../libs/xipay.php";
require_once __DIR__ . "/../libs/database/users.php";
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
if($CMSNT->site("gateway_xipay_status") != 1) {
    exit("Cổng thanh toán này chưa được kích hoạt");
}
if($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("HTTP/1.1 405 Method Not Allowed");
    exit("Method Not Allowed");
}
$out_trade_no = isset($_GET["out_trade_no"]) ? check_string($_GET["out_trade_no"]) : "";
$trade_no = isset($_GET["trade_no"]) ? check_string($_GET["trade_no"]) : "";
$trade_status = isset($_GET["trade_status"]) ? check_string($_GET["trade_status"]) : "";
$type = isset($_GET["type"]) ? check_string($_GET["type"]) : "";
$money = isset($_GET["money"]) ? check_string($_GET["money"]) : "";
$display_type = $type == "wxpay" ? "WeChat Pay" : "Alipay";
if(empty($out_trade_no) || empty($trade_no) || empty($trade_status) || empty($money)) {
    exit("fail");
}
$epay_config["apiurl"] = "https://pay.xipay.cc/";
$epay_config["pid"] = $CMSNT->site("gateway_xipay_pid");
$epay_config["key"] = $CMSNT->site("gateway_xipay_md5key");
$epay = new EpayCore($epay_config);
$verify_result = $epay->verifyNotify();
if($verify_result) {
    if($trade_status == "TRADE_SUCCESS") {
        if($row = $CMSNT->get_row(" SELECT * FROM `payment_xipay` WHERE `out_trade_no` = '" . $out_trade_no . "' AND `transaction_id` IS NULL AND `status` =  0 ")) {
            $user = new users();
            $isCong = $user->AddCredits($row["user_id"], $row["price"], __("Recharge") . " " . $display_type . " #" . $out_trade_no, "TOPUP_xipay_" . $out_trade_no);
            if($isCong) {
                $CMSNT->update("payment_xipay", ["status" => 1, "type" => $type, "transaction_id" => $trade_no], " `id` = '" . $row["id"] . "'  ");
                $CMSNT->insert("deposit_log", ["user_id" => $row["user_id"], "method" => $display_type, "amount" => $money, "received" => $row["price"], "create_time" => time(), "is_virtual" => 0]);
                $my_text = $CMSNT->site("noti_recharge");
                $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                $my_text = str_replace("{username}", getRowRealtime("users", $row["user_id"], "username"), $my_text);
                $my_text = str_replace("{method}", $display_type, $my_text);
                $my_text = str_replace("{amount}", $money, $my_text);
                $my_text = str_replace("{price}", format_currency($row["price"]), $my_text);
                $my_text = str_replace("{time}", gettime(), $my_text);
                sendMessAdmin($my_text);
            }
        }
    } else {
        $CMSNT->update("payment_xipay", ["status" => 2], " `id` = '" . $row["id"] . "' AND `status` = 0 ");
    }
    echo "success";
} else {
    echo "fail";
}

?>