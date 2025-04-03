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
if(!($getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '" . check_string($_REQUEST["api_key"]) . "' AND `banned` = 0 "))) {
    checkBlockIP("API", 15);
    exit(json_encode(["status" => "error", "msg" => __("API Key không hợp lệ")]));
}
$user = [];
$data = [];
$user = ["username" => $getUser["username"], "money" => $getUser["money"]];
$data = ["status" => "success", "msg" => "Lấy dữ liệu thành công!", "data" => $user];
echo json_encode($data, JSON_PRETTY_PRINT);

?>