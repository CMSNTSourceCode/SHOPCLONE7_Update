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
$body = ["title" => __("Random Face") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n\n";
$body["footer"] = "\n\n";
if(isSecureCookie("user_login")) {
    require_once __DIR__ . "/../../models/is_user.php";
}
if($CMSNT->site("status_menu_tools") != 1) {
    redirect(base_url("client/home"));
}
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/nav.php";
echo "\n\n<section class=\"py-5 inner-section profile-part\">\n    <div class=\"container\">\n        <div class=\"row\">\n            <div class=\"col-12\">\n                <div class=\"posterd home mb-3\" style=\"background-image: url(";
echo base_url("mod/img/bg-intro.webp");
echo ")\">\n                    <div class=\"welcomto\">\n                        <div class=\"box-intro\">\n                            <img src=\"";
echo base_url("mod/img/icon-random-face.webp");
echo "\" alt=\"Accnice\" width=\"70\"\n                                height=\"70\">\n                        </div>\n                        <div class=\"\">\n                            <div\n                                style=\"font-size: 15px; text-shadow: rgba(0, 0, 0, 0.25) 0px 3px 5px;font-family: Robot,Roboto,sans-serif;\">\n                                ";
echo __("Bạn đang xem");
echo "</div>\n                            <h1\n                                style=\"color: #fff; font-size: 25px; font-weight:500; margin-top: 10px; text-shadow: rgba(0, 0, 0, 0.25) 0px 3px 5px;font-family: Robot,Roboto,sans-serif;\">\n                                ";
echo __("Tool Random Face");
echo "</h1>\n                        </div>\n                    </div>\n                </div>\n            </div>\n            ";
require_once __DIR__ . "/widget_tools.php";
echo "            <div class=\"mb-5\"></div>\n\n            <div class=\"col-12 text-center\">\n                <button type=\"button\" class=\"btn btn-primary fw-semibold px-3 py-2 mb-3\" onclick=\"tai_lai_trang()\"\n                    cursorshover=\"true\"><i class=\"fa-solid fa-rotate-right\"></i> CHANGE FACE </button>\n            </div>\n            <div class=\"col-12 text-center\">\n\n                <iframe id=\"iframe\" src=\"https://thispersondoesnotexist.com\" width=\"100%\" style=\"height:1000px\"\n                    frameborder=\"0\" scrolling=\"auto\">\n                </iframe>\n\n            </div>\n        </div>\n    </div>\n</section>\n\n\n\n";
require_once __DIR__ . "/footer.php";
echo "<script>\n// Tải lại trang\nfunction tai_lai_trang() {\n    location.reload();\n}\n</script>";

?>