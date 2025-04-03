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
$body = ["title" => __("Profile") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n<link rel=\"stylesheet\" href=\"" . BASE_URL("public/client/") . "css/wallet.css\">\n";
$body["footer"] = "\n\n";
require_once __DIR__ . "/../../models/is_user.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/nav.php";
echo "\n<section class=\"py-5 inner-section profile-part\">\n    <div class=\"container\">\n        <div class=\"row content-reverse\">\n            <div class=\"col-lg-3\">\n                ";
require_once __DIR__ . "/sidebar.php";
echo "            </div>\n            <div class=\"col-lg-9\">\n                <div class=\"row\">\n\n                    <div class=\"col-lg-12\">\n                        <div class=\"account-card\">\n                            <h4 class=\"account-title\">";
echo __("Ví của tôi");
echo "</h4>\n                            <div class=\"my-wallet\">\n                                <p>";
echo __("Số dư hiện tại");
echo "</p>\n                                <h3>";
echo format_currency($getUser["money"]);
echo "</h3>\n                            </div>\n                            <div class=\"wallet-card-group\">\n                                <div class=\"wallet-card\">\n                                    <p>";
echo __("Tổng tiền nạp");
echo "</p>\n                                    <h3>";
echo format_currency($getUser["total_money"]);
echo "</h3>\n                                </div>\n                                <div class=\"wallet-card\">\n                                    <p>";
echo __("Số dư đã sử dụng");
echo "</p>\n                                    <h3>";
echo format_currency($getUser["total_money"] - $getUser["money"]);
echo "</h3>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col-lg-12\">\n                    <div class=\"account-card\">\n                        <div class=\"account-title\">\n                            <h4>";
echo __("Hồ sơ của bạn");
echo "</h4>\n                            <button data-bs-toggle=\"modal\"\n                                data-bs-target=\"#profile-edit\">";
echo __("Chỉnh sửa thông tin");
echo "</button>\n                        </div>\n                        <div class=\"account-content\">\n                            <div class=\"row\">\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\">\n                                        <label class=\"form-label\">";
echo __("Tên đăng nhập");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["username"];
echo "\"\n                                            readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\">\n                                        <label class=\"form-label\">";
echo __("Địa chỉ Email");
echo "</label>\n                                        <input type=\"email\" class=\"form-control\" placeholder=\"Enter your email..\"\n                                            value=\"";
echo $getUser["email"];
echo "\" readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\"><label class=\"form-label\">";
echo __("Số điện thoại");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["phone"];
echo "\"\n                                            readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\"><label class=\"form-label\">";
echo __("Họ và Tên");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["fullname"];
echo "\"\n                                            readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\"><label\n                                            class=\"form-label\">";
echo __("Telegram Chat ID");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\"\n                                            value=\"";
echo $getUser["telegram_chat_id"];
echo "\" readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\"><label class=\"form-label\">";
echo __("Thiết bị");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["device"];
echo "\"\n                                            readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\"><label\n                                            class=\"form-label\">";
echo __("Đăng ký vào lúc");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["create_date"];
echo "\"\n                                            readonly>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6 col-lg-4\">\n                                    <div class=\"form-group\"><label\n                                            class=\"form-label\">";
echo __("Đăng nhập gần nhất");
echo "</label>\n                                        <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["update_date"];
echo "\"\n                                            readonly>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</section>\n\n<div class=\"modal fade\" id=\"profile-edit\">\n    <div class=\"modal-dialog modal-dialog-centered\">\n        <div class=\"modal-content\"><button class=\"modal-close\" data-bs-dismiss=\"modal\"><i\n                    class=\"icofont-close\"></i></button>\n            <form class=\"modal-form\">\n                <div class=\"form-title\">\n                    <h3>";
echo __("Chỉnh sửa thông tin");
echo "</h3>\n                </div>\n                <div class=\"form-group\"><label class=\"form-label\">";
echo __("Số điện thoại");
echo "</label>\n                    <input type=\"hidden\" class=\"form-control\" value=\"";
echo $getUser["token"];
echo "\" id=\"token\">\n                    <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["phone"];
echo "\" id=\"phone\">\n                </div>\n                <div class=\"form-group\"><label class=\"form-label\">";
echo __("Họ và Tên");
echo "</label>\n                    <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["fullname"];
echo "\" id=\"fullname\">\n                </div>\n                <div class=\"form-group\"><label class=\"form-label\">";
echo __("Telegram Chat ID");
echo "</label>\n                    <input type=\"text\" class=\"form-control\" value=\"";
echo $getUser["telegram_chat_id"];
echo "\"\n                        id=\"telegram_chat_id\">\n                </div>\n                <button class=\"form-btn\" id=\"btnSaveProfile\" type=\"button\">";
echo __("Lưu");
echo "</button>\n            </form>\n        </div>\n    </div>\n</div>\n<script type=\"text/javascript\">\n\$(\"#btnSaveProfile\").on(\"click\", function() {\n    \$('#btnSaveProfile').html('<i class=\"fa fa-spinner fa-spin\"></i> ";
echo __("Processing...");
echo "')\n        .prop('disabled',\n            true);\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/client/auth.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'ChangeProfile',\n            token: \$(\"#token\").val(),\n            phone: \$(\"#phone\").val(),\n            fullname: \$(\"#fullname\").val(),\n            telegram_chat_id: \$(\"#telegram_chat_id\").val()\n        },\n        success: function(respone) {\n            if (respone.status == 'success') {\n                Swal.fire('";
echo __("Successful!");
echo "', respone.msg, 'success');\n            } else {\n                Swal.fire('";
echo __("Failure!");
echo "', respone.msg, 'error');\n            }\n            \$('#btnSaveProfile').html(\n                '";
echo __("Lưu");
echo "'\n            ).prop('disabled',\n                false);\n        },\n        error: function() {\n            showMessage('";
echo __("Không thể xử lý");
echo "', 'error');\n            \$('#btnSaveProfile').html(\n                '";
echo __("Lưu");
echo "'\n            ).prop('disabled',\n                false);\n        }\n\n    });\n});\n</script>\n\n\n\n\n";
require_once __DIR__ . "/footer.php";

?>