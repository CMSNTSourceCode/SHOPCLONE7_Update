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
$CMSNT = new DB();
if(empty($_GET["api_key"])) {
    exit(json_encode(["status" => "error", "msg" => __("Thiếu api_key")]));
}
if(empty($_GET["product"])) {
    exit(json_encode(["status" => "error", "msg" => __("Thiếu product")]));
}
$client_domain = isset($_SERVER["HTTP_X_CLIENT_DOMAIN"]) ? $_SERVER["HTTP_X_CLIENT_DOMAIN"] : NULL;
if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '" . check_string($_REQUEST["api_key"]) . "' AND `banned` = 0 "))) {
    checkBlockIP("API", 15);
    exit(json_encode(["status" => "error", "msg" => __("API Key không hợp lệ")]));
}
$data_product = [];
foreach ($CMSNT->get_list(" SELECT * FROM `products` WHERE `id` = '" . check_string($_GET["product"]) . "' AND `status` = 1 AND `allow_api` = 1 ") as $product) {
    $stock = $product["supplier_id"] != 0 ? $product["api_stock"] : getStock($product["code"]);
    $data_product[] = ["id" => $product["id"], "name" => $product["name"], "price" => $product["price"], "amount" => (int) $stock, "description" => $product["short_desc"], "flag" => $product["flag"], "min" => (int) $product["min"], "max" => (int) $product["max"]];
}
exit(json_encode(["status" => "success", "msg" => __("Lấy dữ liệu thành công!"), "product" => $data_product], JSON_PRETTY_PRINT));

?>