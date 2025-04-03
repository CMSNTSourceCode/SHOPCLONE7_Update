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
require_once __DIR__ . "/../../libs/database/users.php";
require_once __DIR__ . "/../../libs/toyyibpay.php";
$Mobile_Detect = new Mobile_Detect();
if($CMSNT->site("status") != 1) {
    $data = json_encode(["status" => "error", "msg" => __("Hệ thống đang bảo trì!")]);
    exit($data);
}
if(!isset($_POST["action"])) {
    $data = json_encode(["status" => "error", "msg" => __("The Request Not Found")]);
    exit($data);
}
if($CMSNT->site("status_demo") != 0) {
    exit(json_encode(["status" => "error", "msg" => __("Chức năng này không thể sử dụng trên website demo")]));
}
if($_POST["action"] == "WithdrawCommission") {
    if($CMSNT->site("status_demo") != 0) {
        exit(json_encode(["status" => "error", "msg" => __("This function cannot be used because this is a demo site")]));
    }
    if($CMSNT->site("affiliate_status") != 1) {
        exit(json_encode(["status" => "error", "msg" => __("Chức năng này đang được bảo trì")]));
    }
    if(empty($_POST["token"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập")]));
    }
    if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập")]));
    }
    if(time() - $getUser["time_request"] < $config["max_time_load"]) {
        exit(json_encode(["status" => "error", "msg" => __("Thao tác quá nhanh, vui lòng chờ")]));
    }
    if(empty($_POST["bank"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng chọn ngân hàng cần rút")]));
    }
    if(empty($_POST["stk"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập số tài khoản cần rút")]));
    }
    if(empty($_POST["name"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập tên chủ tài khoản")]));
    }
    if(empty($_POST["amount"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập số tiền cần rút")]));
    }
    if($_POST["amount"] < $CMSNT->site("affiliate_min")) {
        exit(json_encode(["status" => "error", "msg" => __("Số tiền rút tối thiểu phải là") . " " . format_currency($CMSNT->site("affiliate_min"))]));
    }
    if($getUser["ref_price"] < $_POST["amount"]) {
        exit(json_encode(["status" => "error", "msg" => __("Số dư hoa hồng khả dụng của bạn không đủ")]));
    }
    $amount = check_string($_POST["amount"]);
    $trans_id = random("123456789QWERTYUIOPASDFGHJKLZXCVBNM", 6);
    $User = new users();
    $isTru = $User->RemoveCommission($getUser["id"], $amount, __("Withdraw commission balance") . " #" . $trans_id);
    if($isTru) {
        if(getRowRealtime("users", $getUser["id"], "ref_price") < 0) {
            $User->Banned($getUser["id"], __("Gian lận khi rút số dư hoa hồng"));
            exit(json_encode(["status" => "error", "msg" => __("Tài khoản của bạn đã bị khóa vì gian lận")]));
        }
        $isInsert = $CMSNT->insert("aff_withdraw", ["trans_id" => $trans_id, "user_id" => $getUser["id"], "bank" => check_string($_POST["bank"]), "stk" => check_string($_POST["stk"]), "name" => check_string($_POST["name"]), "amount" => check_string($_POST["amount"]), "status" => "pending", "create_gettime" => gettime(), "update_gettime" => gettime(), "reason" => NULL]);
        if($isInsert) {
            $my_text = $CMSNT->site("noti_affiliate_withdraw");
            $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
            $my_text = str_replace("{username}", $getUser["username"], $my_text);
            $my_text = str_replace("{bank}", check_string($_POST["bank"]), $my_text);
            $my_text = str_replace("{account_number}", check_string($_POST["stk"]), $my_text);
            $my_text = str_replace("{account_name}", check_string($_POST["name"]), $my_text);
            $my_text = str_replace("{amount}", format_currency(check_string($_POST["amount"])), $my_text);
            $my_text = str_replace("{ip}", myip(), $my_text);
            $my_text = str_replace("{time}", gettime(), $my_text);
            sendMessTelegram($my_text, "", $CMSNT->site("affiliate_chat_id_telegram"));
            exit(json_encode(["status" => "success", "msg" => __("Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý")]));
        }
        exit(json_encode(["status" => "error", "msg" => "ERROR 1 - " . __("System error")]));
    }
    exit(json_encode(["status" => "error", "msg" => "ERROR 2 - " . __("System error")]));
}
if($_POST["action"] == "nap_the") {
    if($CMSNT->site("card_status") != 1) {
        exit(json_encode(["status" => "error", "msg" => __("Chức năng nạp thẻ đang được tắt")]));
    }
    if(empty($_POST["token"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập")]));
    }
    if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập")]));
    }
    if(time() - $getUser["time_request"] < $config["max_time_load"]) {
        exit(json_encode(["status" => "error", "msg" => __("Bạn đang thao tác quá nhanh, vui lòng chờ")]));
    }
    if(empty($_POST["telco"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng chọn nhà mạng")]));
    }
    if(empty($_POST["amount"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng chọn mệnh giá cần nạp")]));
    }
    if($_POST["amount"] <= 0) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng chọn mệnh giá cần nạp")]));
    }
    if(empty($_POST["serial"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập serial thẻ")]));
    }
    if(empty($_POST["pin"])) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập mã thẻ")]));
    }
    $telco = check_string($_POST["telco"]);
    $list_network_topup_card = $CMSNT->site("list_network_topup_card");
    $cards = explode("\n", $list_network_topup_card);
    $allowed_cards = [];
    foreach ($cards as $card) {
        $card = trim($card);
        if(!$card) {
        } else {
            $arr = explode("|", $card);
            if(count($arr) == 2) {
                $allowed_cards[] = $arr[0];
            }
        }
    }
    if(!in_array($telco, $allowed_cards)) {
        exit(json_encode(["status" => "error", "msg" => __("Loại thẻ không được hỗ trợ")]));
    }
    $amount = check_string($_POST["amount"]);
    $serial = check_string($_POST["serial"]);
    $pin = check_string($_POST["pin"]);
    $checkResult = checkFormatCard($telco, $serial, $pin);
    if($checkResult["status"] !== true) {
        exit(json_encode(["status" => "error", "msg" => $checkResult["msg"]]));
    }
    if(5 < $CMSNT->num_rows(" SELECT * FROM `cards` WHERE `user_id` = '" . $getUser["id"] . "' AND `status` = 'pending'  ")) {
        exit(json_encode(["status" => "error", "msg" => __("Vui lòng không spam!")]));
    }
    if(5 <= $CMSNT->num_rows("SELECT * FROM `cards` WHERE `status` = 'error' AND `user_id` = '" . $getUser["id"] . "' AND `create_date` >= DATE(NOW()) AND `create_date` < DATE(NOW()) + INTERVAL 1 DAY  ") - $CMSNT->num_rows("SELECT * FROM `cards` WHERE `status` = 'complted' AND `user_id` = '" . $getUser["id"] . "' AND `create_date` >= DATE(NOW()) AND `create_date` < DATE(NOW()) + INTERVAL 1 DAY  ")) {
        exit(json_encode(["status" => "error", "msg" => __("Bạn đã bị chặn sử dụng chức năng nạp thẻ trong 1 ngày")]));
    }
    $trans_id = random("QWERTYUIOPASDFGHJKLZXCVBNM", 6) . time();
    $data = card24h($telco, $amount, $serial, $pin, $trans_id);
    if($data["status"] == 99) {
        $isInsert = $CMSNT->insert("cards", ["trans_id" => $trans_id, "telco" => $telco, "amount" => $amount, "serial" => $serial, "pin" => $pin, "price" => 0, "user_id" => $getUser["id"], "status" => "pending", "reason" => "", "create_date" => gettime(), "update_date" => gettime()]);
        if($isInsert) {
            $CMSNT->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Thực hiện nạp thẻ Serial: " . $serial . " - Pin: " . $pin]);
            checkBlockIP("PAYMENT", 5);
            exit(json_encode(["status" => "success", "msg" => __("Đẩy thẻ lên thành công, vui lòng chờ xử lý thẻ trong giây lát!")]));
        }
        exit(json_encode(["status" => "error", "msg" => __("Nạp thẻ thất bại, vui lòng liên hệ Admin")]));
    }
    exit(json_encode(["status" => "error", "msg" => $data["data"]["msg"]]));
} else {
    if($_POST["action"] == "RechargeCrypto") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng đăng nhập")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập số tiền cần nạp")]));
        }
        $amount = check_string($_POST["amount"]);
        if($amount < $CMSNT->site("crypto_min")) {
            exit(json_encode(["status" => "error", "msg" => __("Số tiền gửi tối thiểu là:") . " \$" . $CMSNT->site("crypto_min")]));
        }
        if($CMSNT->site("crypto_max") < $amount) {
            exit(json_encode(["status" => "error", "msg" => __("Số tiền gửi tối đa là:") . " \$" . format_cash($CMSNT->site("crypto_max"))]));
        }
        if($CMSNT->site("crypto_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("Chức năng này đang được bảo trì")]));
        }
        if($CMSNT->site("crypto_token") == "" || $CMSNT->site("crypto_address") == "") {
            exit(json_encode(["status" => "error", "msg" => __("Chức năng này chưa được cấu hình, vui lòng liên hệ Admin")]));
        }
        if(3 <= $CMSNT->num_rows(" SELECT * FROM `payment_crypto` WHERE `user_id` = '" . $getUser["id"] . "' AND `status` = 'waiting' AND ROUND(`amount`) = '" . $amount . "'  ")) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng không SPAM")]));
        }
        $name = "Recharge " . check_string($_SERVER["HTTP_HOST"]);
        $description = "Recharge invoice to " . $getUser["username"];
        $callback = base_url("api/callback_crypto.php");
        $return_url = base_url("client/recharge-crypto");
        $request_id = md5(time() . random("qwertyuiopasdfghjklzxcvbnm0123456789", 4));
        $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
        $result = file_get_contents("https://fpayment.co/api/AddInvoice.php?token_wallet=" . $CMSNT->site("crypto_token") . "&address_wallet=" . trim($CMSNT->site("crypto_address")) . "&name=" . urlencode($name) . "&description=" . urlencode($description) . "&amount=" . $amount . "&request_id=" . $request_id . "&callback=" . urlencode($callback) . "&return_url=" . urlencode($return_url), false, stream_context_create($arrContextOptions));
        $result = json_decode($result, true);
        if(!isset($result["status"])) {
            exit(json_encode(["status" => "error", "msg" => __("Không thể tạo hóa đơn do lỗi API, vui lòng thử lại sau")]));
        }
        if($result["status"] == "error") {
            exit(json_encode(["status" => "error", "msg" => __($result["msg"])]));
        }
        $trans_id = check_string($result["data"]["trans_id"]);
        $received = check_string($result["data"]["amount"]) * $CMSNT->site("crypto_rate");
        $isInsert = $CMSNT->insert("payment_crypto", ["trans_id" => $trans_id, "user_id" => $getUser["id"], "request_id" => check_string($result["data"]["request_id"]), "amount" => check_string($result["data"]["amount"]), "received" => $received, "create_gettime" => gettime(), "update_gettime" => gettime(), "status" => check_string($result["data"]["status"]), "url_payment" => check_string($result["data"]["url_payment"]), "msg" => NULL]);
        if($isInsert) {
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Generate Crypto Recharge Invoice") . " #" . $trans_id]);
            checkBlockIP("PAYMENT", 5);
            exit(json_encode(["url" => check_string($result["data"]["url_payment"]), "status" => "success", "msg" => __("Tạo hoá đơn nạp tiền thành công")]));
        }
    }
    if($_POST["action"] == "RechargeCryptoNew") {
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please enter amount")]));
        }
        $amount = check_string($_POST["amount"]);
        if($amount < $CMSNT->site("crypto_min")) {
            exit(json_encode(["status" => "error", "msg" => __("The minimum deposit amount is:") . " \$" . $CMSNT->site("crypto_min")]));
        }
        if($CMSNT->site("crypto_max") < $amount) {
            exit(json_encode(["status" => "error", "msg" => __("The maximum deposit amount is:") . " \$" . format_cash($CMSNT->site("crypto_max"))]));
        }
        if($CMSNT->site("crypto_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("This function is under maintenance")]));
        }
        if($CMSNT->site("crypto_merchant_id") == "" || $CMSNT->site("crypto_api_key") == "") {
            exit(json_encode(["status" => "error", "msg" => __("Chức năng này chưa được cấu hình, vui lòng liên hệ Admin")]));
        }
        if(3 <= $CMSNT->num_rows(" SELECT * FROM `payment_crypto` WHERE `user_id` = '" . $getUser["id"] . "' AND `status` = 'waiting' AND ROUND(`amount`) = '" . $amount . "'  ")) {
            exit(json_encode(["status" => "error", "msg" => __("Please do not SPAM")]));
        }
        $request_id = md5(time() . random("qwertyuiopasdfghjklzxcvbnm0123456789", 5));
        $curl = curl_init();
        curl_setopt_array($curl, [CURLOPT_URL => "https://app.fpayment.net/api/AddInvoice", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["merchant_id" => $CMSNT->site("crypto_merchant_id"), "api_key" => $CMSNT->site("crypto_api_key"), "name" => "Recharge " . check_string($_SERVER["HTTP_HOST"]), "description" => "Recharge invoice to " . $getUser["username"], "amount" => $amount, "request_id" => $request_id, "callback_url" => base_url("api/callback_crypto_new.php"), "success_url" => base_url("client/recharge-crypto"), "cancel_url" => base_url("client/recharge-crypto")]]);
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        if(!isset($result["status"])) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng liên hệ Admin FPAYMENT khắc phục vấn đề này nếu bạn là Admin website.")]));
        }
        if($result["status"] == "success") {
            $trans_id = check_string($result["data"]["trans_id"]);
            $amount = check_string($result["data"]["amount"]);
            $status = check_string($result["data"]["status"]);
            $url_payment = check_string($result["data"]["url_payment"]);
            $received = $amount * $CMSNT->site("crypto_rate");
            $isInsert = $CMSNT->insert("payment_crypto", ["trans_id" => $trans_id, "user_id" => $getUser["id"], "request_id" => $request_id, "amount" => $amount, "received" => $received, "create_gettime" => gettime(), "update_gettime" => gettime(), "status" => $status, "url_payment" => $url_payment, "msg" => NULL]);
            if($isInsert) {
                $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Tạo hóa đơn nạp tiền điện tử") . " #" . $trans_id]);
                checkBlockIP("PAYMENT", 5);
                exit(json_encode(["url" => $url_payment, "status" => "success", "msg" => __("Tạo hóa đơn nạp tiền thành công")]));
            }
            exit(json_encode(["status" => "error", "msg" => __("Tạo hóa đơn nạp tiền thất bại")]));
        }
        exit(json_encode(["status" => "error", "msg" => __($result["msg"])]));
    }
    if($_POST["action"] == "CreateToyyibpay") {
        if($CMSNT->site("status_demo") != 0) {
            exit(json_encode(["status" => "error", "msg" => __("You cannot use this function because this is a demo site")]));
        }
        if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
            exit(json_encode(["status" => "error", "msg" => __("The system is maintenance")]));
        }
        if($CMSNT->site("toyyibpay_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("This function is under maintenance")]));
        }
        if($CMSNT->site("toyyibpay_userSecretKey") == "") {
            exit(json_encode(["status" => "error", "msg" => __("This function has not been configured")]));
        }
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(time() - $getUser["time_request"] < $config["max_time_load"]) {
            exit(json_encode(["status" => "error", "msg" => __("You are working too fast, please wait")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please enter deposit amount")]));
        }
        if($_POST["amount"] <= 0) {
            exit(json_encode(["status" => "error", "msg" => __("Deposit amount is not available")]));
        }
        if($_POST["amount"] < $CMSNT->site("toyyibpay_min")) {
            exit(json_encode(["status" => "error", "msg" => __("Minimum deposit amount is RM" . $CMSNT->site("toyyibpay_min") . "")]));
        }
        $amount = check_string($_POST["amount"]);
        $trans_id = random("QWERTYUIOPASDFGHJKLZXCVBNM", 3) . time();
        $toyyibpay = new toyyibpay($CMSNT->site("toyyibpay_userSecretKey"));
        $result = $toyyibpay->createBill(["categoryCode" => $CMSNT->site("toyyibpay_categoryCode"), "billName" => "Invoice - RM " . $amount, "billDescription" => "Recharge invoice on website " . $_SERVER["HTTP_HOST"], "billPriceSetting" => 1, "billPayorInfo" => 0, "billAmount" => check_string($_POST["amount"]) * 100, "billReturnUrl" => base_url("client/recharge-toyyibpay"), "billCallbackUrl" => base_url("api/callback_toyyibpay.php"), "billExternalReferenceNo" => $trans_id, "billTo" => $getUser["username"], "billEmail" => !empty($getUser["email"]) ? $getUser["email"] : "None", "billPhone" => !empty($getUser["phone"]) ? $getUser["phone"] : 0, "billSplitPayment" => 0, "billSplitPaymentArgs" => "", "billPaymentChannel" => 0, "billContentEmail" => "Thank you for using our system", "billChargeToCustomer" => $CMSNT->site("toyyibpay_billChargeToCustomer"), "billExpiryDate" => "", "billExpiryDays" => 3]);
        $result = json_decode($result, true);
        $BillCode = $result[0]["BillCode"];
        if(!isset($BillCode)) {
            exit(json_encode(["status" => "error", "msg" => __("Error API!")]));
        }
        $isInsert = $CMSNT->insert("payment_toyyibpay", ["user_id" => $getUser["id"], "trans_id" => $trans_id, "billName" => "Invoice - RM " . $amount, "amount" => $amount, "status" => 0, "BillCode" => $BillCode, "create_gettime" => gettime(), "update_gettime" => gettime()]);
        if($isInsert) {
            $CMSNT->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
            $Mobile_Detect = new Mobile_Detect();
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Create Recharge Bank Malaysia Invoice #") . " " . $trans_id]);
            checkBlockIP("PAYMENT", 5);
            exit(json_encode(["invoice_url" => "https://toyyibpay.com/" . $BillCode, "status" => "success", "msg" => __("Successful!")]));
        }
        exit(json_encode(["status" => "error", "msg" => __("Error!")]));
    }
    if($_POST["action"] == "RechargeKorapay") {
        if($CMSNT->site("status_demo") != 0) {
            exit(json_encode(["status" => "error", "msg" => __("You cannot use this function because this is a demo site")]));
        }
        if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
            exit(json_encode(["status" => "error", "msg" => __("The system is maintenance")]));
        }
        if($CMSNT->site("korapay_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("This function is under maintenance")]));
        }
        if($CMSNT->site("korapay_secretKey") == "") {
            exit(json_encode(["status" => "error", "msg" => __("This function has not been configured")]));
        }
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(time() - $getUser["time_request"] < $config["max_time_load"]) {
            exit(json_encode(["status" => "error", "msg" => __("You are working too fast, please wait")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please enter deposit amount")]));
        }
        if($_POST["amount"] <= 0) {
            exit(json_encode(["status" => "error", "msg" => __("Deposit amount is not available")]));
        }
        if($_POST["amount"] < $CMSNT->site("korapay_min")) {
            exit(json_encode(["status" => "error", "msg" => __("Minimum deposit amount is " . $CMSNT->site("korapay_min") . "")]));
        }
        if($CMSNT->site("korapay_max") < $_POST["amount"]) {
            exit(json_encode(["status" => "error", "msg" => __("Maximum deposit amount is " . $CMSNT->site("korapay_max") . "")]));
        }
        $amount = check_string($_POST["amount"]);
        $trans_id = random("QWERTYUIOPASDFGHJKLZXCVBNM", 3) . time();
        $price = $amount * $CMSNT->site("korapay_rate");
        require_once __DIR__ . "/../../libs/korapay.php";
        $params = ["amount" => (int) $amount, "currency" => $CMSNT->site("korapay_currency_code"), "reference" => $trans_id, "redirect_url" => base_url("?action=recharge-korapay"), "notification_url" => base_url("api/callback_korapay.php"), "narration" => "Deposit money into " . $getUser["username"], "customer" => ["email" => $getUser["email"]]];
        $secretKey = $CMSNT->site("korapay_secretKey");
        $response = korapayInitializeCharge($secretKey, $params);
        if($response && isset($response["status"]) && $response["status"] === true) {
            $checkoutUrl = $response["data"]["checkout_url"];
            $reference = $response["data"]["reference"];
            $isInsert = $CMSNT->insert("payment_korapay", ["user_id" => $getUser["id"], "trans_id" => $reference, "price" => $price, "amount" => $amount, "status" => 0, "created_at" => gettime(), "updated_at" => gettime(), "checkout_url" => $checkoutUrl]);
            if($isInsert) {
                $CMSNT->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
                $Mobile_Detect = new Mobile_Detect();
                $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Create Korapay top-up invoice #") . " " . $trans_id]);
                checkBlockIP("PAYMENT", 5);
                exit(json_encode(["invoice_url" => $checkoutUrl, "status" => "success", "msg" => __("Successful!")]));
            }
        } else {
            exit(json_encode(["status" => "error", "msg" => $response["message"]]));
        }
    }
    if($_POST["action"] == "RechargeTmweasyapi") {
        if($CMSNT->site("status_demo") != 0) {
            exit(json_encode(["status" => "error", "msg" => __("You cannot use this function because this is a demo site")]));
        }
        if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
            exit(json_encode(["status" => "error", "msg" => __("The system is maintenance")]));
        }
        if($CMSNT->site("tmweasyapi_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("This function is under maintenance")]));
        }
        if($CMSNT->site("tmweasyapi_username") == "" || $CMSNT->site("tmweasyapi_password") == "" || $CMSNT->site("tmweasyapi_con_id") == "") {
            exit(json_encode(["status" => "error", "msg" => __("This function has not been configured")]));
        }
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(time() - $getUser["time_request"] < $config["max_time_load"]) {
            exit(json_encode(["status" => "error", "msg" => __("You are working too fast, please wait")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please enter deposit amount")]));
        }
        if($_POST["amount"] <= 0) {
            exit(json_encode(["status" => "error", "msg" => __("Deposit amount is not available")]));
        }
        if($_POST["amount"] < $CMSNT->site("tmweasyapi_min")) {
            exit(json_encode(["status" => "error", "msg" => __("Minimum deposit amount is " . $CMSNT->site("tmweasyapi_min") . "")]));
        }
        if($CMSNT->site("tmweasyapi_max") < $_POST["amount"]) {
            exit(json_encode(["status" => "error", "msg" => __("Maximum deposit amount is " . $CMSNT->site("tmweasyapi_max") . "")]));
        }
        $amount = check_string($_POST["amount"]);
        $trans_id = random("QWERTYUIOPASDFGHJKLZXCVBNM", 3) . time();
        $price = $amount * $CMSNT->site("tmweasyapi_rate");
        require_once __DIR__ . "/../../libs/tmweasyapi.php";
        $paramsCreate = ["username" => $CMSNT->site("tmweasyapi_username"), "password" => $CMSNT->site("tmweasyapi_password"), "con_id" => $CMSNT->site("tmweasyapi_con_id"), "amount" => $amount, "ref1" => $trans_id, "method" => "create_pay"];
        $responseCreate = callMaemaneeApi($paramsCreate);
        if($responseCreate === false) {
            exit(json_encode(["status" => "error", "msg" => __("Lỗi gọi API create_pay")]));
        }
        if(isset($responseCreate["status"]) && $responseCreate["status"] == 1) {
            $idPay = $responseCreate["id_pay"];
            $paramsDetail = ["username" => $CMSNT->site("tmweasyapi_username"), "password" => $CMSNT->site("tmweasyapi_password"), "con_id" => $CMSNT->site("tmweasyapi_con_id"), "id_pay" => $idPay, "qr" => 1, "method" => "detail_pay"];
            $responseDetail = callMaemaneeApi($paramsDetail);
            if($responseDetail === false) {
                exit(json_encode(["status" => "error", "msg" => __("Lỗi gọi API detail_pay")]));
            }
            if(isset($responseDetail["status"]) && $responseDetail["status"] == 1) {
                $ref1 = check_string($responseDetail["ref1"]);
                $amount = check_string($responseDetail["amount"]);
                $urlPay = check_string($responseDetail["urlpay"]);
                $timeOut = check_string($responseDetail["time_out"]);
                $isInsert = $CMSNT->insert("payment_tmweasyapi", ["user_id" => $getUser["id"], "trans_id" => $trans_id, "id_pay" => $idPay, "price" => $price, "amount" => $amount, "status" => 0, "created_at" => gettime(), "updated_at" => gettime(), "checkout_url" => $urlPay]);
                if($isInsert) {
                    $CMSNT->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
                    $Mobile_Detect = new Mobile_Detect();
                    $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Create Tmweasyapi Thailand top-up invoice #") . " " . $trans_id]);
                    checkBlockIP("PAYMENT", 5);
                    exit(json_encode(["invoice_url" => $urlPay, "qr" => $responseDetail["qr_base64_image"], "time_out" => $timeOut, "amount" => $amount, "status" => "success", "msg" => __("Successful!")]));
                }
                exit(json_encode(["status" => "error", "msg" => __("Không thể tạo hóa đơn nạp tiền!")]));
            }
            $msgError = isset($responseDetail["msg"]) ? $responseDetail["msg"] : "Không rõ lỗi";
            exit("Không thể lấy chi tiết thanh toán. Lý do: " . $msgError);
        }
        $msgError = isset($responseCreate["msg"]) ? check_string($responseCreate["msg"]) : "Không rõ lỗi";
        exit(json_encode(["status" => "error", "msg" => $msgError]));
    }
    if($_POST["action"] == "RechargeOpenPix") {
        if($CMSNT->site("status_demo") != 0) {
            exit(json_encode(["status" => "error", "msg" => __("You cannot use this function because this is a demo site")]));
        }
        if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
            exit(json_encode(["status" => "error", "msg" => __("The system is maintenance")]));
        }
        if($CMSNT->site("openpix_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("This function is under maintenance")]));
        }
        if($CMSNT->site("openpix_api_key") == "") {
            exit(json_encode(["status" => "error", "msg" => __("This function has not been configured")]));
        }
        if($CMSNT->site("openpix_HMAC_key") == "") {
            exit(json_encode(["status" => "error", "msg" => __("This function has not been configured")]));
        }
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(time() - $getUser["time_request"] < $config["max_time_load"]) {
            exit(json_encode(["status" => "error", "msg" => __("You are working too fast, please wait")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please enter deposit amount")]));
        }
        if($_POST["amount"] <= 0) {
            exit(json_encode(["status" => "error", "msg" => __("Deposit amount is not available")]));
        }
        if($_POST["amount"] < $CMSNT->site("openpix_min")) {
            exit(json_encode(["status" => "error", "msg" => __("Minimum deposit amount is " . $CMSNT->site("openpix_min") . "")]));
        }
        if($CMSNT->site("openpix_max") < $_POST["amount"]) {
            exit(json_encode(["status" => "error", "msg" => __("Maximum deposit amount is " . $CMSNT->site("openpix_max") . "")]));
        }
        $amount = check_string($_POST["amount"]);
        $trans_id = random("QWERTYUIOPASDFGHJKLZXCVBNM", 3) . time();
        $price = $amount * $CMSNT->site("openpix_rate");
        $openpix_value = (double) $amount * 100;
        $data = ["correlationID" => $trans_id, "value" => $openpix_value, "comment" => "Topup " . $getUser["username"]];
        $json = json_encode($data);
        $ch = curl_init();
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => "https://api.openpix.com.br/api/v1/charge?return_existing=true", CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_POSTFIELDS => $json, CURLOPT_POST => 1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_HTTPHEADER => ["Authorization: " . $CMSNT->site("openpix_api_key"), "content-type: application/json"]]);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if($response && isset($response["charge"]) && $response["charge"]["status"] === "ACTIVE" || $response && isset($response["status"]) && $response["status"] === "ACTIVE") {
            $transactionID = isset($response["charge"]) ? $response["charge"]["transactionID"] : $response["pix"]["transactionID"];
            $qrCodeImage = isset($response["charge"]) ? $response["charge"]["qrCodeImage"] : $response["pix"]["qrCodeImage"];
            $checkoutUrl = isset($response["charge"]) ? $response["charge"]["paymentLinkUrl"] : $response["paymentLinkUrl"];
            $reference = isset($response["charge"]) ? $response["charge"]["correlationID"] : $response["correlationID"];
            $isInsert = $CMSNT->insert("payment_openpix", ["user_id" => $getUser["id"], "trans_id" => $trans_id, "price" => $price, "amount" => $amount, "status" => 0, "created_at" => gettime(), "updated_at" => gettime(), "checkout_url" => $checkoutUrl]);
            if($isInsert) {
                $CMSNT->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
                $Mobile_Detect = new Mobile_Detect();
                $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Create OpenPix top-up invoice #") . " " . $trans_id]);
                checkBlockIP("PAYMENT", 5);
                exit(json_encode(["invoice_url" => $checkoutUrl, "status" => "success", "msg" => __("Successful!")]));
            }
            exit(json_encode(["status" => "error", "msg" => __("Failed to create invoice")]));
        }
        if(isset($response["error"])) {
            exit(json_encode(["status" => "error", "msg" => $response["error"]]));
        }
        exit(json_encode(["status" => "error", "msg" => "Unknown error occurred"]));
    }
    if($_POST["action"] == "RechargeBakong") {
        if($CMSNT->site("status_demo") != 0) {
            exit(json_encode(["status" => "error", "msg" => __("You cannot use this function because this is a demo site")]));
        }
        if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
            exit(json_encode(["status" => "error", "msg" => __("The system is maintenance")]));
        }
        if($CMSNT->site("bakong_status") != 1) {
            exit(json_encode(["status" => "error", "msg" => __("This function is under maintenance")]));
        }
        if(empty($_POST["token"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '" . check_string($_POST["token"]) . "' AND `banned` = 0 "))) {
            exit(json_encode(["status" => "error", "msg" => __("Please log in")]));
        }
        if(time() - $getUser["time_request"] < $config["max_time_load"]) {
            exit(json_encode(["status" => "error", "msg" => __("You are working too fast, please wait")]));
        }
        if(empty($_POST["amount"])) {
            exit(json_encode(["status" => "error", "msg" => __("Please enter deposit amount")]));
        }
        if($_POST["amount"] <= 0) {
            exit(json_encode(["status" => "error", "msg" => __("Deposit amount is not available")]));
        }
        if($_POST["amount"] < $CMSNT->site("bakong_min")) {
            exit(json_encode(["status" => "error", "msg" => __("Minimum deposit amount is " . $CMSNT->site("bakong_min") . "")]));
        }
        if($CMSNT->site("bakong_max") < $_POST["amount"]) {
            exit(json_encode(["status" => "error", "msg" => __("Maximum deposit amount is " . $CMSNT->site("bakong_max") . "")]));
        }
        $amount = check_string((double) $_POST["amount"]);
        $trans_id = random("123456789", 4) . time();
        $price = $amount * $CMSNT->site("bakong_rate");
        require_once __DIR__ . "/../../libs/bakong.php";
        $params = ["amount" => $amount, "transaction_id" => $trans_id, "success_url" => base_url("?action=recharge-bakong"), "remark" => "Topup " . $getUser["username"]];
        $response = createPaymentBakong($params);
        if(isset($response)) {
            $isInsert = $CMSNT->insert("payment_bakong", ["user_id" => $getUser["id"], "trans_id" => $trans_id, "price" => $price, "amount" => $amount, "status" => 0, "created_at" => gettime(), "updated_at" => gettime(), "checkout_url" => NULL]);
            if($isInsert) {
                $CMSNT->update("users", ["time_request" => time()], " `id` = '" . $getUser["id"] . "' ");
                $Mobile_Detect = new Mobile_Detect();
                $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Create Bakong Wallet Cambodia top-up invoice #") . " " . $trans_id]);
                checkBlockIP("PAYMENT", 5);
                exit(json_encode(["status" => "success", "msg" => __("Successful!"), "invoice_url" => $response]));
            }
            exit(json_encode(["status" => "error", "msg" => __("Failed to create invoice 1")]));
        }
        exit(json_encode(["status" => "error", "msg" => __("Failed to create invoice")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Request does not exist")]));
}

?>