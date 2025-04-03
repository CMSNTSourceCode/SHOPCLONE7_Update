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
$user = new users();
if($CMSNT->site("check_time_cron_thesieure") < time() && time() - $CMSNT->site("check_time_cron_thesieure") < 5) {
    exit("[ÉT O ÉT ] Thao tác quá nhanh, vui lòng đợi");
}
$CMSNT->update("settings", ["value" => time()], " `name` = 'check_time_cron_thesieure' ");
if($CMSNT->site("thesieure_status") == 1) {
    if($CMSNT->site("thesieure_token") == "") {
        exit("Vui lòng cấu hình Token THESIEURE");
    }
    $result = curl_get2("https://api.web2m.com/historyapithesieure/" . trim($CMSNT->site("thesieure_token")));
    $result = json_decode($result, true);
    if(!$result["status"]) {
        exit("Lấy dữ liệu thất bại");
    }
    foreach ($result["tranList"] as $data) {
        $partnerId = check_string($data["username"]);
        $description = check_string($data["description"]);
        $tid = check_string($data["transId"]);
        $amount = str_replace(",", "", check_string($data["amount"]));
        $amount = str_replace("đ", "", $amount);
        $user_id = parse_order_id($description, $CMSNT->site("prefix_autobank"));
        if(($getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '" . $user_id . "' ")) && $CMSNT->num_rows(" SELECT * FROM `payment_thesieure` WHERE `tid` = '" . $tid . "' ") == 0) {
            $received = checkPromotion($amount);
            $insertSv2 = $CMSNT->insert("payment_thesieure", ["tid" => $tid, "method" => "THESIEURE", "user_id" => $getUser["id"], "description" => $description, "amount" => $amount, "received" => $received, "create_gettime" => gettime(), "create_time" => time()]);
            if($insertSv2) {
                $isCong = $user->AddCredits($getUser["id"], $received, "Nạp tiền tự động qua ví THESIEURE (#" . $tid . " - " . $description . " - " . $amount . ")", "TOPUP_THESIEURE" . $tid);
                if($isCong) {
                    if($CMSNT->site("affiliate_status") == 1 && $getUser["ref_id"] != 0) {
                        $ck = $CMSNT->site("affiliate_ck");
                        if(getRowRealtime("users", $getUser["ref_id"], "ref_ck") != 0) {
                            $ck = getRowRealtime("users", $getUser["ref_id"], "ref_ck");
                        }
                        $price = $received * $ck / 100;
                        $user->AddCommission($getUser["ref_id"], $getUser["id"], $price, __("Hoa hồng thành viên " . $getUser["username"]));
                    }
                    debit_processing($getUser["id"]);
                    $CMSNT->insert("deposit_log", ["user_id" => $getUser["id"], "method" => "THESIEURE", "amount" => $amount, "received" => $received, "create_time" => time(), "is_virtual" => 0]);
                    $my_text = $CMSNT->site("noti_recharge");
                    $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                    $my_text = str_replace("{username}", $getUser["username"], $my_text);
                    $my_text = str_replace("{method}", "THESIEURE", $my_text);
                    $my_text = str_replace("{amount}", format_currency($amount), $my_text);
                    $my_text = str_replace("{price}", format_currency($received), $my_text);
                    $my_text = str_replace("{time}", gettime(), $my_text);
                    sendMessAdmin($my_text);
                    echo "[<b style=\"color:green\">-</b>] Xử lý thành công 1 hoá đơn.<br>";
                }
            }
        }
    }
}

?>