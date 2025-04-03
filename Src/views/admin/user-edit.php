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
$body = ["title" => __("Edit Member"), "desc" => "CMSNT Panel", "keyword" => "cmsnt, CMSNT, cmsnt.co,"];
$body["header"] = "\n\n";
$body["footer"] = "\n\n";
require_once __DIR__ . "/../../models/is_admin.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";
require_once __DIR__ . "/nav.php";
if(!checkPermission($getUser["admin"], "edit_user")) {
    exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back();}</script>");
}
if(isset($_GET["id"])) {
    $CMSNT = new DB();
    $id = check_string($_GET["id"]);
    $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '" . $id . "' ");
    if(!$user) {
        exit("ID user không tồn tại trong hệ thống");
    }
    if($getUser["admin"] != 99999 && $user["admin"] == 99999) {
        exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back();}</script>");
    }
    if(isset($_POST["email"])) {
        $Mobile_Detect = new Mobile_Detect();
        if($CMSNT->site("status_demo") != 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"Không được dùng chức năng này vì đây là trang web demo.\")){window.history.back().location.reload();}</script>");
        }
        if(100 < check_string($_POST["discount"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Chiết khấu giảm giá không được lớn hơn 100\")){window.history.back().location.reload();}</script>");
        }
        if(check_string($_POST["admin"]) != $user["admin"]) {
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Thay đổi quyền Admin cho thành viên " . $user["username"] . "[" . $user["id"] . "] từ " . $user["admin"] . " -> " . check_string($_POST["admin"]) . "."]);
            $CMSNT->insert("logs", ["user_id" => $user["id"], "createdate" => gettime(), "action" => "Bạn được Admin " . $getUser["username"] . " thay đổi quyền Admin."]);
        }
        if(check_string($_POST["ctv"]) != $user["ctv"]) {
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Thay đổi quyền CTV cho thành viên " . $user["username"] . "[" . $user["id"] . "] từ " . $user["ctv"] . " -> " . check_string($_POST["ctv"]) . "."]);
            $CMSNT->insert("logs", ["user_id" => $user["id"], "createdate" => gettime(), "action" => "Bạn được Admin " . $getUser["username"] . " thay đổi quyền CTV."]);
        }
        if($_POST["discount"] != $user["discount"]) {
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Thay đổi chiết khấu thành viên " . $user["username"] . " từ " . $user["discount"] . "% -> " . check_string($_POST["discount"]) . "%."]);
            $CMSNT->insert("logs", ["user_id" => $user["id"], "createdate" => gettime(), "action" => "Bạn được Admin " . $getUser["username"] . " thay đổi chiết khấu."]);
        }
        if($_POST["username"] != $user["username"]) {
            if($CMSNT->get_row(" SELECT * FROM `users` WHERE `username` = '" . check_string($_POST["username"]) . "' AND `id` != '" . $user["id"] . "' ")) {
                exit("<script type=\"text/javascript\">if(!alert(\"Tên đăng nhập này đã có người sử dụng\")){window.history.back().location.reload();}</script>");
            }
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Change Username " . $user["username"] . "[" . $user["id"] . "] | " . $user["username"] . " -> " . check_string($_POST["username"]) . "."]);
            $CMSNT->insert("logs", ["user_id" => $user["id"], "createdate" => gettime(), "action" => __("Bạn được Admin thay đổi Username.")]);
        }
        if($_POST["email"] != $user["email"]) {
            if($CMSNT->get_row(" SELECT * FROM `users` WHERE `email` = '" . check_string($_POST["email"]) . "' AND `id` != '" . $user["id"] . "' ")) {
                exit("<script type=\"text/javascript\">if(!alert(\"Địa chỉ Email này đã có người sử dụng\")){window.history.back().location.reload();}</script>");
            }
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Change Email " . $user["username"] . "[" . $user["id"] . "] | " . $user["email"] . " -> " . check_string($_POST["email"]) . "."]);
            $CMSNT->insert("logs", ["user_id" => $user["id"], "createdate" => gettime(), "action" => __("Bạn được Admin thay đổi Email.")]);
        }
        if($_POST["admin"] == 99999 && $user["admin"] != 99999) {
            exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back().location.reload();}</script>");
        }
        $DBUser = new users();
        $isUpdate = $DBUser->update_by_id(["username" => check_string($_POST["username"]), "email" => check_string($_POST["email"]), "status_2fa" => check_string($_POST["status_2fa"]), "token" => check_string($_POST["token"]), "api_key" => check_string($_POST["api_key"]), "phone" => check_string($_POST["phone"]), "gender" => check_string($_POST["gender"]), "admin" => check_string($_POST["admin"]), "ctv" => check_string($_POST["ctv"]), "discount" => check_string($_POST["discount"]), "banned" => check_string($_POST["banned"]), "ref_id" => check_string($_POST["ref_id"])], $user["id"]);
        if($isUpdate) {
            if(!empty($_POST["password"])) {
                $DBUser->update_by_id(["password" => TypePassword(check_string($_POST["password"]))], $user["id"]);
            }
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Cập nhật thông tin thành viên " . $user["username"] . "[" . $user["id"] . "]."]);
            $CMSNT->insert("logs", ["user_id" => $user["id"], "createdate" => gettime(), "action" => __("You are changed information by Admin.")]);
            $my_text = $CMSNT->site("noti_action");
            $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
            $my_text = str_replace("{username}", $getUser["username"], $my_text);
            $my_text = str_replace("{action}", "Cập nhật thông tin thành viên " . $user["username"] . "[" . $user["id"] . "]", $my_text);
            $my_text = str_replace("{ip}", myip(), $my_text);
            $my_text = str_replace("{time}", gettime(), $my_text);
            sendMessAdmin($my_text);
            exit("<script type=\"text/javascript\">if(!alert(\"" . __("Cập nhật thông tin thành công!") . "\")){window.history.back().location.reload();}</script>");
        }
    }
    if(isset($_POST["cong_tien"])) {
        if($CMSNT->site("status_demo") != 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"Không được dùng chức năng này vì đây là trang web demo.\")){window.history.back().location.reload();}</script>");
        }
        if($_POST["amount"] <= 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"Amount không hợp lệ !\")){window.history.back().location.reload();}</script>");
        }
        $Mobile_Detect = new Mobile_Detect();
        $amount = check_string($_POST["amount"]);
        $reason = "[VÍ CHÍNH] " . check_string($_POST["reason"]);
        if($_POST["wallet"] == 2) {
            $CMSNT->cong("users", "debit", $amount, " `id` = '" . $id . "' ");
            $reason = "[VÍ GHI NỢ] " . check_string($_POST["reason"]);
        }
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Cộng " . format_currency($amount) . " cho User " . $user["username"] . "[" . $user["id"] . "] lý do (" . $reason . ")."]);
        $DBUser = new users();
        $DBUser->AddCredits($id, $amount, $reason, "admin_add_" . uniqid());
        $my_text = $CMSNT->site("noti_action");
        $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
        $my_text = str_replace("{username}", $getUser["username"], $my_text);
        $my_text = str_replace("{action}", "[Admin] Cộng " . format_currency($amount) . " cho User " . $user["username"] . "[" . $user["id"] . "] lý do (" . $reason . ").", $my_text);
        $my_text = str_replace("{ip}", myip(), $my_text);
        $my_text = str_replace("{time}", gettime(), $my_text);
        sendMessAdmin($my_text);
        admin_msg_success("Cộng số dư thành công!", "", 1000);
    }
    if(isset($_POST["tru_tien"])) {
        if($CMSNT->site("status_demo") != 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"Không được dùng chức năng này vì đây là trang web demo.\")){window.history.back().location.reload();}</script>");
        }
        if($_POST["amount"] <= 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"Amount không hợp lệ !\")){window.history.back().location.reload();}</script>");
        }
        $Mobile_Detect = new Mobile_Detect();
        $amount = check_string($_POST["amount"]);
        if($_POST["wallet"] == 2) {
            $CMSNT->tru("users", "debit", $amount, " `id` = '" . $id . "' ");
            $reason = "[VÍ GHI NỢ] " . check_string($_POST["reason"]);
        } else {
            if(getRowRealtime("users", $id, "money") < $amount) {
                exit("<script type=\"text/javascript\">if(!alert(\"Số dư bạn trừ vượt quá số dư khả dụng của thành viên\")){window.history.back().location.reload();}</script>");
            }
            $reason = "[VÍ CHÍNH] " . check_string($_POST["reason"]);
            $DBUser = new users();
            $DBUser->RemoveCredits($id, $amount, $reason, "admin_remove_" . uniqid());
        }
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "createdate" => gettime(), "device" => $Mobile_Detect->getUserAgent(), "ip" => myip(), "action" => "[Admin] Trừ " . format_currency($amount) . " cho User " . $user["username"] . "[" . $user["id"] . "] lý do (" . $reason . ")."]);
        $my_text = $CMSNT->site("noti_action");
        $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
        $my_text = str_replace("{username}", $getUser["username"], $my_text);
        $my_text = str_replace("{action}", "[Admin] Trừ " . format_currency($amount) . " cho User " . $user["username"] . "[" . $user["id"] . "] lý do (" . $reason . ").", $my_text);
        $my_text = str_replace("{ip}", myip(), $my_text);
        $my_text = str_replace("{time}", gettime(), $my_text);
        sendMessAdmin($my_text);
        admin_msg_success("Trừ số dư thành công!", "", 1000);
    }
}
echo "\n\n<div class=\"main-content app-content\">\n    <div class=\"container-fluid\">\n        <div class=\"d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb\">\n            <h1 class=\"page-title fw-semibold fs-18 mb-0\"><a type=\"button\"\n                    class=\"btn btn-dark btn-raised-shadow btn-wave btn-sm me-1\" href=\"";
echo base_url_admin("users");
echo "\"><i\n                        class=\"fa-solid fa-arrow-left\"></i></a> Chỉnh sửa thành viên ";
echo $user["username"];
echo "</h1>\n        </div>\n        <div class=\"row gx-5\">\n            <div class=\"col-12\">\n                <div class=\"mt-4 mt-md-0\">\n                    <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#modal-addCredit\"\n                        class=\"btn btn-sm btn-wave btn-success me-1 mb-3 push\">\n                        <i class=\"fa fa-fw fa-plus\"></i> Cộng số dư\n                    </button>\n                    <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#modal-removeCredit\"\n                        class=\"btn btn-sm btn-wave btn-danger me-1 mb-3 push\">\n                        <i class=\"fa fa-fw fa-minus\"></i> Trừ số dư\n                    </button>\n                    <a type=\"button\" href=\"";
echo base_url_admin("logs&user_id=" . $user["id"]);
echo "\" target=\"_blank\"\n                        class=\"btn btn-sm btn-wave btn-primary me-1 mb-3 push\">\n                        <i class=\"fa fa-fw fa-history\"></i> Nhật ký hoạt động\n                    </a>\n                    <a type=\"button\" href=\"";
echo base_url_admin("transactions&user_id=" . $user["id"]);
echo "\" target=\"_blank\"\n                        class=\"btn btn-sm btn-wave btn-info me-1 mb-3 push\">\n                        <i class=\"fa fa-fw fa-history\"></i> Biến động số dư\n                    </a>\n                </div>\n            </div>\n            <div class=\"col-12\">\n                <div class=\"card custom-card shadow-none mb-4\">\n                    <div class=\"card-body\">\n                        <form action=\"\" method=\"POST\">\n                            <div class=\"row\">\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Username (<span class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-user\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["username"];
echo "\"\n                                                name=\"username\" required>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Email (<span class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-envelope\"></i>\n                                            </span>\n                                            <input type=\"email\" class=\"form-control\" value=\"";
echo $user["email"];
echo "\"\n                                                name=\"email\" required>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class=\"row\">\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Token (<span class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-key\"></i>\n                                            </span>\n                                            <input type=\"password\" class=\"form-control\" id=\"token_input\"\n                                                value=\"";
echo $user["token"];
echo "\" name=\"token\" required>\n                                            <button type=\"button\" id=\"show_token\" class=\"btn btn-danger\"\n                                                onclick=\"toggleTokenVisibility()\">Show</button>\n                                        </div>\n                                        <script>\n                                        function toggleTokenVisibility() {\n                                            var input = document.getElementById('token_input');\n                                            var button = document.getElementById('show_token');\n                                            if (input.type === 'password') {\n                                                input.type = 'text';\n                                                button.textContent = 'Hide';\n                                            } else {\n                                                input.type = 'password';\n                                                button.textContent = 'Show';\n                                            }\n                                        }\n                                        </script>\n                                        <small>Bảo mật thông tin này vì kẻ xấu có thể thực hiện đăng nhập tài khoản bằng\n                                            Token</small>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">API Key (<span class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-key\"></i>\n                                            </span>\n                                            <input type=\"password\" class=\"form-control\" value=\"";
echo $user["api_key"];
echo "\"\n                                                name=\"api_key\" id=\"api_key_input\" required>\n                                            <button type=\"button\" id=\"show_api_key\" class=\"btn btn-danger\"\n                                                onclick=\"toggleApiKeyVisibility()\">Show</button>\n                                        </div>\n                                        <smalli>Bảo mật thông tin này vì kẻ xấu có thể mua hàng thông qua API KEY\n                                        </smalli>\n                                    </div>\n                                    <script>\n                                    function toggleApiKeyVisibility() {\n                                        var input = document.getElementById('api_key_input');\n                                        var button = document.getElementById('show_api_key');\n                                        if (input.type === 'password') {\n                                            input.type = 'text';\n                                            button.textContent = 'Hide';\n                                        } else {\n                                            input.type = 'password';\n                                            button.textContent = 'Show';\n                                        }\n                                    }\n                                    </script>\n                                </div>\n                                <div class=\"col-md-4\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">";
echo __("Mật khẩu");
echo " (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-key\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" placeholder=\"**********\"\n                                                name=\"password\">\n                                        </div>\n                                        <small>";
echo __("Nhập mật khẩu cần thay đổi, hệ thống sẽ tự động mã hóa (bỏ trống nếu không muốn thay đổi)");
echo "</small>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-4\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Secret Key Google 2FA</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class='bx bx-key'></i>\n                                            </span>\n                                            <input type=\"password\" class=\"form-control\" id=\"key_2fa_input\"\n                                                value=\"";
echo $user["SecretKey_2fa"];
echo "\" disabled>\n                                            <button type=\"button\" id=\"show_key_2fa\" class=\"btn btn-danger\"\n                                                onclick=\"toggleKey2FAVisibility()\">Show</button>\n                                        </div>\n                                        <small>Lộ thông tin này có thể khiến kẻ xấu bỏ qua bước xác minh 2FA.</small>\n                                        <script>\n                                        function toggleKey2FAVisibility() {\n                                            var input = document.getElementById('key_2fa_input');\n                                            var button = document.getElementById('show_key_2fa');\n                                            if (input.type === 'password') {\n                                                input.type = 'text';\n                                                button.textContent = 'Hide';\n                                            } else {\n                                                input.type = 'password';\n                                                button.textContent = 'Show';\n                                            }\n                                        }\n                                        </script>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-4\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">ON/OFF Google 2FA (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class='bx bxs-key'></i>\n                                            </span>\n                                            <select class=\"form-control select2bs4\" name=\"status_2fa\">\n                                                <option ";
echo $user["status_2fa"] == 1 ? "selected" : "";
echo " value=\"1\">\n                                                    ON\n                                                </option>\n                                                <option ";
echo $user["status_2fa"] == 0 ? "selected" : "";
echo " value=\"0\">\n                                                    OFF</option>\n                                            </select>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-4\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">";
echo __("Phone");
echo " (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-phone\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["phone"];
echo "\"\n                                                name=\"phone\">\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-4\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">";
echo __("Giới tính");
echo " (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-venus-mars\"></i>\n                                            </span>\n                                            <select class=\"form-control select2bs4\" name=\"gender\">\n                                                <option ";
echo $user["gender"] == "Male" ? "selected" : "";
echo " value=\"Male\">\n                                                    Nam (Male)\n                                                </option>\n                                                <option ";
echo $user["gender"] == "Female" ? "selected" : "";
echo "                                                    value=\"Female\">\n                                                    Nữ (Female)</option>\n                                            </select>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-4\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">";
echo __("Chiết khấu giảm giá");
echo " (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-percent\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["discount"];
echo "\"\n                                                name=\"discount\">\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Cộng tác viên (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <select class=\"form-control select2bs4\" name=\"ctv\">\n                                            <option value=\"1\" ";
echo $user["ctv"] == 1 ? "selected" : "";
echo ">Có</option>\n                                            <option value=\"0\" ";
echo $user["ctv"] == 0 ? "selected" : "";
echo ">Không\n                                            </option>\n                                        </select>\n                                        <small>Chức năng này sắp ra mắt, chỉ SET cho người bạn muốn có quyền đăng bán\n                                            sản phẩm.</small>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Người giới thiệu</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-user\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" name=\"ref_id\" value=\"";
echo $user["ref_id"];
echo "\">\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Admin Role (<span\n                                                class=\"text-danger\">*</span>)</label>\n                                        <select class=\"form-control select2bs4\" name=\"admin\">\n                                            <option value=\"0\" ";
echo $user["admin"] == 0 ? "selected" : "";
echo ">User (Khách\n                                                hàng)\n                                            </option>\n                                            ";
foreach ($CMSNT->get_list(" SELECT * FROM `admin_role` ") as $role) {
    echo "                                            <option value=\"";
    echo $role["id"];
    echo "\"\n                                                ";
    echo $user["admin"] == $role["id"] ? "selected" : "";
    echo ">\n                                                ";
    echo $role["name"];
    echo " (Admin Role)\n                                            </option>\n                                            ";
}
echo "                                            ";
if($user["admin"] == 99999) {
    echo "                                            <option value=\"99999\" ";
    echo $user["admin"] == 99999 ? "selected" : "";
    echo ">\n                                                Administrator (Admin Root)</option>\n                                            ";
}
echo "                                        </select>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <div class=\"mb-4\">\n                                            <label class=\"form-label\">Banned (<span\n                                                    class=\"text-danger\">*</span>)</label>\n                                            <select class=\"form-control select2bs4\" name=\"banned\">\n                                                <option ";
echo $user["banned"] == 1 ? "selected" : "";
echo " value=\"1\">\n                                                    Banned</option>\n                                                <option ";
echo $user["banned"] == 0 ? "selected" : "";
echo " value=\"0\">Live\n                                                </option>\n                                            </select>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class=\"row\">\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Ví chính</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-wallet\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\"\n                                                value=\"";
echo format_currency($user["money"]);
echo "\" disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Tổng tiền nạp</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-money-bill\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\"\n                                                value=\"";
echo format_currency($user["total_money"]);
echo "\" disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Số dư đã sử dụng</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class='bx bxs-wallet-alt'></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\"\n                                                value=\"";
echo format_currency($user["total_money"] - $user["money"]);
echo "\"\n                                                disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-3\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Số tiền còn nợ hệ thống</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class='bx bxs-wallet-alt'></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\"\n                                                value=\"";
echo format_currency($user["debit"]);
echo "\" disabled> \n                                        </div>\n                                        <small>Số tiền nợ sẽ tự động trừ khi người dùng nạp tiền tự động.</small>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class=\"row\">\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Địa chỉ IP dùng để đăng nhập</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-wifi\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["ip"];
echo "\" disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Thiết bị đăng nhập</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-desktop\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["device"];
echo "\"\n                                                disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class=\"row mb-4\">\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Đăng ký tài khoản vào lúc</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-calendar-days\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["create_date"];
echo "\"\n                                                disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-md-6\">\n                                    <div class=\"mb-4\">\n                                        <label class=\"form-label\">Đăng nhập gần nhất vào lúc</label>\n                                        <div class=\"input-group\">\n                                            <span class=\"input-group-text\">\n                                                <i class=\"fa-solid fa-calendar-days\"></i>\n                                            </span>\n                                            <input type=\"text\" class=\"form-control\" value=\"";
echo $user["update_date"];
echo "\"\n                                                disabled>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n                            <a type=\"button\" class=\"btn btn-danger\" href=\"";
echo base_url_admin("users");
echo "\"><i\n                                    class=\"fa fa-fw fa-undo\"></i> ";
echo __("Back");
echo "</a>\n                            <button type=\"submit\" class=\"btn btn-primary\"><i class=\"bi bi-download\"></i>\n                                ";
echo __("Save");
echo "</button>\n                        </form>\n                    </div>\n                </div>\n            </div>\n             \n\n        </div>\n\n    </div>\n</div>\n<br>\n\n\n\n";
require_once __DIR__ . "/footer.php";
echo "\n<div class=\"modal fade\" id=\"modal-addCredit\" tabindex=\"-1\" aria-labelledby=\"modal-block-popout\" role=\"dialog\"\n    aria-hidden=\"true\">\n    <div class=\"modal-dialog modal-dialog-centered\">\n        <div class=\"modal-content\">\n            <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">\n                <div class=\"modal-header\">\n                    <h6 class=\"modal-title\" id=\"staticBackdropLabel2\"><i class=\"fa fa-plus\"></i> CỘNG SỐ DƯ\n                    </h6>\n                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n                </div>\n                <div class=\"modal-body\">\n                    <div id=\"notice_debit\"\n                        class=\"alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm\"\n                        style=\"display: none;\" role=\"alert\">\n                        Khi chọn <b>VÍ GHI NỢ</b>, số dư sẽ được cộng trước cho user trong trường hợp auto bank deplay,\n                        khi auto bank hoạt động trở lại, hệ thống sẽ tự động trừ lại số tiền đã cộng.\n                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"><i\n                                class=\"bi bi-x\"></i></button>\n                    </div>\n                    <div class=\"row mb-4\">\n                        <label class=\"col-sm-4 col-form-label\" for=\"example-hf-email\">Loại ví:</label>\n                        <div class=\"col-sm-8\">\n                            <select class=\"form-control\" name=\"wallet\" id=\"walletSelect\">\n                                <option value=\"1\">VÍ CHÍNH</option>\n                                <option value=\"2\">VÍ GHI NỢ</option>\n                            </select>\n                        </div>\n                    </div>\n\n                    <div class=\"row mb-4\">\n                        <label class=\"col-sm-4 col-form-label\" for=\"example-hf-email\">";
echo __("Amount:");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <input type=\"text\" class=\"form-control\" name=\"amount\" id=\"amountInput\"\n                                placeholder=\"";
echo __("Nhập số tiền cần cộng");
echo "\" required>\n                        </div>\n                    </div>\n                    <div class=\"row mb-4\">\n                        <label class=\"col-sm-4 col-form-label\"\n                            for=\"example-hf-email\">";
echo __("Lý do (nếu có):");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <textarea class=\"form-control\" name=\"reason\"></textarea>\n                        </div>\n                    </div>\n                    <!-- <div class=\"mb-4\">\n                        <div class=\"form-check\">\n                            <input class=\"form-check-input\" type=\"checkbox\" value=\"1\" id=\"cong_hoa_hong\" name=\"cong_hoa_hong\">\n                            <label class=\"form-check-label\" for=\"cong_hoa_hong\">\n                                Cộng hoa hồng cho người giới thiệu nếu có\n                            </label>\n                        </div>\n                    </div> -->\n                    <center>Nhấn vào nút Submit để thực hiện cộng <b id=\"amountDisplay\" style=\"color:red;\">0</b> vào <b\n                            id=\"walletDisplay\">VÍ CHÍNH</b></center>\n                </div>\n                <script>\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    var selectWallet = document.getElementById('walletSelect');\n                    var amountInput = document.getElementById('amountInput');\n                    var amountDisplay = document.getElementById('amountDisplay');\n                    var walletDisplay = document.getElementById('walletDisplay');\n                    var noticeDebit = document.getElementById('notice_debit');\n\n                    // Hiển thị giá trị mặc định cho số tiền và loại ví\n                    updateAmountDisplay();\n                    updateWalletDisplay();\n\n                    // Lắng nghe sự kiện input trên input số tiền\n                    amountInput.addEventListener('input', function() {\n                        updateAmountDisplay();\n                    });\n\n                    // Lắng nghe sự kiện change trên select box chọn loại ví\n                    selectWallet.addEventListener('change', function() {\n                        updateWalletDisplay();\n                        // Nếu chọn ví ghi nợ, hiển thị notice_debit\n                        if (selectWallet.value === \"2\") {\n                            noticeDebit.style.display = 'block';\n                        } else {\n                            noticeDebit.style.display = 'none';\n                        }\n                    });\n\n                    function updateAmountDisplay() {\n                        // Lấy giá trị từ input\n                        var inputValue = amountInput.value;\n\n                        // Kiểm tra nếu giá trị rỗng hoặc không phải là số\n                        if (!inputValue || isNaN(inputValue)) {\n                            amountDisplay.textContent =\n                                '0'; // Hiển thị 0 nếu không có giá trị hoặc giá trị không hợp lệ\n                            return;\n                        }\n\n                        // Định dạng số tiền và hiển thị vào amountDisplay\n                        var formattedAmount = formatNumber(inputValue);\n                        amountDisplay.textContent = formattedAmount;\n                    }\n\n                    function formatNumber(value) {\n                        return parseFloat(value).toLocaleString('vi-VN');\n                    }\n\n                    function updateWalletDisplay() {\n                        // Hiển thị loại ví được chọn\n                        walletDisplay.textContent = selectWallet.options[selectWallet.selectedIndex].text;\n                    }\n                });\n                </script>\n\n\n\n                <div class=\"modal-footer\">\n                    <button type=\"button\" class=\"btn btn-hero btn-danger\" data-bs-dismiss=\"modal\"><i\n                            class=\"fa fa-fw fa-times me-1\"></i> ";
echo __("Close");
echo "</button>\n                    <button type=\"submit\" name=\"cong_tien\" class=\"btn btn-hero btn-success\"><i\n                            class=\"fa fa-fw fa-plus me-1\"></i> ";
echo __("Submit");
echo "</button>\n                </div>\n            </form>\n        </div>\n    </div>\n</div>\n\n<div class=\"modal fade\" id=\"modal-removeCredit\" tabindex=\"-1\" aria-labelledby=\"modal-block-popout\" role=\"dialog\"\n    aria-hidden=\"true\">\n    <div class=\"modal-dialog modal-dialog-centered\">\n        <div class=\"modal-content\">\n            <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">\n                <div class=\"modal-header\">\n                    <h6 class=\"modal-title\" id=\"staticBackdropLabel2\"><i class=\"fa fa-minus\"></i> ";
echo __("Balance");
echo "                    </h6>\n                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n                </div>\n                <div class=\"modal-body\">\n                    <div class=\"row mb-4\">\n                        <label class=\"col-sm-4 col-form-label\" for=\"example-hf-email\">Loại ví:</label>\n                        <div class=\"col-sm-8\">\n                            <select class=\"form-control\" name=\"wallet\" id=\"walletSelect2\">\n                                <option value=\"1\">VÍ CHÍNH</option>\n                                <option value=\"2\">VÍ GHI NỢ</option>\n                            </select>\n                        </div>\n                    </div>\n                    <div class=\"row mb-4\">\n                        <label class=\"col-sm-4 col-form-label\" for=\"example-hf-email\">";
echo __("Amount");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <input type=\"text\" class=\"form-control\" name=\"amount\" id=\"amountInput2\"\n                                placeholder=\"";
echo __("Nhập số tiền cần trừ");
echo "\" required>\n                        </div>\n                    </div>\n                    <div class=\"row mb-4\">\n                        <label class=\"col-sm-4 col-form-label\" for=\"example-hf-email\">";
echo __("Reason");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <textarea class=\"form-control\" name=\"reason\" id=\"reasonInput\"></textarea>\n                        </div>\n                    </div>\n                    <center>Nhấn vào nút Submit để thực hiện trừ <b id=\"amountDisplay2\" style=\"color:red;\">0</b> trong\n                        <b id=\"walletDisplay2\">VÍ CHÍNH</b>\n                    </center>\n                </div>\n\n                <script>\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    var selectWallet = document.getElementById('walletSelect2');\n                    var amountInput = document.getElementById('amountInput2');\n                    var amountDisplay = document.getElementById('amountDisplay2');\n                    var walletDisplay = document.getElementById('walletDisplay2');\n\n                    // Hiển thị giá trị mặc định cho số tiền\n                    updateAmountDisplay();\n\n                    // Lắng nghe sự kiện input trên input số tiền\n                    amountInput.addEventListener('input', function() {\n                        updateAmountDisplay();\n                    });\n                    // Lắng nghe sự kiện change trên select box chọn loại ví\n                    selectWallet.addEventListener('change', function() {\n                        updateWalletDisplay();\n                    });\n\n                    function updateAmountDisplay() {\n                        // Lấy giá trị từ input\n                        var inputValue = amountInput.value;\n\n                        // Kiểm tra nếu giá trị rỗng hoặc không phải là số\n                        if (!inputValue || isNaN(inputValue)) {\n                            amountDisplay.textContent =\n                                '0'; // Hiển thị 0 nếu không có giá trị hoặc giá trị không hợp lệ\n                            return;\n                        }\n\n                        // Định dạng số tiền và hiển thị vào amountDisplay\n                        var formattedAmount = formatNumber(inputValue);\n                        amountDisplay.textContent = formattedAmount;\n                    }\n\n                    function updateWalletDisplay() {\n                        // Hiển thị loại ví được chọn\n                        walletDisplay.textContent = selectWallet.options[selectWallet.selectedIndex].text;\n                    }\n\n                    function formatNumber(value) {\n                        return parseFloat(value).toLocaleString('vi-VN');\n                    }\n                });\n                </script>\n                <div class=\"modal-footer\">\n                    <button type=\"button\" class=\"btn btn-hero btn-danger\" data-bs-dismiss=\"modal\"><i\n                            class=\"fa fa-fw fa-times me-1\"></i> ";
echo __("Close");
echo "</button>\n                    <button type=\"submit\" name=\"tru_tien\" class=\"btn btn-hero btn-success\"><i\n                            class=\"fa fa-fw fa-minus me-1\"></i> ";
echo __("Submit");
echo "</button>\n                </div>\n            </form>\n        </div>\n    </div>\n</div>";

?>