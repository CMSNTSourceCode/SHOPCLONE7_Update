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
$body = ["title" => __("Bảo mật tài khoản") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n<link rel=\"stylesheet\" href=\"" . BASE_URL("public/client/") . "css/profile.css\">\n";
$body["footer"] = "\n\n";
require_once __DIR__ . "/../../models/is_user.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/nav.php";
echo "\n<section class=\"py-5 inner-section profile-part\">\n    <div class=\"container\">\n        <div class=\"row content-reverse\">\n            <div class=\"col-lg-3\">\n                ";
require_once __DIR__ . "/sidebar.php";
echo "            </div>\n            <div class=\"col-lg-9\">\n                <div class=\"account-card\">\n                    <div class=\"account-title\">\n                        <h4>";
echo __("Bảo mật tài khoản");
echo "</h4>\n                    </div>\n                    <div class=\"account-content\">\n                        <div class=\"row\">\n                            <div class=\"col-md-12 col-lg-12\">\n                                <div class=\"form-group row\">\n                                    <div class=\"col-xl-6\">\n                                        <label class=\"form-label\">";
echo __("Xác minh đăng nhập bằng");
echo "                                            <b>OTP Mail:</b></label>\n                                    </div>\n                                    <div class=\"col-xl-6\">\n                                        <input type=\"hidden\" id=\"token\" value=\"";
echo $getUser["token"];
echo "\">\n                                        <label class=\"switch\">\n                                            <input type=\"checkbox\" value=\"1\" id=\"status_otp_mail\"\n                                                ";
echo $getUser["status_otp_mail"] == 1 ? "checked" : "";
echo ">\n                                            <span class=\"slider\"></span>\n                                        </label>\n                                    </div>\n                                </div>\n                            </div>\n\n                            <div class=\"col-md-12 col-lg-12\">\n                                <div class=\"form-group row\">\n                                    <div class=\"col-xl-6\">\n                                        <label\n                                            class=\"form-label\">";
echo __("Gửi thông báo về mail khi đăng nhập thành công:");
echo "</label>\n                                    </div>\n                                    <div class=\"col-xl-6\">\n                                        <label class=\"switch\">\n                                            <input type=\"checkbox\" value=\"1\" id=\"status_noti_login_to_mail\"\n                                                ";
echo $getUser["status_noti_login_to_mail"] == 1 ? "checked" : "";
echo ">\n                                            <span class=\"slider\"></span>\n                                        </label>\n                                    </div>\n                                </div>\n                            </div>\n\n                            <div class=\"col-md-12 col-lg-12\">\n                                <div class=\"form-group row\">\n                                    <div class=\"col-xl-6\">\n                                        <label\n                                            class=\"form-label\">";
echo __("Đúng Trình Duyệt và IP mua hàng mới có thể xem đơn hàng:");
echo "</label>\n                                    </div>\n                                    <div class=\"col-xl-6\">\n                                        <label class=\"switch\">\n                                            <input type=\"checkbox\" value=\"1\" id=\"status_view_order\"\n                                                ";
echo $getUser["status_view_order"] == 1 ? "checked" : "";
echo ">\n                                            <span class=\"slider\"></span>\n                                        </label>\n                                    </div>\n                                </div>\n                            </div>\n\n                            <center>\n                                <button class=\"form-btn\" id=\"btnChangeSecurity\"\n                                    type=\"button\">";
echo __("Cập Nhật");
echo "</button>\n                            </center>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"account-card py-4\">\n\n                    <div class=\"account-content\">\n                        <div class=\"row\">\n                            <div class=\"col-md-12 col-lg-12\">\n                                <div class=\"form-group row\">\n                                    <div class=\"col-xl-6\">\n                                        <label class=\"form-label\">";
echo __("Xác minh đăng nhập bằng");
echo " <b>Google\n                                                Authenticator</b>:</label>\n                                    </div>\n                                    <div class=\"col-xl-6\">\n                                        <label class=\"switch\">\n                                            <input type=\"checkbox\" value=\"1\" id=\"status_2fa\"\n                                                ";
echo $getUser["status_2fa"] == 1 ? "checked" : "";
echo ">\n                                            <span class=\"slider\"></span>\n                                        </label>\n                                        <div id=\"qr_2fa\" style=\"display:none;\">\n                                            ";
$google2fa = new PragmaRX\Google2FAQRCode\Google2FA();
$qrCodeUrl = $google2fa->getQRCodeInline($CMSNT->site("title"), $getUser["email"], $getUser["SecretKey_2fa"]);
echo "                                            ";
echo $qrCodeUrl;
echo "<br>\n                                            \n                                            <input placeholder=\"";
echo __("Nhập mã xác minh để lưu");
echo "\" class=\"input-style\"\n                                                id=\"secret\" type=\"text\">\n                                            <button class=\"btn-save\" id=\"btnSave2FA\">\n                                                <span><i class=\"fa-solid fa-floppy-disk\"></i>\n                                                    ";
echo __("Lưu");
echo "</span>\n                                            </button>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n                            <small>";
echo __("- Sử dụng điện thoại tải App Google Authenticator sau đó quét mã QR để nhận mã xác minh.");
echo "<br>\n                            ";
echo __("- Mã QR sẽ được thay đổi khi bạn tắt xác minh.");
echo "<br>\n                        ";
echo __("- Nếu bật Xác minh đăng nhập bằng OTP Mail thì không bật Google Authenticator và ngược lại.");
echo "</small>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</section>\n\n<script>\n\$(document).ready(function() {\n    \$(\"#status_2fa\").change(function() {\n        var qrElement = \$(\"#qr_2fa\");\n        var toggled = qrElement.data('toggled');\n        \n        if (!toggled) {\n            qrElement.show();\n            qrElement.data('toggled', true);\n        } else {\n            qrElement.hide();\n            qrElement.data('toggled', false);\n        }\n    });\n});\n\n</script>\n<script type=\"text/javascript\">\n\$(\"#btnSave2FA\").on(\"click\", function() {\n    \$('#btnSave2FA').html('<span><i class=\"fa fa-spinner fa-spin\"></i> ";
echo __("Processing...");
echo "</span>')\n        .prop('disabled',\n            true);\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/client/auth.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'Save2FA',\n            token: \$(\"#token\").val(),\n            status_2fa: \$(\"#status_2fa\").is(\":checked\") ? 1 : 0,\n            secret: \$(\"#secret\").val()\n        },\n        success: function(respone) {\n            if (respone.status == 'success') {\n                Swal.fire('";
echo __("Successful!");
echo "', respone.msg, 'success');\n            } else {\n                Swal.fire('";
echo __("Failure!");
echo "', respone.msg, 'error');\n            }\n            \$('#btnSave2FA').html(\n                '<span><i class=\"fa-solid fa-floppy-disk\"></i> ";
echo __("Lưu");
echo "</span>'\n            ).prop('disabled',\n                false);\n        },\n        error: function() {\n            showMessage('";
echo __("Không thể xử lý");
echo "', 'error');\n            \$('#btnSave2FA').html(\n                '<span><i class=\"fa-solid fa-floppy-disk\"></i> ";
echo __("Lưu");
echo "</span>'\n            ).prop('disabled',\n                false);\n        }\n\n    });\n});\n</script>\n\n<script type=\"text/javascript\">\n\$(\"#btnChangeSecurity\").on(\"click\", function() {\n    \$('#btnChangeSecurity').html('<span><i class=\"fa fa-spinner fa-spin\"></i> ";
echo __("Processing...");
echo "</span>')\n        .prop('disabled',\n            true);\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/client/auth.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'changeSecurity',\n            token: \$(\"#token\").val(),\n            status_noti_login_to_mail: \$(\"#status_noti_login_to_mail\").is(\":checked\") ? 1 : 0,\n            status_otp_mail: \$(\"#status_otp_mail\").is(\":checked\") ? 1 : 0,\n            status_view_order: \$(\"#status_view_order\").is(\":checked\") ? 1 : 0\n        },\n        success: function(respone) {\n            if (respone.status == 'success') {\n                Swal.fire('";
echo __("Successful!");
echo "', respone.msg, 'success');\n            } else {\n                Swal.fire('";
echo __("Failure!");
echo "', respone.msg, 'error');\n            }\n            \$('#btnChangeSecurity').html(\n                '";
echo __("Cập nhật");
echo "'\n            ).prop('disabled',\n                false);\n        },\n        error: function() {\n            showMessage('";
echo __("Không thể xử lý");
echo "', 'error');\n            \$('#btnChangeSecurity').html(\n                '";
echo __("Cập nhật");
echo "'\n            ).prop('disabled',\n                false);\n        }\n\n    });\n});\n</script>\n\n\n";
require_once __DIR__ . "/footer.php";

?>