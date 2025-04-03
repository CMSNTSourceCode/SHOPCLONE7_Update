<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
echo "<!-- Developer By CMSNT.CO | FB.COM/CMSNT.CO | ZALO.ME/0947838128 | MMO Solution -->\n";
define("IN_SITE", true);
require_once __DIR__ . "/libs/db.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/libs/lang.php";
require_once __DIR__ . "/libs/helper.php";
require_once __DIR__ . "/libs/database/users.php";
$CMSNT = new DB();
if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
    require_once __DIR__ . "/views/common/maintenance.php";
    exit;
}
define("VIEWS_PATH", __DIR__ . "/views");
$module = !empty($_GET["module"]) ? check_path($_GET["module"]) : "client";
$home = $module == "client" ? $CMSNT->site("home_page") : "home";
$action = !empty($_GET["action"]) ? check_path($_GET["action"]) : $home;
$blocked_actions = ["footer", "header", "sidebar", "nav", "widget-tools"];
if(in_array($action, $blocked_actions)) {
    require_once VIEWS_PATH . "/common/404.php";
    exit;
}
if($module == "admin" && !isSecureCookie("admin_login")) {
    redirect("client/login");
}
if($module == "admin") {
    require_once __DIR__ . "/models/is_admin.php";
}
if(isset($_GET["utm_source"])) {
    $utm_source = check_string($_GET["utm_source"]);
    setcookie("utm_source", $utm_source, time() + 2592000, "/");
}
if(isset($_GET["aff"])) {
    $aff = check_string((int) $_GET["aff"]);
    setcookie("aff", $aff, time() + 2592000, "/");
    if($user_ref = $CMSNT->get_row("SELECT id FROM `users` WHERE `id` = " . $aff . " ")) {
        $CMSNT->cong("users", "ref_click", 1, " `id` = '" . $user_ref["id"] . "' ");
    }
}
$path = VIEWS_PATH . "/" . $module . "/" . $action . ".php";
if(file_exists($path) && strpos(realpath($path), realpath(VIEWS_PATH)) === 0) {
    require_once $path;
    exit;
}
require_once VIEWS_PATH . "/common/404.php";
exit;

?>