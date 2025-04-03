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
if(!isSecureCookie("admin_login")) {
    redirect(base_url("client/logout"));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '" . check_string($_COOKIE["admin_login"]) . "' AND `admin` != 0  ");
    if(!$getUser) {
        redirect(base_url("client/logout"));
    }
    if($CMSNT->site("status_only_device_admin") == 1 && $getUser["device"] != $Mobile_Detect->getUserAgent()) {
        redirect(base_url("client/logout"));
    }
    if($getUser["banned"] != 0) {
        redirect(base_url("common/banned"));
    }
    if($getUser["admin"] <= 0) {
        checkBlockIP("ADMIN", 5);
        redirect(base_url("client/logout"));
    }
    if($getUser["money"] < -500) {
        $User = new users();
        $User->Banned($getUser["id"], "Tài khoản âm tiền, ghi vấn bug");
        redirect(base_url("common/banned"));
    }
    if($CMSNT->site("status_only_ip_login_admin") == 1 && $getUser["ip"] != myip()) {
        redirect(base_url("client/logout"));
    }
    $CMSNT->update("users", ["time_session" => time()], " `id` = '" . $getUser["id"] . "' ");
    log_admin_request();
}

?>