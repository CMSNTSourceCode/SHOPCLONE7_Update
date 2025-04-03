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
if($CMSNT->site("time_cron_checklive_instagram") < time() && time() - $CMSNT->site("time_cron_checklive_instagram") < 1) {
    exit("Thao tác quá nhanh, vui lòng thử lại sau!");
}
$CMSNT->update("settings", ["value" => time()], " `name` = 'time_cron_checklive_instagram' ");
if($CMSNT->site("api_check_live_instagram") == "") {
    exit("Bạn chưa cấu hình API, cấu hình tại Cài đặt -> Kết nối");
}
$uids = [];
$emails = [];
$products_info = [];
$where_is_checklive = "";
$products_list = $CMSNT->get_list("SELECT * FROM `products` WHERE `check_live` = 'Instagram'");
if(!empty($products_list)) {
    $product_codes = array_map(function ($product) {
        return $product["code"];
    }, $products_list);
    $product_codes_str = implode("','", array_map("addslashes", $product_codes));
    $where_is_checklive = " AND `product_code` IN ('" . $product_codes_str . "')";
    $thirty_minutes_ago = time() - $CMSNT->site("time_limit_check_live_instagram");
    $products = $CMSNT->get_list("SELECT * FROM `product_stock` WHERE `time_check_live` < " . $thirty_minutes_ago . " " . $where_is_checklive . " ORDER BY `time_check_live` ASC LIMIT 1000");
    foreach ($products as $product) {
        if(!in_array(check_string($product["uid"]), $uids)) {
            $uids[] = check_string($product["uid"]);
        }
        $products_info[check_string($product["uid"])] = $product;
    }
    $mh = curl_multi_init();
    $curl_handles = [];
    foreach ($uids as $uid) {
        $ch = curl_init();
        $url = $CMSNT->site("api_check_live_instagram") . "?account=" . $uid . "&api_key=" . $CMSNT->site("api_key_check_live_instagram");
        curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true]);
        curl_multi_add_handle($mh, $ch);
        $curl_handles[$uid] = $ch;
    }
    $running = NULL;
    do {
        curl_multi_exec($mh, $running);
    } while (0 >= $running);
    foreach ($curl_handles as $uid => $ch) {
        $result = curl_multi_getcontent($ch);
        $info = curl_getinfo($ch);
        if($info["http_code"] == 200) {
            $result_array = json_decode($result, true);
            if(isset($result_array["data"]) && $result_array["data"]["status"] == "live") {
                $CMSNT->update("product_stock", ["time_check_live" => time()], " `id` = '" . $products_info[$uid]["id"] . "' ");
                echo "UID: " . substr($uid, 0, 8) . "*******, Result: LIVE <br>";
            } elseif(isset($result_array["data"]) && $result_array["data"]["status"] == "die") {
                $isInsert = false;
                $isInsert = $CMSNT->insert("product_die", ["product_code" => $products_info[$uid]["product_code"], "seller" => $products_info[$uid]["seller"], "uid" => $products_info[$uid]["uid"], "account" => $products_info[$uid]["account"], "create_gettime" => $products_info[$uid]["create_gettime"], "type" => $products_info[$uid]["type"]]);
                if($isInsert) {
                    $CMSNT->remove("product_stock", " `id` = '" . $products_info[$uid]["id"] . "' ");
                    echo "UID: " . substr($uid, 0, 8) . "*******, Result: DIE " . $result . "<br>";
                }
            } else {
                $error_message = "UID: " . substr($uid, 0, 8) . "*******, Result: ERROR, Error: " . $result;
                echo $error_message . "<br>";
            }
        } else {
            $error_message = "UID: " . substr($uid, 0, 8) . "*******, Result: ERROR, Error: " . $result;
            echo $error_message . "<br>";
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    curl_multi_close($mh);
} else {
    exit("Không có sản phẩm nào bật check live");
}

?>