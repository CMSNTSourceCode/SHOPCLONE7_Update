<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/../../libs/db.php";
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../libs/lang.php";
require_once __DIR__ . "/../../libs/helper.php";
$CMSNT = new DB();
if($CMSNT->site("time_cron_checklive_gmail") < time() && time() - $CMSNT->site("time_cron_checklive_gmail") < 1) {
    exit("Thao tác quá nhanh, vui lòng thử lại sau!");
}
$CMSNT->update("settings", ["value" => time()], " `name` = 'time_cron_checklive_gmail' ");
if($CMSNT->site("api_check_live_gmail") == "") {
    exit("Bạn chưa cấu hình API, cấu hình tại Cài đặt -> Kết nối");
}
$uids = [];
$emails = [];
$products_info = [];
$where_is_checklive = "";
$products_list = $CMSNT->get_list("SELECT * FROM `products` WHERE `check_live` = 'Gmail'");
if(!empty($products_list)) {
    $product_codes = array_map(function ($product) {
        return $product["code"];
    }, $products_list);
    $product_codes_str = implode("','", array_map("addslashes", $product_codes));
    $where_is_checklive = " AND `product_code` IN ('" . $product_codes_str . "')";
    $thirty_minutes_ago = time() - $CMSNT->site("time_limit_check_live_gmail");
    $products = $CMSNT->get_list("SELECT * FROM `product_stock` WHERE `time_check_live` < " . $thirty_minutes_ago . " " . $where_is_checklive . " ORDER BY `time_check_live` ASC LIMIT 1000");
    foreach ($products as $product) {
        $email = $product["uid"];
        if(!in_array($email, $uids)) {
            $uids[] = $email;
            $emails[] = ["email" => $email];
        }
        $products_info[$email] = $product;
    }
    $ch = curl_init();
    $url = $CMSNT->site("api_check_live_gmail");
    $api_key = $CMSNT->site("api_key_check_live_gmail");
    curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => json_encode(["api_key" => $api_key, "emails" => $emails]), CURLOPT_HTTPHEADER => ["Content-Type: application/json"]]);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    if($info["http_code"] == 200) {
        $response_data = json_decode($result, true);
        foreach ($response_data["data"] as $email_data) {
            $email = $email_data["email"];
            $status = $email_data["status"];
            if($status == "live") {
                $CMSNT->update("product_stock", ["time_check_live" => time()], " `id` = '" . $products_info[$email]["id"] . "' ");
                echo "GMAIL: " . $email . ", Result: LIVE | " . $status . "<br>";
            } else {
                $isInsert = false;
                $isInsert = $CMSNT->insert("product_die", ["product_code" => $products_info[$email]["product_code"], "seller" => $products_info[$email]["seller"], "uid" => $products_info[$email]["uid"], "account" => $products_info[$email]["account"], "create_gettime" => $products_info[$email]["create_gettime"], "type" => $products_info[$email]["type"]]);
                if($isInsert) {
                    $CMSNT->remove("product_stock", " `id` = '" . $products_info[$email]["id"] . "' ");
                    echo "GMAIL: " . $email . ", Result: DIE | " . $status . " <br>";
                }
            }
        }
    } else {
        foreach ($uids as $email) {
            $CMSNT->update("product_stock", ["time_check_live" => time()], " `id` = '" . $products_info[$email]["id"] . "' ");
            $error_message = "GMAIL: " . substr($email, 0, 6) . "*******, Result: ERROR";
            echo $error_message . "<br>";
        }
    }
    curl_close($ch);
} else {
    exit("Không có sản phẩm nào bật check live");
}

?>