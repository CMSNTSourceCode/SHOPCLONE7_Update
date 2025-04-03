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
setcookie("login", "", time() - 3600, "/");
setcookie("admin_login", "", time() - 3600, "/");
setcookie("remember_token", "", time() - 3600, "/");
setcookie("user_login", "", time() - 3600, "/");
setcookie("user_agent", "", time() - 3600, "/");
session_unset();
session_destroy();
redirect(base_url("client/login"));

?>