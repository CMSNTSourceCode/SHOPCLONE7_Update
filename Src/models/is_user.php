<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
if(!defined("IN_SITE")) {
    exit("The Request Not Found");
}
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
if(!isSecureCookie("user_login")) {
    redirect(base_url("client/logout"));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '" . check_string($_COOKIE["user_login"]) . "'  ");
    if(!$getUser) {
        redirect(base_url("client/logout"));
    }
    if($CMSNT->site("status_only_device_client") == 1 && $getUser["device"] != $Mobile_Detect->getUserAgent()) {
        redirect(base_url("client/logout"));
    }
    if($getUser["banned"] != 0) {
        redirect(base_url("common/banned"));
    }
    if($getUser["money"] < -500) {
        $User = new users();
        $User->Banned($getUser["id"], "Tài khoản âm tiền, ghi vấn bug");
        redirect(base_url("common/banned"));
    }
    if(!empty($getUser["token_forgot_password"])) {
        $CMSNT->update("users", ["token_forgot_password" => NULL], " `id` = '" . $getUser["id"] . "' ");
    }
    $CMSNT->update("users", ["time_session" => time()], " `id` = '" . $getUser["id"] . "' ");
    if(function_exists("apcu_exists") && function_exists("apcu_store")) {
        $cache_key = "delete_log_" . $getUser["id"] . "_" . date("Y-m-d");
        if(!apcu_exists($cache_key)) {
            $count_before = $CMSNT->get_row("SELECT COUNT(id) as total FROM dongtien WHERE user_id = " . $getUser["id"])["total"];
            $CMSNT->query("DELETE\n                FROM dongtien\n                WHERE user_id = " . $getUser["id"] . "\n                AND id NOT IN (\n                    SELECT id\n                    FROM (\n                        SELECT id\n                        FROM dongtien\n                        WHERE user_id = " . $getUser["id"] . "\n                        ORDER BY thoigian DESC\n                        LIMIT 10000\n                    ) AS t\n                ) ");
            $count_after = $CMSNT->get_row("SELECT COUNT(id) as total FROM dongtien WHERE user_id = " . $getUser["id"])["total"];
            $deleted_rows = $count_before - $count_after;
            if(0 < $deleted_rows) {
                $CMSNT->insert("logs", ["user_id" => 0, "action" => "Tự động xóa " . $deleted_rows . " log dòng tiền cũ của user ID " . $getUser["id"] . " username " . $getUser["username"], "createdate" => gettime(), "ip" => myip(), "device" => $Mobile_Detect->getUserAgent()]);
            }
            apcu_store($cache_key, true, 86400);
        }
    }
}

?>