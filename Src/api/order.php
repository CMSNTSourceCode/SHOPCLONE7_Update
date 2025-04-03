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
if(empty($_GET["order"])) {
    exit(json_encode(["status" => "error", "msg" => __("Thiếu order")]));
}
if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '" . check_string($_REQUEST["api_key"]) . "' AND `banned` = 0 "))) {
    checkBlockIP("API", 15);
    exit(json_encode(["status" => "error", "msg" => __("API Key không hợp lệ")]));
}
if($getUser["banned"] == 1) {
    exit(json_encode(["status" => "error", "msg" => __("Tài khoản đã bị khóa")]));
}
$order = check_string($_GET["order"]);
$order = $CMSNT->get_row("SELECT * FROM `product_order` WHERE `trans_id` = '" . $order . "' AND `buyer` = '" . $getUser["id"] . "' ");
if(!$order) {
    exit(json_encode(["status" => "error", "msg" => __("Đơn hàng không tồn tại")]));
}
$accounts = [];
$accounts_data = $CMSNT->get_list("SELECT * FROM `product_sold` WHERE `trans_id` = '" . $order["trans_id"] . "' ");
if(empty($accounts_data)) {
    exit(json_encode(["status" => "error", "msg" => __("Không có tài khoản nào trong đơn hàng này")]));
}
foreach ($accounts_data as $account) {
    $accounts[] = $account["account"];
}
exit(json_encode(["status" => "success", "msg" => __("Lấy đơn hàng thành công!"), "trans_id" => $order["trans_id"], "data" => $accounts]));

?>