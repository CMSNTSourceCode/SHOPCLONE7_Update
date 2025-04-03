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
$body = ["title" => "Chỉnh sửa API nhà cung cấp", "desc" => "CMSNT Panel", "keyword" => "cmsnt, CMSNT, cmsnt.co,"];
$body["header"] = "\n\n";
$body["footer"] = "\n \n";
require_once __DIR__ . "/../../libs/suppliers.php";
require_once __DIR__ . "/../../models/is_admin.php";
if(isset($_GET["id"])) {
    $id = check_string($_GET["id"]);
    if(!($supplier = $CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '" . $id . "' "))) {
        redirect(base_url_admin("product-api"));
    }
} else {
    redirect(base_url_admin("product-api"));
}
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";
require_once __DIR__ . "/nav.php";
require_once __DIR__ . "/../../models/is_license.php";
if(!checkPermission($getUser["admin"], "manager_suppliers")) {
    exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back();}</script>");
}
if(isset($_POST["save"])) {
    if($CMSNT->site("status_demo") != 0) {
        exit("<script type=\"text/javascript\">if(!alert(\"Không được dùng chức năng này vì đây là trang web demo.\")){window.history.back().location.reload();}</script>");
    }
    if(empty($_POST["type"])) {
        exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng chọn loại API cần kết nối\")){window.history.back().location.reload();}</script>");
    }
    $type = check_string($_POST["type"]);
    if(empty($_POST["domain"])) {
        exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập domain cần kết nối\")){window.history.back().location.reload();}</script>");
    }
    $domain = check_string($_POST["domain"]);
    if(in_array($domain, $domain_blacklist)) {
        exit("<script type=\"text/javascript\">if(!alert(\"" . $domain . " nằm trong danh sách đen, không thể kết nối\")){window.history.back().location.reload();}</script>");
    }
    $price = "";
    $token = !empty($_POST["token"]) ? check_string($_POST["token"]) : NULL;
    if($type == "SHOPCLONE6") {
        $checkdomain = checkDomainAPI(check_string($_POST["domain"]), check_string($_POST["proxy"]));
        if(!$checkdomain["status"]) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $checkdomain["msg"] . "\")){window.history.back().location.reload();}</script>");
        }
        $data = balance_API_SHOPCLONE6(check_string($_POST["domain"]), check_string($_POST["username"]), check_string($_POST["password"]), check_string($_POST["proxy"]));
        $price = $data;
        $data = json_decode($data, true);
        if(isset($data["status"]) && $data["status"] == "error") {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $data["msg"] . "\")){window.history.back().location.reload();}</script>");
        }
    }
    if($type == "SHOPCLONE7") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập api_key\")){window.history.back().location.reload();}</script>");
        }
        $checkdomain = checkDomainAPI(check_string($_POST["domain"]), check_string($_POST["proxy"]));
        if(!$checkdomain["status"]) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $checkdomain["msg"] . "\")){window.history.back().location.reload();}</script>");
        }
        $response = balance_API_SHOPCLONE7(check_string($_POST["domain"]), check_string($_POST["api_key"]), check_string($_POST["proxy"]));
        $result = json_decode($response, true);
        if($result["status"] == "error") {
            $price = $result["msg"];
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["msg"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency($result["data"]["money"]);
    }
    if($type == "API_1") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập api_key\")){window.history.back().location.reload();}</script>");
        }
        $response = balance_API_1(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($response, true);
        if(!$result["status"]) {
            $price = $result["msg"];
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["msg"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency($result["balance"]);
    }
    if($type == "API_4") {
        if(empty($_POST["username"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập username\")){window.history.back().location.reload();}</script>");
        }
        if(empty($_POST["password"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập password\")){window.history.back().location.reload();}</script>");
        }
        $result = balance_API_4(check_string($_POST["domain"]), check_string($_POST["username"]), check_string($_POST["password"]));
        $result = json_decode($result, true);
        if(!isset($result["data"]["Data"]["userDetail"]["coin"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Thông tin kết nối không chính xác\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency(check_string($result["data"]["Data"]["userDetail"]["coin"]));
        $token = check_string($result["data"]["Data"]["accessToken"]);
    }
    if($type == "API_6") {
        $result = balance_API_6(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if(!isset($result["balance"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["message"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency($result["balance"]);
    }
    if($type == "API_9") {
        $result = balance_API_9(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if($result["error"] != 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["message"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency($result["data"]["balance"]);
    }
    if($type == "API_14") {
        $result = balance_API_14(check_string($_POST["domain"]), check_string($_POST["token"]));
        $result = json_decode($result, true);
        if(!isset($result["user"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["message"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency($result["user"]["balance"]);
    }
    if($type == "API17") {
        if(empty($_POST["username"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập username\")){window.history.back().location.reload();}</script>");
        }
        $username = check_string($_POST["username"]);
        if(empty($_POST["password"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập password\")){window.history.back().location.reload();}</script>");
        }
        $password = check_string($_POST["password"]);
        $data = balance_API_17(check_string($_POST["domain"]), $username, $password);
        $price = $data;
        $data = json_decode($data, true);
        if(isset($data["status"]) && $data["status"] == "error") {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $data["msg"] . "\")){window.history.back().location.reload();}</script>");
        }
    }
    if($type == "API_18") {
        $result = balance_API_18(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if(isset($result["error"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["error"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = "\$" . $result["balance"];
    }
    if($type == "API_19") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập api_key\")){window.history.back().location.reload();}</script>");
        }
        $result = balance_API_19(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if(!isset($result["balance"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["message"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency($result["balance"]);
    }
    if($type == "API_20") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập kioskToken\")){window.history.back().location.reload();}</script>");
        }
        if(empty($_POST["token"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập userToken\")){window.history.back().location.reload();}</script>");
        }
        $result = curl_get(check_string($_POST["domain"]) . "api/getStock?kioskToken=" . check_string($_POST["api_key"]) . "&userToken=" . check_string($_POST["token"]));
        $result = json_decode($result, true);
        if(!$result["success"]) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["description"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = check_string($result["name"]);
    }
    if($type == "API_21") {
        if(empty($_POST["token"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập token\")){window.history.back().location.reload();}</script>");
        }
        $price = "Không có API lấy số dư";
    }
    if($type == "API_22") {
        if(empty($_POST["token"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập token\")){window.history.back().location.reload();}</script>");
        }
        $price = "Không có API lấy số dư";
    }
    if($type == "API_23") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập kioskToken\")){window.history.back().location.reload();}</script>");
        }
        $result = balance_API_23(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if($result["Code"] != 0) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["Message"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = check_string("\$" . $result["Balance"]);
    }
    if($type == "API_24") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập api_key\")){window.history.back().location.reload();}</script>");
        }
        $result = balance_API_24(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if(!isset($result["data"][0]["money_available"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"[SYSTEM] Thông tin kết nối không chính xác\")){window.history.back().location.reload();}</script>");
        }
        $price = format_currency(check_string($result["data"][0]["money_available"]));
    }
    if($type == "API_25") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập api_key\")){window.history.back().location.reload();}</script>");
        }
        $result = balance_API_25(check_string($_POST["domain"]), check_string($_POST["api_key"]));
        $result = json_decode($result, true);
        if(isset($result) && $result["Code"] == 1) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . $result["Message"] . "\")){window.history.back().location.reload();}</script>");
        }
        $price = "\$" . $result["Balance"];
    }
    if($type == "API_26") {
        if(empty($_POST["api_key"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập api_key\")){window.history.back().location.reload();}</script>");
        }
        if(empty($_POST["token"])) {
            exit("<script type=\"text/javascript\">if(!alert(\"Vui lòng nhập token\")){window.history.back().location.reload();}</script>");
        }
        $result = balance_API_26(check_string($_POST["domain"]), check_string($_POST["api_key"]), check_string($_POST["token"]));
        $result = json_decode($result, true);
        if(!isset($result["status"]) && $result["status"] != "ok") {
            exit("<script type=\"text/javascript\">if(!alert(\"Thông tin kết nối không chính xác\")){window.history.back().location.reload();}</script>");
        }
        $price = check_string($result["balance"]);
    }
    $isUpdate = $CMSNT->update("suppliers", ["type" => $type, "domain" => $domain, "username" => !empty($_POST["username"]) ? check_string($_POST["username"]) : NULL, "password" => !empty($_POST["password"]) ? check_string($_POST["password"]) : NULL, "api_key" => !empty($_POST["api_key"]) ? check_string($_POST["api_key"]) : NULL, "token" => $token, "coupon" => !empty($_POST["coupon"]) ? check_string($_POST["coupon"]) : NULL, "price" => check_string($price), "check_string_api" => check_string($_POST["check_string_api"]), "discount" => check_string($_POST["discount"]), "update_name" => check_string($_POST["update_name"]), "proxy" => check_string($_POST["proxy"]), "sync_category" => !empty($_POST["sync_category"]) ? check_string($_POST["sync_category"]) : "OFF", "update_price" => check_string($_POST["update_price"]), "roundMoney" => check_string($_POST["roundMoney"]), "update_gettime" => gettime()], " `id` = '" . $supplier["id"] . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Edit API Supplier (" . $supplier["domain"] . " ID " . $supplier["id"] . ")."]);
        $my_text = $CMSNT->site("noti_action");
        $my_text = str_replace("{domain}", $_SERVER["SERVER_NAME"], $my_text);
        $my_text = str_replace("{username}", $getUser["username"], $my_text);
        $my_text = str_replace("{action}", "Edit API Supplier (" . $supplier["domain"] . " ID " . $supplier["id"] . ").", $my_text);
        $my_text = str_replace("{ip}", myip(), $my_text);
        $my_text = str_replace("{time}", gettime(), $my_text);
        sendMessAdmin($my_text);
        exit("<script type=\"text/javascript\">if(!alert(\"Lưu thành công!\")){window.history.back().location.reload();}</script>");
    }
    exit("<script type=\"text/javascript\">if(!alert(\"Lưu thất bại!\")){window.history.back().location.reload();}</script>");
}
echo "\n\n\n<div class=\"main-content app-content\">\n    <div class=\"container-fluid\">\n        <div class=\"d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb\">\n            <h1 class=\"page-title fw-semibold fs-18 mb-0\"><a type=\"button\"\n                    class=\"btn btn-dark btn-raised-shadow btn-wave btn-sm me-1\"\n                    href=\"";
echo base_url_admin("product-api");
echo "\"><i class=\"fa-solid fa-arrow-left\"></i></a> Chỉnh sửa API\n                nhà cung cấp ";
echo $supplier["domain"];
echo "            </h1>\n        </div>\n        \n        ";
foreach ($cron_suppliers as $type => $key) {
    if($supplier["type"] == $type && 120 <= time() - $CMSNT->site("time_cron_suppliers_" . $key)) {
        echo "        <div class=\"alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm\" role=\"alert\">\n            <svg class=\"svg-danger\" xmlns=\"http://www.w3.org/2000/svg\" height=\"1.5rem\" viewBox=\"0 0 24 24\"\n                width=\"1.5rem\" fill=\"#000000\">\n                <path d=\"M0 0h24v24H0z\" fill=\"none\" />\n                <path\n                    d=\"M15.73 3H8.27L3 8.27v7.46L8.27 21h7.46L21 15.73V8.27L15.73 3zM12 17.3c-.72 0-1.3-.58-1.3-1.3 0-.72.58-1.3 1.3-1.3.72 0 1.3.58 1.3 1.3 0 .72-.58 1.3-1.3 1.3zm1-4.3h-2V7h2v6z\" />\n            </svg>\n            Vui lòng thực hiện <b><a target=\"_blank\" class=\"text-primary\"\n                    href=\"https://help.cmsnt.co/huong-dan/huong-dan-xu-ly-khi-website-bao-loi-cron/\">CRON JOB</a></b>\n            liên kết:\n            <a class=\"text-primary\" href=\"";
        echo base_url("cron/suppliers/" . $key . ".php");
        echo "\" target=\"_blank\">\n                ";
        echo base_url("cron/suppliers/" . $key . ".php");
        echo "            </a> 1 phút 1 lần để hệ thống tự động cập nhật dữ liệu từ API.\n            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\">\n                <i class=\"bi bi-x\"></i>\n            </button>\n        </div>\n        ";
    }
}
echo "\n        <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">\n            <div class=\"row\">\n                <div class=\"col-xl-8\">\n                    <div class=\"card custom-card\">\n                        <div class=\"card-header justify-content-between\">\n                            <div class=\"card-title\">\n                                CHỈNH SỬA KẾT NỐI API\n                            </div>\n                        </div>\n                        <div class=\"card-body\">\n                            <div class=\"row mb-5 gy-3\">\n                                <div class=\"col-12 mb-2\">\n                                    <div class=\"api-section p-3 rounded bg-light mb-3\">\n                                        <h5 class=\"border-bottom pb-2 mb-3\"><i class=\"fa-solid fa-plug-circle-plus text-primary\"></i> Thông tin kết nối API</h5>\n                                        <div class=\"row\">\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"api-select\">\n                                                    <i class=\"fa-solid fa-server text-info\"></i> ";
echo __("Loại API:");
echo " \n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <select class=\"form-select form-select-lg shadow-sm\" id=\"api-select\" name=\"type\" required>\n                                                    <option value=\"\">-- Chọn loại API --</option>\n                                                    <option ";
echo $supplier["type"] == "SHOPCLONE7" ? "selected" : "";
echo " value=\"SHOPCLONE7\" class=\"bg-success-subtle\">\n                                                        SHOPCLONE7 CMSNT (Miễn phí)</option>\n                                                    <option ";
echo $supplier["type"] == "SHOPCLONE6" ? "selected" : "";
echo " value=\"SHOPCLONE6\" class=\"bg-success-subtle\">\n                                                        SHOPCLONE5 & SHOPCLONE6 CMSNT (Miễn phí)</option>\n                                                    <option ";
echo $supplier["type"] == "API_1" ? "selected" : "";
echo " value=\"API_1\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_4" ? "selected" : "";
echo " value=\"API_4\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_6" ? "selected" : "";
echo " value=\"API_6\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_9" ? "selected" : "";
echo " value=\"API_9\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_14" ? "selected" : "";
echo " value=\"API_14\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_17" ? "selected" : "";
echo " value=\"API_17\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_18" ? "selected" : "";
echo " value=\"API_18\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_19" ? "selected" : "";
echo " value=\"API_19\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_20" ? "selected" : "";
echo " value=\"API_20\" class=\"bg-warning-subtle\">API (Không còn hỗ trợ)</option>\n                                                    <option ";
echo $supplier["type"] == "API_21" ? "selected" : "";
echo " value=\"API_21\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_22" ? "selected" : "";
echo " value=\"API_22\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_23" ? "selected" : "";
echo " value=\"API_23\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_24" ? "selected" : "";
echo " value=\"API_24\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_25" ? "selected" : "";
echo " value=\"API_25\">API (200.000đ / lần)</option>\n                                                    <option ";
echo $supplier["type"] == "API_26" ? "selected" : "";
echo " value=\"API_26\">API (200.000đ / lần)</option>\n                                                </select>\n                                                <div class=\"form-text\"><i class=\"fas fa-info-circle\"></i> API CMSNT được hỗ trợ miễn phí, API khác tính phí 200.000đ/lần</div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"domain\">\n                                                    <i class=\"fa-solid fa-globe text-primary\"></i> ";
echo __("Domain");
echo "                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <div class=\"input-group input-group-lg\">\n                                                    <span class=\"input-group-text bg-light\"><i class=\"fas fa-link\"></i></span>\n                                                    <input type=\"text\" class=\"form-control shadow-sm\" id=\"domain\" value=\"";
echo $supplier["domain"];
echo "\"\n                                                        placeholder=\"VD: https://domain.com/\" name=\"domain\" autocomplete=\"off\" \n                                                        data-lpignore=\"true\" required>\n                                                </div>\n                                                <div class=\"form-text\"><i class=\"fas fa-info-circle\"></i> Nhập đầy đủ URL kèm https:// hoặc http://</div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"balance\">\n                                                    <i class=\"fa-solid fa-wallet text-success\"></i> ";
echo __("Số dư:");
echo "                                                </label>\n                                                <div class=\"input-group\">\n                                                    <span class=\"input-group-text bg-light\"><i class=\"fas fa-coins\"></i></span>\n                                                    <textarea class=\"form-control shadow-sm\" id=\"balance\" readonly>";
echo $supplier["price"];
echo "</textarea>\n                                                </div>\n                                            </div>\n                                        </div>\n                                        \n                                        <!-- Thông tin đăng nhập -->\n                                        <div class=\"credentials-container mt-3\">\n                                            <div class=\"row\">\n                                                <div class=\"col-md-6 mb-3\" id=\"username\" style=\"display: none;\">\n                                                    <label class=\"form-label fw-bold\" for=\"username-input\">\n                                                        <i class=\"fa-solid fa-user text-warning\"></i> ";
echo __("Username:");
echo "                                                        <span class=\"text-danger\">*</span>\n                                                    </label>\n                                                    <div class=\"input-group\">\n                                                        <span class=\"input-group-text bg-light\"><i class=\"fas fa-user\"></i></span>\n                                                        <input type=\"text\" class=\"form-control shadow-sm\" id=\"username-input\" name=\"username\"\n                                                            value=\"";
echo $supplier["username"];
echo "\" autocomplete=\"new-password\"\n                                                            placeholder=\"";
echo __("Nhập tên đăng nhập website API");
echo "\">\n                                                    </div>\n                                                </div>\n                                                <div class=\"col-md-6 mb-3\" id=\"password\" style=\"display: none;\">\n                                                    <label class=\"form-label fw-bold\" for=\"password-input\">\n                                                        <i class=\"fa-solid fa-key text-warning\"></i> ";
echo __("Password:");
echo "                                                        <span class=\"text-danger\">*</span>\n                                                    </label>\n                                                    <div class=\"input-group\">\n                                                        <span class=\"input-group-text bg-light\"><i class=\"fas fa-lock\"></i></span>\n                                                        <input type=\"password\" class=\"form-control shadow-sm\" id=\"password-input\" name=\"password\"\n                                                            value=\"";
echo $supplier["password"];
echo "\" autocomplete=\"new-password\"\n                                                            placeholder=\"";
echo __("Nhập mật khẩu đăng nhập website API");
echo "\">\n                                                        <button class=\"btn btn-outline-secondary\" type=\"button\" id=\"toggle-password\">\n                                                            <i class=\"fas fa-eye\"></i>\n                                                        </button>\n                                                    </div>\n                                                </div>\n                                                <div class=\"col-md-6 mb-3\" id=\"api_key\" style=\"display: none;\">\n                                                    <label class=\"form-label fw-bold\" for=\"api-key-input\">\n                                                        <i class=\"fa-solid fa-key text-danger\"></i> ";
echo __("API Key:");
echo "                                                        <span class=\"text-danger\">*</span>\n                                                    </label>\n                                                    <div class=\"input-group\">\n                                                        <span class=\"input-group-text bg-light\"><i class=\"fas fa-key\"></i></span>\n                                                        <input type=\"text\" class=\"form-control shadow-sm\" id=\"api-key-input\" name=\"api_key\"\n                                                            value=\"";
echo $supplier["api_key"];
echo "\" autocomplete=\"new-password\"\n                                                            placeholder=\"";
echo __("Nhập Api Key trong website API");
echo "\">\n                                                    </div>\n                                                </div>\n                                                <div class=\"col-md-6 mb-3\" id=\"token\" style=\"display: none;\">\n                                                    <label class=\"form-label fw-bold\" for=\"token-input\">\n                                                        <i class=\"fa-solid fa-shield-halved text-success\"></i> ";
echo __("Token:");
echo "                                                        <span class=\"text-danger\">*</span>\n                                                    </label>\n                                                    <div class=\"input-group\">\n                                                        <span class=\"input-group-text bg-light\"><i class=\"fas fa-shield-alt\"></i></span>\n                                                        <input type=\"text\" class=\"form-control shadow-sm\" id=\"token-input\" name=\"token\"\n                                                            value=\"";
echo $supplier["token"];
echo "\" autocomplete=\"new-password\"\n                                                            placeholder=\"";
echo __("Nhập Token trong website API");
echo "\">\n                                                    </div>\n                                                </div>\n                                                <div class=\"col-md-6 mb-3\" id=\"coupon\" style=\"display: none;\">\n                                                    <label class=\"form-label fw-bold\" for=\"coupon-input\">\n                                                        <i class=\"fa-solid fa-tag text-info\"></i> ";
echo __("Coupon:");
echo "                                                    </label>\n                                                    <div class=\"input-group\">\n                                                        <span class=\"input-group-text bg-light\"><i class=\"fas fa-percentage\"></i></span>\n                                                        <input type=\"text\" class=\"form-control shadow-sm\" id=\"coupon-input\" name=\"coupon\"\n                                                            value=\"";
echo $supplier["coupon"];
echo "\" autocomplete=\"new-password\"\n                                                            placeholder=\"";
echo __("Nhập mã giảm giá nếu có");
echo "\">\n                                                    </div>\n                                                </div>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div>\n                                \n                                <!-- Cài đặt đồng bộ -->\n                                <div class=\"col-12 mb-2\">\n                                    <div class=\"api-section p-3 rounded bg-light mb-3\">\n                                        <h5 class=\"border-bottom pb-2 mb-3\"><i class=\"fa-solid fa-sliders text-success\"></i> Cài đặt đồng bộ dữ liệu</h5>\n                                        <div class=\"row\">\n                                            <div class=\"col-md-6 mb-3\" id=\"sync_category\" style=\"display: none;\">\n                                                <label class=\"form-label fw-bold\" for=\"sync-category-select\">\n                                                    <i class=\"fa-solid fa-folder-tree text-primary\"></i> Đồng bộ chuyên mục từ API\n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <select class=\"form-select\" id=\"sync-category-select\" name=\"sync_category\" required>\n                                                    <option ";
echo $supplier["sync_category"] == "OFF" ? "selected" : "";
echo " value=\"OFF\">OFF - Không đồng bộ</option>\n                                                    <option ";
echo $supplier["sync_category"] == "ON" ? "selected" : "";
echo " value=\"ON\">ON - Đồng bộ tự động</option>\n                                                </select>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-info-circle\"></i> Hệ thống sẽ tự động đồng bộ và thêm chuyên mục từ API.\n                                                </div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"update-price-select\">\n                                                    <i class=\"fa-solid fa-sack-dollar text-success\"></i> Cập nhật giá bán tự động\n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <select class=\"form-select\" id=\"update-price-select\" name=\"update_price\" required>\n                                                    <option ";
echo $supplier["update_price"] == "ON" ? "selected" : "";
echo " value=\"ON\">ON - Cập nhật tự động</option>\n                                                    <option ";
echo $supplier["update_price"] == "OFF" ? "selected" : "";
echo " value=\"OFF\">OFF - Giữ nguyên giá</option>\n                                                </select>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-info-circle\"></i> Khi giá sản phẩm thay đổi ở API, hệ thống sẽ tự động cập nhật.\n                                                </div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"round-money-select\">\n                                                    <i class=\"fa-solid fa-circle-dollar-to-slot text-primary\"></i> Làm tròn giá bán\n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <select class=\"form-select\" id=\"round-money-select\" name=\"roundMoney\" required>\n                                                    <option ";
echo $supplier["roundMoney"] == "OFF" ? "selected" : "";
echo " value=\"OFF\">OFF - Giữ nguyên số</option>\n                                                    <option ";
echo $supplier["roundMoney"] == "ON" ? "selected" : "";
echo " value=\"ON\">ON - Làm tròn số</option>\n                                                </select>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-info-circle\"></i> VD: ";
echo format_currency(10550);
echo " sẽ làm tròn thành ";
echo format_currency(10600);
echo " hoặc ";
echo format_currency(10530);
echo " sẽ làm tròn thành ";
echo format_currency(10500);
echo ".\n                                                </div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"discount-input\">\n                                                    <i class=\"fa-solid fa-percent text-danger\"></i> Tăng giá so với giá gốc\n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <div class=\"input-group\">\n                                                    <input type=\"number\" class=\"form-control shadow-sm\" id=\"discount-input\" value=\"";
echo $supplier["discount"];
echo "\" min=\"0\"\n                                                        placeholder=\"Nhập % tăng giá\" name=\"discount\" required>\n                                                    <span class=\"input-group-text bg-light\">%</span>\n                                                </div>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-info-circle\"></i> Nhập 10 để tăng giá bán thêm 10% so với giá gốc, nhập 0 để giữ nguyên.\n                                                </div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"update-name-select\">\n                                                    <i class=\"fa-solid fa-font text-info\"></i> Cập nhật tên & mô tả tự động\n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <select class=\"form-select\" id=\"update-name-select\" name=\"update_name\" required>\n                                                    <option ";
echo $supplier["update_name"] == "ON" ? "selected" : "";
echo " value=\"ON\">ON - Cập nhật tự động</option>\n                                                    <option ";
echo $supplier["update_name"] == "OFF" ? "selected" : "";
echo " value=\"OFF\">OFF - Giữ nguyên nội dung</option>\n                                                </select>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-info-circle\"></i> Tự động cập nhật tên và mô tả sản phẩm từ API.\n                                                </div>\n                                            </div>\n                                            <div class=\"col-md-6 mb-3\">\n                                                <label class=\"form-label fw-bold\" for=\"check-string-api-select\">\n                                                    <i class=\"fa-solid fa-code text-warning\"></i> Lọc HTML trong nội dung API\n                                                    <span class=\"text-danger\">*</span>\n                                                </label>\n                                                <select class=\"form-select\" id=\"check-string-api-select\" name=\"check_string_api\" required>\n                                                    <option ";
echo $supplier["check_string_api"] == "ON" ? "selected" : "";
echo " value=\"ON\">ON - Kích hoạt bảo vệ</option>\n                                                    <option ";
echo $supplier["check_string_api"] == "OFF" ? "selected" : "";
echo " value=\"OFF\">OFF - Tắt bảo vệ</option>\n                                                </select>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-shield-alt text-danger\"></i> Bảo vệ website bằng cách lọc mã HTML độc hại từ API.\n                                                </div>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div>\n                                <div class=\"col-12 mb-2\">\n                                    <div class=\"api-section p-3 rounded bg-light mb-3\">\n                                        <h5 class=\"border-bottom pb-2 mb-3\"><i class=\"fa-solid fa-sliders text-success\"></i> Cài đặt khác</h5>\n                                        <div class=\"row\">\n                                            \n                                            <div class=\"col-md-6 mb-3\" id=\"proxy\" style=\"display: none;\">\n                                                <label class=\"form-label fw-bold\" for=\"proxy-input\">\n                                                    <i class=\"fa-solid fa-globe text-danger\"></i> Proxy v4 hoặc v6 (nếu có):\n                                                </label>\n                                                <div class=\"input-group\">\n                                                    <input type=\"text\" class=\"form-control shadow-sm\" id=\"proxy-input\" value=\"";
echo $supplier["proxy"];
echo "\"\n                                                        placeholder=\"ip:port:username:password\" name=\"proxy\" autocomplete=\"off\">\n                                                </div>\n                                                <div class=\"form-text\">\n                                                    <i class=\"fas fa-info-circle\"></i> Chỉ dùng Proxy nếu quý khách đã nhờ phía API whitelist IP nhưng vẫn không hiện số dư sau khi kết nối.\n                                                </div>\n                                            </div>\n                                            \n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class=\"d-grid gap-2 mb-3\">\n                                <button type=\"submit\" name=\"save\" class=\"btn btn-primary btn-lg shadow-lg btn-wave\">\n                                    <i class=\"fa-solid fa-floppy-disk me-1\"></i> ";
echo __("Lưu thay đổi");
echo "                                </button>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"col-xl-4\">\n                    <div class=\"card custom-card position-sticky\" style=\"top: 85px;\">\n                        <div class=\"card-header bg-primary\">\n                            <div class=\"card-title\">\n                                <i class=\"fa-solid fa-circle-info me-1\"></i> LƯU Ý\n                            </div>\n                        </div>\n                        <div class=\"card-body\">\n                            <div class=\"alert alert-primary\" role=\"alert\">\n                                <i class=\"fa-solid fa-lightbulb me-1\"></i> <strong>Mục đích:</strong> Chức năng này cho phép quý khách bán lại sản phẩm của website khác trên chính website của quý khách.\n                            </div>\n\n                            <div class=\"alert alert-warning mb-3\" role=\"alert\">\n                                <h6 class=\"alert-heading\"><i class=\"fa-solid fa-triangle-exclamation me-1\"></i> Lưu ý quan trọng!</h6>\n                                <p>Trường hợp quý khách cấu hình đúng nhưng không hiện số dư API có thể do máy chủ không thể kết nối với API đích.</p>\n                                <a href=\"https://help.cmsnt.co/huong-dan/ket-noi-api-nhap-dung-thong-tin-nhung-khong-ra-so-du-thi-lam-sao/\" class=\"btn btn-sm btn-warning mt-2\" target=\"_blank\">\n                                    <i class=\"fas fa-external-link-alt me-1\"></i> Xem hướng dẫn xử lý\n                                </a>\n                            </div>\n\n                            <div class=\"d-flex align-items-center p-3 rounded bg-light mb-3\">\n                                <div class=\"me-3 text-primary fs-3\"><i class=\"fa-solid fa-handshake\"></i></div>\n                                <div>\n                                    <h6 class=\"mb-1\">API cùng hệ sinh thái CMSNT</h6>\n                                    <p class=\"mb-0 text-success fw-bold\">Miễn phí</p>\n                                </div>\n                            </div>\n\n                            <div class=\"d-flex align-items-center p-3 rounded bg-light mb-3\">\n                                <div class=\"me-3 text-warning fs-3\"><i class=\"fa-solid fa-circle-dollar-to-slot\"></i></div>\n                                <div>\n                                    <h6 class=\"mb-1\">API ngoài hệ sinh thái</h6>\n                                    <p class=\"mb-0\">Phí tích hợp: <span class=\"text-danger fw-bold\">200.000đ / 1 lần</span></p>\n                                </div>\n                            </div>\n\n                            <div class=\"d-grid gap-2\">\n                                <a href=\"https://www.cmsnt.co/p/contact.html\" class=\"btn btn-outline-primary\" target=\"_blank\">\n                                    <i class=\"fa-solid fa-headset me-1\"></i> Liên hệ hỗ trợ kết nối API\n                                </a>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n        </form>\n    </div>\n</div>\n</div>\n\n\n\n\n\n";
require_once __DIR__ . "/footer.php";
echo "\n<script>\nvar lightboxVideo = GLightbox({\n    selector: '.glightbox'\n});\n\nCKEDITOR.replace(\"description\");\nCKEDITOR.replace(\"note\");\n\nfunction removeImageProduct(id, image) {\n    cuteAlert({\n        type: \"question\",\n        title: \"Xác nhận xóa ảnh\",\n        message: \"Bạn có chắc chắn muốn xóa ảnh \" + id + \" không ?\",\n        confirmText: \"Đồng Ý\",\n        cancelText: \"Hủy\"\n    }).then((e) => {\n        if (e) {\n            \$.ajax({\n                url: \"";
echo BASE_URL("ajaxs/admin/remove.php");
echo "\",\n                method: \"POST\",\n                dataType: \"JSON\",\n                data: {\n                    id: id,\n                    image: image,\n                    action: 'removeImageProduct'\n                },\n                success: function(result) {\n                    if (result.status == 'success') {\n                        showMessage(result.msg, result.status);\n                        location.reload();\n                    } else {\n                        showMessage(result.msg, result.status);\n                    }\n                },\n                error: function() {\n                    alert(html(result));\n                    location.reload();\n                }\n            });\n        }\n    })\n}\n</script>\n\n\n<script>\ndocument.addEventListener(\"DOMContentLoaded\", function() {\n    // Ngăn chặn autofill bằng cách thêm một trường ẩn và đảm bảo không tự động điền\n    const form = document.querySelector('form');\n    const hiddenInput = document.createElement('input');\n    hiddenInput.type = 'text';\n    hiddenInput.style.display = 'none';\n    hiddenInput.name = 'prevent_autofill';\n    hiddenInput.setAttribute('autocomplete', 'off');\n    form.prepend(hiddenInput);\n    \n    // Thêm thuộc tính autocomplete=\"new-password\" vào tất cả các trường input\n    const allInputs = document.querySelectorAll('input[type=\"text\"], input[type=\"password\"]');\n    allInputs.forEach(input => {\n        input.setAttribute('autocomplete', 'new-password');\n    });\n    \n    // Đoạn code xử lý toggle fields\n    const typeSelect = document.querySelector(\"select[name='type']\");\n    const usernameField = document.getElementById(\"username\");\n    const passwordField = document.getElementById(\"password\");\n    const apiKeyField = document.getElementById(\"api_key\");\n    const tokenField = document.getElementById(\"token\");\n    const couponField = document.getElementById(\"coupon\");\n    const sync_category = document.getElementById(\"sync_category\");\n    const proxyField = document.getElementById(\"proxy\");\n\n    // Thêm xử lý hiển thị/ẩn mật khẩu\n    document.getElementById('toggle-password').addEventListener('click', function() {\n        const passwordInput = document.getElementById('password-input');\n        const icon = this.querySelector('i');\n        \n        if (passwordInput.type === 'password') {\n            passwordInput.type = 'text';\n            icon.classList.remove('fa-eye');\n            icon.classList.add('fa-eye-slash');\n        } else {\n            passwordInput.type = 'password';\n            icon.classList.remove('fa-eye-slash');\n            icon.classList.add('fa-eye');\n        }\n    });\n\n    function toggleFields() {\n        const selectedType = typeSelect.value;\n        usernameField.style.display = \"none\";\n        passwordField.style.display = \"none\";\n        apiKeyField.style.display = \"none\";\n        tokenField.style.display = \"none\";\n        couponField.style.display = \"none\";\n        sync_category.style.display = \"none\";\n        proxyField.style.display = \"none\";\n\n        if (selectedType === \"SHOPCLONE6\") {\n            sync_category.style.display = \"block\";\n            usernameField.style.display = \"block\";\n            passwordField.style.display = \"block\";\n            proxyField.style.display = \"block\";\n        } else if (selectedType === \"SHOPCLONE7\") {\n            sync_category.style.display = \"block\";\n            apiKeyField.style.display = \"block\";\n            couponField.style.display = \"block\";\n            proxyField.style.display = \"block\";\n        } else if (selectedType === \"API_4\" || selectedType === \"API_17\") {\n            usernameField.style.display = \"block\";\n            passwordField.style.display = \"block\";\n        } else if (selectedType === \"API_1\" || selectedType === \"API_6\" || selectedType === \"API_18\" ||\n            selectedType === \"API_19\" || selectedType === \"API_9\" || selectedType === \"API_23\" ||\n            selectedType === \"API_24\" || selectedType === \"API_25\") {\n            apiKeyField.style.display = \"block\";\n        } else if (selectedType === \"API_14\" || selectedType === \"API_21\" || selectedType === \"API_22\") {\n            tokenField.style.display = \"block\";\n        } else if (selectedType === \"API_20\" || selectedType === \"API_26\") {\n            apiKeyField.style.display = \"block\";\n            tokenField.style.display = \"block\";\n        }\n    }\n    toggleFields();\n    typeSelect.addEventListener(\"change\", toggleFields);\n    \n    // Cải thiện UX với hiệu ứng làm nổi bật section\n    const apiSelect = document.getElementById('api-select');\n    apiSelect.addEventListener('change', function() {\n        if (this.value) {\n            document.querySelector('.credentials-container').classList.add('animate__animated', 'animate__fadeIn');\n            setTimeout(() => {\n                document.querySelector('.credentials-container').classList.remove('animate__animated', 'animate__fadeIn');\n            }, 1000);\n        }\n    });\n});\n</script>\n\n<style>\n.api-section {\n    border-left: 4px solid #3498db;\n    transition: all 0.3s ease;\n}\n\n.api-section:hover {\n    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);\n}\n</style>";

?>