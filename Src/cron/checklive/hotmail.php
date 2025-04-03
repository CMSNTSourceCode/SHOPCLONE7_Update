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
if($CMSNT->site("time_cron_checklive_hotmail") < time() && time() - $CMSNT->site("time_cron_checklive_hotmail") < 1) {
    exit("Thao tác quá nhanh, vui lòng thử lại sau!");
}
$CMSNT->update("settings", ["value" => time()], " `name` = 'time_cron_checklive_hotmail' ");
$uids = [];
$products_info = [];
$where_is_checklive = "";
$products_list = $CMSNT->get_list("SELECT * FROM `products` WHERE `check_live` = 'Hotmail'");
if(!empty($products_list)) {
    $product_codes = array_map(function ($product) {
        return $product["code"];
    }, $products_list);
    $product_codes_str = implode("','", array_map("addslashes", $product_codes));
    $where_is_checklive = " AND `product_code` IN ('" . $product_codes_str . "')";
    $products = $CMSNT->get_list("SELECT * FROM `product_stock` WHERE `id` > 0 " . $where_is_checklive . " ORDER BY `time_check_live` ASC LIMIT 50 ");
    foreach ($products as $product) {
        if(!in_array(check_string($product["account"]), $uids)) {
            $uids[] = check_string($product["account"]);
        }
        $products_info[check_string($product["account"])] = $product;
    }
    $mh = curl_multi_init();
    $curl_handles = [];
    foreach ($uids as $uid) {
        $ch = curl_init();
        $url = base_url("api/checklive_hotmail.php?account=" . $uid);
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
            if(isset($result) && $result == "DIE") {
                $isInsert = false;
                $isInsert = $CMSNT->insert("product_die", ["product_code" => $products_info[$uid]["product_code"], "seller" => $products_info[$uid]["seller"], "uid" => $products_info[$uid]["uid"], "account" => $products_info[$uid]["account"], "create_gettime" => $products_info[$uid]["create_gettime"], "type" => $products_info[$uid]["type"]]);
                if($isInsert) {
                    $CMSNT->remove("product_stock", " `id` = '" . $products_info[$uid]["id"] . "' ");
                    echo "UID: " . substr($uid, 0, 6) . "*******, Result: DIE <br>";
                }
            } else {
                $CMSNT->update("product_stock", ["time_check_live" => time()], " `id` = '" . $products_info[$uid]["id"] . "' ");
                echo "UID: " . substr($uid, 0, 6) . "*******, Result: LIVE<br>";
            }
        } else {
            $CMSNT->update("product_stock", ["time_check_live" => time()], " `id` = '" . $products_info[$uid]["id"] . "' ");
            $error_message = "UID: " . substr($uid, 0, 6) . "*******, Result: ERROR";
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