<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../libs/db.php";
require_once __DIR__ . "/../../libs/lang.php";
require_once __DIR__ . "/../../libs/helper.php";
if($CMSNT->site("status_demo") != 0) {
    $data = json_encode(["status" => "error", "msg" => __("This function cannot be used because this is a demo site")]);
    exit($data);
}
if(!isset($_POST["action"])) {
    $data = json_encode(["status" => "error", "msg" => __("The Request Not Found")]);
    exit($data);
}
if($_POST["action"] == "download_order") {
    if(empty($_POST["token"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
    }
    if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
    }
    if(empty($_POST["trans_id"])) {
        exit(json_encode(["status" => "error", "msg" => __("Đơn hàng không hợp lệ")]));
    }
    $trans_id = check_string($_POST["trans_id"]);
    if(!($order = $CMSNT->get_row("SELECT * FROM `product_order` WHERE `trans_id` = '" . $trans_id . "' AND `buyer` = " . $getUser["id"] . " AND `trash` = 0 "))) {
        exit(json_encode(["status" => "error", "msg" => __("Đơn hàng không tồn tại trong hệ thống")]));
    }
    if(($order["status_view_order"] == 1 || $CMSNT->site("isPurchaseIpVerified") == 1) && $order["ip"] != myip()) {
        exit(json_encode(["status" => "error", "msg" => __("Địa chỉ IP của bạn không khớp với địa chỉ IP bạn dùng để mua hàng")]));
    }
    if($order["status_view_order"] == 1 || $CMSNT->site("isPurchaseDeviceVerified") == 1) {
        $Mobile_Detect = new Mobile_Detect();
        if($order["device"] != $Mobile_Detect->getUserAgent()) {
            exit(json_encode(["status" => "error", "msg" => __("Trình duyệt của bạn không khớp với trình duyệt lúc bạn mua hàng")]));
        }
    }
    $accounts = getRowRealtime("products", $order["product_id"], "text_txt") . PHP_EOL;
    foreach ($CMSNT->get_list(" SELECT * FROM `product_sold` WHERE `trans_id` = '" . $trans_id . "' AND `buyer` = " . $getUser["id"] . " ORDER BY id DESC ") as $account) {
        $accounts .= htmlspecialchars_decode($account["account"]) . PHP_EOL;
    }
    $file = $trans_id . ".txt";
    $data = json_encode(["status" => "success", "filename" => $file, "accounts" => $accounts, "msg" => __("Đang tải xuống đơn hàng...")]);
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Download order") . " (" . $order["trans_id"] . ")"]);
    exit($data);
} else {
    if($_POST["action"] == "loadStatusInvoice") {
        if(empty($_POST["trans_id"])) {
            exit(json_encode(["status" => "error", "msg" => __("Trans ID does not exist in the system")]));
        }
        if(!($row = $CMSNT->get_row("SELECT * FROM `invoices` WHERE `trans_id` = '" . check_string($_POST["trans_id"]) . "' "))) {
            exit(json_encode(["status" => "error", "msg" => __("Trans ID does not exist in the system")]));
        }
        exit(json_encode(["data" => ["status" => $row["status"]], "status" => "success", "msg" => ""]));
    }
    if($_POST["action"] == "notication_topup") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($row = $CMSNT->get_row(" SELECT * FROM `payment_bank` WHERE `notication` = 0 AND `user_id` = '" . $getUser["id"] . "' "))) {
            exit(json_encode(["status" => "error", "msg" => __("Không có lịch sử nạp tiền gần đây")]));
        }
        $CMSNT->update("payment_bank", ["notication" => 1], " `id` = '" . $row["id"] . "' ");
        exit(json_encode(["status" => "success", "msg" => __("Nạp tiền thành công") . " " . format_currency($row["received"])]));
    }
    if($_POST["action"] == "notication_topup_momo") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($row = $CMSNT->get_row(" SELECT * FROM `payment_momo` WHERE `notication` = 0 AND `user_id` = '" . $getUser["id"] . "' "))) {
            exit(json_encode(["status" => "error", "msg" => __("Không có lịch sử nạp tiền gần đây")]));
        }
        $CMSNT->update("payment_momo", ["notication" => 1], " `id` = '" . $row["id"] . "' ");
        exit(json_encode(["status" => "success", "msg" => __("Nạp tiền thành công") . " " . format_currency($row["received"])]));
    }
    if($_POST["action"] == "notication_topup_xipay") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($row = $CMSNT->get_row(" SELECT * FROM `payment_xipay` WHERE `notication` = 0 AND `user_id` = '" . $getUser["id"] . "' AND `status` = 1 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Không có lịch sử nạp tiền gần đây")]));
        }
        $CMSNT->update("payment_xipay", ["notication" => 1], " `id` = '" . $row["id"] . "' ");
        exit(json_encode(["status" => "success", "msg" => __("Deposit successful") . " " . format_currency($row["price"])]));
    }
    if($_POST["action"] == "notication_topup_thesieure") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($row = $CMSNT->get_row(" SELECT * FROM `payment_thesieure` WHERE `notication` = 0 AND `user_id` = '" . $getUser["id"] . "' "))) {
            exit(json_encode(["status" => "error", "msg" => __("Không có lịch sử nạp tiền gần đây")]));
        }
        $CMSNT->update("payment_thesieure", ["notication" => 1], " `id` = '" . $row["id"] . "' ");
        exit(json_encode(["status" => "success", "msg" => __("Nạp tiền thành công") . " " . format_currency($row["received"])]));
    }
    if($_POST["action"] == "notication_topup_korapay") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        require_once __DIR__ . "/../../libs/korapay.php";
        require_once __DIR__ . "/../../libs/database/users.php";
        $user = new users();
        foreach ($CMSNT->get_list(" SELECT * FROM `payment_korapay` WHERE `status` = 0 AND `user_id` = '" . $getUser["id"] . "' ORDER BY `id` DESC LIMIT 3 ") as $row) {
            $secretKey = $CMSNT->site("korapay_secretKey");
            $reference = $row["trans_id"];
            $verification = korapayVerifyCharge($secretKey, $reference);
            if($verification || isset($verification["status"]) || $verification["status"] !== false) {
                if($verification["data"]["status"] == "success") {
                    $isCong = $user->AddCredits($row["user_id"], $row["price"], __("Recharge Korapay") . " #" . $reference, "TOPUP_korapay_" . $reference);
                    if($isCong) {
                        $CMSNT->update("payment_korapay", ["status" => 1, "notication" => 1, "updated_at" => gettime()], " `id` = '" . $row["id"] . "'  ");
                        $CMSNT->insert("deposit_log", ["user_id" => $row["user_id"], "method" => __("Korapay Africa"), "amount" => $amount, "received" => $row["price"], "create_time" => time(), "is_virtual" => 0]);
                        $my_text = $CMSNT->site("noti_recharge");
                        $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                        $my_text = str_replace("{username}", getRowRealtime("users", $row["user_id"], "username"), $my_text);
                        $my_text = str_replace("{method}", __("Recharge Korapay"), $my_text);
                        $my_text = str_replace("{amount}", $amount, $my_text);
                        $my_text = str_replace("{price}", format_currency($row["price"]), $my_text);
                        $my_text = str_replace("{time}", gettime(), $my_text);
                        sendMessAdmin($my_text);
                        exit(json_encode(["status" => "success", "msg" => __("Deposit successful") . " " . format_currency($row["price"])]));
                    }
                }
                if($verification["data"]["status"] == "failed" || $verification["data"]["status"] == "expired") {
                    $CMSNT->update("payment_korapay", ["status" => 2, "updated_at" => gettime()], " `id` = '" . $row["id"] . "'  ");
                }
            }
        }
    }
    if($_POST["action"] == "notication_topup_bakong") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        require_once __DIR__ . "/../../libs/bakong.php";
        require_once __DIR__ . "/../../libs/database/users.php";
        $user = new users();
        foreach ($CMSNT->get_list("\n        SELECT * FROM `payment_bakong` \n        WHERE `user_id` = '" . $getUser["id"] . "' \n        AND `status` = 0 \n        ORDER BY `id` DESC \n        LIMIT 3\n    ") as $payment_bakong) {
            $response = verifyPaymentBakong($payment_bakong["trans_id"], $payment_bakong["amount"]);
            if($response["status"]) {
                $isCong = $user->AddCredits($payment_bakong["user_id"], $payment_bakong["amount"], __("Recharge Bakong Wallet Cambodia") . " #" . $payment_bakong["trans_id"], "TOPUP_Bakong_" . $payment_bakong["trans_id"]);
                if($isCong) {
                    $CMSNT->update("payment_bakong", ["status" => 1, "updated_at" => gettime(), "notication" => 1], " `id` = '" . $payment_bakong["id"] . "' ");
                    $CMSNT->insert("deposit_log", ["user_id" => $payment_bakong["user_id"], "method" => __("Bakong Wallet Cambodia"), "amount" => $payment_bakong["amount"], "received" => $payment_bakong["price"], "create_time" => time(), "is_virtual" => 0]);
                    $my_text = $CMSNT->site("noti_recharge");
                    $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                    $my_text = str_replace("{username}", getRowRealtime("users", $payment_bakong["user_id"], "username"), $my_text);
                    $my_text = str_replace("{method}", __("Bakong Wallet Cambodia"), $my_text);
                    $my_text = str_replace("{amount}", $payment_bakong["amount"], $my_text);
                    $my_text = str_replace("{price}", format_currency($payment_bakong["price"]), $my_text);
                    $my_text = str_replace("{time}", gettime(), $my_text);
                    sendMessAdmin($my_text);
                    exit(json_encode(["status" => "success", "msg" => __("Deposit successful") . " " . format_currency($payment_bakong["price"])]));
                }
            }
        }
    }
    if($_POST["action"] == "notication_topup_tmweasyapi") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        require_once __DIR__ . "/../../libs/tmweasyapi.php";
        require_once __DIR__ . "/../../libs/database/users.php";
        require_once __DIR__ . "/../../libs/database/users.php";
        $user = new users();
        foreach ($CMSNT->get_list("\n        SELECT * FROM `payment_tmweasyapi` \n        WHERE `user_id` = '" . $getUser["id"] . "' \n        AND `status` = 0 \n        ORDER BY `id` DESC \n        LIMIT 3\n    ") as $payment_tmweasyapi) {
            $paramsConfirm = ["username" => $CMSNT->site("tmweasyapi_username"), "password" => $CMSNT->site("tmweasyapi_password"), "con_id" => $CMSNT->site("tmweasyapi_con_id"), "id_pay" => $payment_tmweasyapi["id_pay"], "ip" => myip(), "method" => "confirm"];
            $result = callMaemaneeApi($paramsConfirm);
            if($result === false) {
                exit(json_encode(["status" => "error", "msg" => __("Không thể kết nối API confirm")]));
            }
            if(!empty($result["status"]) && $result["status"] == 1) {
                $ref1 = $result["ref1"] ?? "";
                $amount = check_string($result["amount"]) ?? 0;
                $isCong = $user->AddCredits($payment_tmweasyapi["user_id"], $payment_tmweasyapi["price"], __("Recharge Tmweasyapi Thailand") . " #" . $payment_tmweasyapi["trans_id"], "TOPUP_Tmweasyapi_" . $payment_tmweasyapi["trans_id"]);
                if($isCong) {
                    $CMSNT->update("payment_tmweasyapi", ["status" => 1, "updated_at" => gettime(), "notication" => 1], " `id` = '" . $payment_tmweasyapi["id"] . "' ");
                    $CMSNT->insert("deposit_log", ["user_id" => $payment_tmweasyapi["user_id"], "method" => __("Tmweasyapi Thailand"), "amount" => $amount, "received" => $payment_tmweasyapi["price"], "create_time" => time(), "is_virtual" => 0]);
                    $my_text = $CMSNT->site("noti_recharge");
                    $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
                    $my_text = str_replace("{username}", getRowRealtime("users", $payment_tmweasyapi["user_id"], "username"), $my_text);
                    $my_text = str_replace("{method}", __("Tmweasyapi Thailand"), $my_text);
                    $my_text = str_replace("{amount}", $amount, $my_text);
                    $my_text = str_replace("{price}", format_currency($payment_tmweasyapi["price"]), $my_text);
                    $my_text = str_replace("{time}", gettime(), $my_text);
                    sendMessAdmin($my_text);
                    exit(json_encode(["status" => "success", "msg" => __("Deposit successful") . " " . format_currency($payment_tmweasyapi["price"])]));
                }
            } else {
                $timeCreated = strtotime($payment_tmweasyapi["created_at"]);
                $timeNow = time();
                if(86400 <= $timeNow - $timeCreated) {
                    $CMSNT->update("payment_tmweasyapi", ["status" => 2, "updated_at" => gettime()], " `id` = '" . $payment_tmweasyapi["id"] . "' ");
                }
            }
        }
    }
    if($_POST["action"] == "notication_topup_openpix") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập để sử dụng tính năng này")]));
        }
        if(!($row = $CMSNT->get_row(" SELECT * FROM `payment_openpix` WHERE `notication` = 0 AND `user_id` = '" . $getUser["id"] . "' AND `status` = 1 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Không có lịch sử nạp tiền gần đây")]));
        }
        $CMSNT->update("payment_openpix", ["notication" => 1], " `id` = '" . $row["id"] . "' ");
        exit(json_encode(["status" => "success", "msg" => __("Deposit successful") . " " . format_currency($row["price"])]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Invalid data")]));
}

?>