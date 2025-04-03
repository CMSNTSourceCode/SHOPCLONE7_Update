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
$body = ["title" => __("Đơn hàng") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n<script src=\"https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js\"></script>\n<link href=\"https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css\" rel=\"stylesheet\">\n";
$body["footer"] = "\n<!-- Select2 Cdn -->\n<script src=\"https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js\"></script>\n<!-- Internal Select-2.js -->\n<script src=\"" . base_url("public/theme/") . "assets/js/select2.js\"></script>\n";
require_once __DIR__ . "/../../models/is_admin.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";
if(!checkPermission($getUser["admin"], "view_orders_product")) {
    exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back();}</script>");
}
if(isset($_GET["limit"])) {
    $limit = (int) check_string($_GET["limit"]);
} else {
    $limit = 10;
}
if(isset($_GET["page"])) {
    $page = check_string((int) $_GET["page"]);
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `id` > 0 ";
$buyer = "";
$username = "";
$create_gettime = "";
$trans_id = "";
$shortByDate = "";
$supplier_id = "";
$api_transid = "";
$product_id = "";
$uid = "";
$account = "";
if(!empty($_GET["account"])) {
    $account = check_string($_GET["account"]);
    $product_sold_rows = $CMSNT->get_list("SELECT * FROM `product_sold` WHERE `account` LIKE \"%" . $account . "%\" ");
    if(!empty($product_sold_rows)) {
        $trans_ids = array_map(function ($row) {
            return $row["trans_id"];
        }, $product_sold_rows);
        $trans_ids_str = implode("\",\"", $trans_ids);
        $where .= " AND `trans_id` IN (\"" . $trans_ids_str . "\") ";
    }
}
if(!empty($_GET["uid"])) {
    $uid = check_string($_GET["uid"]);
    $product_sold_rows = $CMSNT->get_list("SELECT * FROM `product_sold` WHERE `uid` = \"" . $uid . "\" ");
    if(!empty($product_sold_rows)) {
        $trans_ids = array_map(function ($row) {
            return $row["trans_id"];
        }, $product_sold_rows);
        $trans_ids_str = implode("\",\"", $trans_ids);
        $where .= " AND `trans_id` IN (\"" . $trans_ids_str . "\") ";
    }
}
if(!empty($_GET["product_id"])) {
    $product_id = check_string($_GET["product_id"]);
    $where .= " AND `product_id` = \"" . $product_id . "\" ";
}
if(!empty($_GET["api_transid"])) {
    $api_transid = check_string($_GET["api_transid"]);
    $where .= " AND `api_transid` LIKE \"%" . $api_transid . "%\" ";
}
if(!empty($_GET["supplier_id"])) {
    $supplier_id = check_string($_GET["supplier_id"]);
    $where .= " AND `supplier_id` = \"" . $supplier_id . "\" ";
}
if(!empty($_GET["username"])) {
    $username = check_string($_GET["username"]);
    if($idUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `username` = '" . $username . "' ")) {
        $where .= " AND `buyer` =  \"" . $idUser["id"] . "\" ";
    } else {
        $where .= " AND `buyer` =  \"\" ";
    }
}
if(!empty($_GET["buyer"])) {
    $buyer = check_string($_GET["buyer"]);
    $where .= " AND `buyer` = \"" . $buyer . "\" ";
}
if(!empty($_GET["trans_id"])) {
    $trans_id = check_string($_GET["trans_id"]);
    $where .= " AND `trans_id` LIKE \"%" . $trans_id . "%\" ";
}
if(!empty($_GET["create_gettime"])) {
    $create_gettime = check_string($_GET["create_gettime"]);
    $createdate = $create_gettime;
    $create_gettime_1 = str_replace("-", "/", $create_gettime);
    $create_gettime_1 = explode(" to ", $create_gettime_1);
    if($create_gettime_1[0] != $create_gettime_1[1]) {
        $create_gettime_1 = [$create_gettime_1[0] . " 00:00:00", $create_gettime_1[1] . " 23:59:59"];
        $where .= " AND `create_gettime` >= '" . $create_gettime_1[0] . "' AND `create_gettime` <= '" . $create_gettime_1[1] . "' ";
    }
}
if(isset($_GET["shortByDate"])) {
    $shortByDate = check_string($_GET["shortByDate"]);
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date("m");
    $currentYear = date("Y");
    $currentDate = date("Y-m-d");
    if($shortByDate == 1) {
        $where .= " AND `create_gettime` LIKE '%" . $currentDate . "%' ";
    }
    if($shortByDate == 2) {
        $where .= " AND YEAR(create_gettime) = " . $currentYear . " AND WEEK(create_gettime, 1) = " . $currentWeek . " ";
    }
    if($shortByDate == 3) {
        $where .= " AND MONTH(create_gettime) = '" . $currentMonth . "' AND YEAR(create_gettime) = '" . $currentYear . "' ";
    }
    if($shortByDate == 4) {
        $where .= " AND DATE(create_gettime) = '" . $yesterday . "' ";
    }
}
$listDatatable = $CMSNT->get_list(" SELECT * FROM `product_order` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `product_order` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(base_url_admin("product-orders&limit=" . $limit . "&shortByDate=" . $shortByDate . "&buyer=" . $buyer . "&trans_id=" . $trans_id . "&create_gettime=" . $create_gettime . "&username=" . $username . "&supplier_id=" . $supplier_id . "&api_transid=" . $api_transid . "&product_id=" . $product_id . "&uid=" . $uid . "&account=" . $account . "&"), $from, $totalDatatable, $limit);
echo "\n\n\n<div class=\"main-content app-content\">\n    <div class=\"container-fluid\">\n        <div class=\"d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb\">\n            <h1 class=\"page-title fw-semibold fs-18 mb-0\"><i class=\"fa-solid fa-cart-shopping\"></i> Đơn hàng</h1>\n        </div>\n        ";
if(!$CMSNT->get_row(" SELECT * FROM `automations` WHERE `type` IN ('delete_order', 'delete_order_not_uid', 'delete_order_revenue') ")) {
    echo "        <div class=\"alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm\" role=\"alert\">\n            <svg class=\"svg-warning\" xmlns=\"http://www.w3.org/2000/svg\" height=\"1.5rem\" viewBox=\"0 0 24 24\"\n                width=\"1.5rem\" fill=\"#000000\">\n                <path d=\"M0 0h24v24H0z\" fill=\"none\" />\n                <path d=\"M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z\" />\n            </svg>\n            Quý khách nên cài <a class=\"text-primary\"\n                href=\"https://help.cmsnt.co/huong-dan/cau-hinh-tu-dong-xoa-don-hang-da-ban-trong-shopclone7/\"\n                target=\"_blank\">tự động xóa đơn hàng</a> đã bán sau khoảng thời gian nhất định để bảo mật dữ liệu khách\n            hàng và giảm tải tài nguyên máy chủ.\n            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"><i\n                    class=\"bi bi-x\"></i></button>\n        </div>\n        ";
}
echo "        <div class=\"row\">\n            <div class=\"col-xl-12\">\n                <div class=\"text-right\">\n\n                    <button type=\"button\" onclick=\"top_san_pham_ban_chay()\" class=\"btn btn-danger btn-sm mb-3\">\n                        <i class=\"fa-solid fa-chart-line\"></i> TOP SẢN PHẨM BÁN CHẠY\n                    </button>\n\n                </div>\n            </div>\n            <div class=\"col-xl-12\">\n                <div class=\"card custom-card\">\n                    <div class=\"card-header justify-content-between\">\n                        <div class=\"card-title\">\n                            DANH SÁCH ĐƠN HÀNG\n                        </div>\n                    </div>\n                    <div class=\"card-body\">\n                        <form action=\"";
echo base_url();
echo "\" class=\"align-items-center mb-3\" name=\"formSearch\" method=\"GET\">\n                            <div class=\"row g-2 mb-3\">\n                                <input type=\"hidden\" name=\"module\" value=\"admin\">\n                                <input type=\"hidden\" name=\"action\" value=\"product-orders\">\n                                <input type=\"hidden\" value=\"";
echo $getUser["token"];
echo "\" id=\"token\">\n                                <div class=\"col-md-3 col-6\">\n                                    <input class=\"form-control\" value=\"";
echo $buyer;
echo "\" name=\"buyer\" placeholder=\"ID User\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <input class=\"form-control\" value=\"";
echo $username;
echo "\" name=\"username\"\n                                        placeholder=\"Username\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <input class=\"form-control\" value=\"";
echo $trans_id;
echo "\" name=\"trans_id\"\n                                        placeholder=\"Mã đơn hàng\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <input class=\"form-control\" value=\"";
echo $uid;
echo "\" name=\"uid\" placeholder=\"UID\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <input class=\"form-control\" value=\"";
echo $account;
echo "\" name=\"account\"\n                                        placeholder=\"Account\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <input class=\"form-control\" value=\"";
echo $api_transid;
echo "\" name=\"api_transid\"\n                                        placeholder=\"Mã đơn hàng API\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <select class=\"form-control js-example-basic-single\" name=\"supplier_id\">\n                                        <option value=\"\">";
echo __("-- API Supplier --");
echo "</option>\n                                        ";
foreach ($CMSNT->get_list("SELECT * FROM `suppliers` ") as $supplier) {
    echo "                                        <option ";
    echo $supplier_id == $supplier["id"] ? "selected" : "";
    echo "                                            value=\"";
    echo $supplier["id"];
    echo "\">";
    echo $supplier["domain"];
    echo "</option>\n                                        ";
}
echo "                                    </select>\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <select class=\"form-control js-example-basic-single\" name=\"product_id\">\n                                        <option value=\"\">";
echo __("-- Sản phẩm ");
echo "</option>\n                                        ";
foreach ($CMSNT->get_list("SELECT * FROM `products` ") as $product) {
    echo "                                        <option ";
    echo $product_id == $product["id"] ? "selected" : "";
    echo "                                            value=\"";
    echo $product["id"];
    echo "\">\n                                            ";
    echo $product["name"];
    echo "                                        </option>\n                                        ";
}
echo "                                    </select>\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <input type=\"text\" name=\"create_gettime\" class=\"form-control\" id=\"daterange\"\n                                        value=\"";
echo $create_gettime;
echo "\" placeholder=\"Chọn thời gian\">\n                                </div>\n                                <div class=\"col-md-3 col-6\">\n                                    <button class=\"btn btn-hero btn-primary\"><i class=\"fa fa-search\"></i>\n                                        ";
echo __("Search");
echo "                                    </button>\n                                    <a class=\"btn btn-hero btn-danger\" href=\"";
echo base_url_admin("product-orders");
echo "\"><i\n                                            class=\"fa fa-trash\"></i>\n                                        ";
echo __("Clear filter");
echo "                                    </a>\n                                </div>\n                            </div>\n                            <div class=\"top-filter\">\n                                <div class=\"filter-show\">\n                                    <label class=\"filter-label\">Show :</label>\n                                    <select name=\"limit\" onchange=\"this.form.submit()\"\n                                        class=\"form-select filter-select\">\n                                        <option ";
echo $limit == 5 ? "selected" : "";
echo " value=\"5\">5</option>\n                                        <option ";
echo $limit == 10 ? "selected" : "";
echo " value=\"10\">10</option>\n                                        <option ";
echo $limit == 20 ? "selected" : "";
echo " value=\"20\">20</option>\n                                        <option ";
echo $limit == 50 ? "selected" : "";
echo " value=\"50\">50</option>\n                                        <option ";
echo $limit == 100 ? "selected" : "";
echo " value=\"100\">100</option>\n                                        <option ";
echo $limit == 500 ? "selected" : "";
echo " value=\"500\">500</option>\n                                        <option ";
echo $limit == 1000 ? "selected" : "";
echo " value=\"1000\">1000</option>\n                                    </select>\n                                </div>\n                                <div class=\"filter-short\">\n                                    <label class=\"filter-label\">";
echo __("Short by Date:");
echo "</label>\n                                    <select name=\"shortByDate\" onchange=\"this.form.submit()\"\n                                        class=\"form-select filter-select\">\n                                        <option value=\"\">";
echo __("Tất cả");
echo "</option>\n                                        <option ";
echo $shortByDate == 1 ? "selected" : "";
echo " value=\"1\">";
echo __("Hôm nay");
echo "                                        </option>\n                                        <option ";
echo $shortByDate == 4 ? "selected" : "";
echo " value=\"4\">";
echo __("Hôm qua");
echo "                                        </option>\n                                        <option ";
echo $shortByDate == 2 ? "selected" : "";
echo " value=\"2\">";
echo __("Tuần này");
echo "                                        </option>\n                                        <option ";
echo $shortByDate == 3 ? "selected" : "";
echo " value=\"3\">\n                                            ";
echo __("Tháng này");
echo "                                        </option>\n                                    </select>\n                                </div>\n                            </div>\n                        </form>\n                        <div class=\"table-responsive table-wrapper mb-3\">\n                            <table class=\"table text-nowrap table-striped table-hover table-bordered\">\n                                <thead>\n                                    <tr>\n                                        <th class=\"text-center\">\n                                            <div class=\"form-check form-check-md d-flex align-items-center\">\n                                                <input type=\"checkbox\" class=\"form-check-input\" name=\"check_all\"\n                                                    id=\"check_all_checkbox_product\" value=\"option1\">\n                                            </div>\n                                        </th>\n                                        <th class=\"text-center\">";
echo __("Thao tác");
echo "</th>\n                                        <th class=\"text-center\">";
echo __("Username");
echo "</th>\n                                        <th class=\"text-center\">";
echo __("Đơn hàng");
echo "</th>\n                                        <th class=\"text-center\">";
echo __("Thanh toán");
echo "</th>\n                                        <th class=\"text-center\">";
echo __("Sản phẩm");
echo "</th>\n                                        <th class=\"text-center\">";
echo __("Thời gian");
echo "</th>\n                                    </tr>\n                                </thead>\n                                <tbody>\n                                    ";
foreach ($listDatatable as $order) {
    echo "                                    <tr>\n                                        <td class=\"text-center\">\n                                            <div class=\"form-check form-check-md d-flex align-items-center\">\n                                                <input type=\"checkbox\" class=\"form-check-input checkbox_product\"\n                                                    data-id=\"";
    echo $order["id"];
    echo "\" name=\"checkbox_product\"\n                                                    value=\"";
    echo $order["id"];
    echo "\" />\n                                            </div>\n                                        </td>\n                                        <td class=\"text-center\">\n\n                                            <button class=\"btn btn-info btn-sm shadow-info btn-wave\" id=\"btnViewOrder\"\n                                                onclick=\"viewOrder(`";
    echo $order["trans_id"];
    echo "`)\" data-toggle=\"tooltip\"\n                                                type=\"button\"><i class=\"fa-solid fa-eye\"></i></button>\n                                            <button class=\"btn btn-primary btn-sm shadow-primary btn-wave\"\n                                                onclick=\"downloadOrder(`";
    echo $order["trans_id"];
    echo "`)\"><i\n                                                    class=\"fa-solid fa-cloud-arrow-down\"></i></button>\n                                            <button type=\"button\" onclick=\"deleteOrder(`";
    echo $order["id"];
    echo "`)\"\n                                                id=\"btnDeleteOrder";
    echo $order["id"];
    echo "\"\n                                                class=\"btn btn-danger btn-sm shadow-danger btn-wave\">\n                                                <i class=\"fa-solid fa-trash\"></i>\n                                            </button>\n                                            <button class=\"btn btn-success btn-sm shadow-success btn-wave refund-button\"\n                                                data-id=\"";
    echo $order["id"];
    echo "\" data-amount=\"";
    echo $order["amount"];
    echo "\"\n                                                data-pay=\"";
    echo $order["pay"];
    echo "\" data-transid=\"";
    echo $order["trans_id"];
    echo "\"\n                                                ";
    echo $order["amount"] == 0 ? "disabled" : "";
    echo " type=\"button\">\n                                                <i class=\"fa-solid fa-rotate-left\"></i> Hoàn tiền\n                                            </button>\n                                        </td>\n                                        <td class=\"text-center\">\n                                            <a class=\"text-primary\"\n                                                href=\"";
    echo base_url_admin("user-edit&id=" . $order["buyer"]);
    echo "\">";
    echo getRowRealtime("users", $order["buyer"], "username");
    echo "                                                [ID ";
    echo $order["buyer"];
    echo "]</a>\n                                        </td>\n                                        <td>\n                                            Mã đơn hàng: #<strong>";
    echo $order["trans_id"];
    echo "</strong><br>\n                                            Mã đơn hàng API (nếu có): #<strong>";
    echo $order["api_transid"];
    echo "</strong><br>\n                                            ";
    if(checkPermission($getUser["admin"], "view_suppliers")) {
        echo "                                            Server API (nếu có):\n                                            <a\n                                                href=\"";
        echo base_url_admin("product-api-manager&id=" . $order["supplier_id"]);
        echo "\">";
        echo $order["supplier_id"] != 0 ? getRowRealtime("suppliers", $order["supplier_id"], "domain") : "";
        echo "</a><br>\n                                            ";
    }
    echo "                                        </td>\n                                        <td>\n                                            Số lượng: <strong>";
    echo format_cash($order["amount"]);
    echo "</strong><br>\n                                            Thanh toán: <strong\n                                                style=\"color:red;\">";
    echo format_currency($order["pay"]);
    echo "</strong><br>\n                                            Giá vốn: <strong\n                                                style=\"color:blue;\">";
    echo format_currency($order["cost"]);
    echo "</strong> -\n                                            Lãi: <strong\n                                                style=\"color:green;\">";
    echo format_currency($order["pay"] - $order["cost"]);
    echo "</strong><br>\n                                        </td>\n                                        <td>\n                                            <a class=\"text-primary\"\n                                                href=\"";
    echo base_url_admin("product-edit&id=" . $order["product_id"]);
    echo "\">";
    echo $order["product_name"];
    echo "</a>\n                                        </td>\n                                        <td class=\"text-center\">\n                                            <strong data-toggle=\"tooltip\" data-placement=\"bottom\"\n                                                title=\"";
    echo timeAgo(strtotime($order["create_gettime"]));
    echo "\">";
    echo $order["create_gettime"];
    echo "</strong>\n                                        </td>\n\n                                    </tr>\n                                    ";
}
echo "                                </tbody>\n                                <tfoot>\n                                    <tr>\n                                        <td colspan=\"3\">\n                                            <div class=\"btn-list\">\n                                                <button type=\"button\" id=\"btn_delete_product\"\n                                                    class=\"btn btn-outline-danger shadow-danger btn-wave btn-sm\"><i\n                                                        class=\"fa-solid fa-trash\"></i> XÓA ĐƠN HÀNG</button>\n                                            </div>\n                                        </td>\n                                        <td colspan=\"6\">\n                                            <div class=\"text-right\">\n                                                Tài khoản đã bán:\n                                                <strong>";
echo format_cash($CMSNT->get_row(" SELECT SUM(`amount`) FROM `product_order` WHERE `refund` = 0 AND " . $where . " ")["SUM(`amount`)"]);
echo "</strong>\n                                                |\n                                                Đơn hàng: <strong\n                                                    style=\"color: green;\">";
echo format_cash($totalDatatable);
echo "</strong> |\n                                                Doanh thu: <strong\n                                                    style=\"color:red;\">";
echo format_currency($CMSNT->get_row(" SELECT SUM(`pay`) FROM `product_order` WHERE `refund` = 0 AND " . $where . " ")["SUM(`pay`)"]);
echo "</strong>\n                                                |\n                                                Lợi nhuận: <strong\n                                                    style=\"color:blue;\">";
echo format_currency($CMSNT->get_row(" SELECT SUM(`pay`) FROM `product_order` WHERE `refund` = 0 AND " . $where . " ")["SUM(`pay`)"] - $CMSNT->get_row(" SELECT SUM(`cost`) FROM `product_order` WHERE `refund` = 0 AND " . $where . " ")["SUM(`cost`)"]);
echo "</strong>\n                                            </div>\n                                        </td>\n                                    </tr>\n                                </tfoot>\n                            </table>\n                        </div>\n                        <div class=\"row\">\n                            <div class=\"col-sm-12 col-md-5\">\n                                <p class=\"dataTables_info\">Showing ";
echo $limit;
echo " of ";
echo format_cash($totalDatatable);
echo "                                    Results</p>\n                            </div>\n                            <div class=\"col-sm-12 col-md-7 mb-3\">\n                                ";
echo $limit < $totalDatatable ? $urlDatatable : "";
echo "                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n\n";
require_once __DIR__ . "/footer.php";
echo "\n\n<!-- Modal Hoàn tiền -->\n<div class=\"modal fade\" id=\"refundModal\" tabindex=\"-1\" aria-labelledby=\"refundModalLabel\" aria-hidden=\"true\">\n    <div class=\"modal-dialog modal-dialog-centered modal-lg\">\n        <!-- modal-lg để rộng hơn, tùy ý -->\n        <div class=\"modal-content rounded-3 shadow\">\n\n            <!-- Tiêu đề Modal (header) -->\n            <div class=\"modal-header text-white\">\n                <!-- Tiêu đề sẽ được thiết lập động bằng JS -->\n                <h5 class=\"modal-title fw-bold\" id=\"refundModalLabel\"></h5>\n                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n            </div>\n\n            <!-- Phần nội dung (body) -->\n            <div class=\"modal-body py-4\">\n\n                <!-- Thông tin hoặc hướng dẫn (tùy chọn) -->\n                <div class=\"alert alert-info d-flex align-items-center\" role=\"alert\">\n                    <i class=\"fa-solid fa-circle-info me-2\"></i>\n                    <div>\n                        Vui lòng chọn <strong>Hoàn toàn bộ</strong> hoặc <strong>Hoàn một phần</strong> và nhập số lượng\n                        cần hoàn.\n                    </div>\n                </div>\n\n                <!-- Form chính -->\n                <form class=\"px-2\">\n\n                    <!-- Chọn kiểu hoàn tiền -->\n                    <div class=\"mb-3\">\n                        <label class=\"form-label fw-semibold d-block mb-2\">Chọn cách hoàn tiền <span class=\"text-danger\">*</span></label>\n                        <div class=\"form-check form-check-inline form-check-md\">\n                            <input class=\"form-check-input\" type=\"radio\" name=\"refundType\" id=\"refundFull\" value=\"full\"\n                                checked>\n                            <label class=\"form-check-label\" for=\"refundFull\">\n                                Hoàn toàn bộ\n                            </label>\n                        </div>\n                        <div class=\"form-check form-check-inline form-check-md\">\n                            <input class=\"form-check-input\" type=\"radio\" name=\"refundType\" id=\"refundPartial\"\n                                value=\"partial\">\n                            <label class=\"form-check-label\" for=\"refundPartial\">\n                                Hoàn một phần\n                            </label>\n                        </div>\n                    </div>\n\n                    <!-- Nhập số lượng khi hoàn một phần -->\n                    <div id=\"partialGroup\" style=\"display: none;\">\n                        <div class=\"mb-3\">\n                            <label for=\"partialQuantity\" class=\"form-label fw-semibold\">";
echo __("Số lượng cần hoàn");
echo " <span class=\"text-danger\">*</span></label>\n                            <div class=\"input-group\">\n                                <span class=\"input-group-text bg-light\" id=\"basic-addon1\">\n                                    <i class=\"fa-solid fa-hashtag\"></i>\n                                </span>\n                                <input type=\"number\" class=\"form-control\" id=\"partialQuantity\" name=\"partialQuantity\"\n                                    placeholder=\"Nhập số lượng cần hoàn\" min=\"1\" aria-describedby=\"basic-addon1\">\n                            </div>\n                            <small class=\"text-muted\">";
echo __("Không vượt quá tổng số lượng còn lại");
echo "</small>\n                        </div>\n                    </div>\n                    <div class=\"mb-3\">\n                        <label for=\"reason\" class=\"form-label fw-semibold\">";
echo __("Lý do hoàn tiền");
echo " <span class=\"text-danger\">*</span></label>\n                        <textarea class=\"form-control\" id=\"reason\" placeholder=\"Nhập nội dung hoàn tiền\"></textarea>\n                    </div>\n\n                    <!-- Hiển thị tổng số tiền hoàn -->\n                    <div class=\"mb-3\">\n                        <label for=\"refundAmount\" class=\"form-label fw-semibold\">";
echo __("Tổng số tiền hoàn");
echo "</label>\n                        <input type=\"text\" class=\"form-control\" id=\"refundAmount\" name=\"refundAmount\" placeholder=\"0\"\n                            disabled>\n                    </div>\n\n                    <!-- Hiển thị số lượng tài khoản hoàn -->\n                    <div class=\"mb-3\">\n                        <label for=\"refundCount\" class=\"form-label fw-semibold\">";
echo __("Số lượng tài khoản hoàn");
echo "</label>\n                        <div class=\"form-floating\">\n                            <input type=\"text\" class=\"form-control\" id=\"refundCount\" name=\"refundCount\" placeholder=\"0\"\n                                disabled>\n                            <label for=\"refundCount\">Tài khoản</label>\n                        </div>\n                    </div>\n\n                </form>\n                <div class=\"alert alert-warning d-flex align-items-center\" role=\"alert\">\n                    <i class=\"fa-solid fa-warning me-2\"></i>\n                    <div>\n                        ";
echo __("Hệ thống sẽ thu hồi hoa hồng nếu đơn hàng có phát sinh hoa hồng cho người giới thiệu.");
echo "                    </div>\n                </div>\n            </div>\n\n            <!-- Footer của Modal -->\n            <div class=\"modal-footer border-top-0\">\n                <button type=\"button\" class=\"btn btn-outline-secondary\" data-bs-dismiss=\"modal\">\n                    <i class=\"fa-solid fa-xmark me-1\"></i> Đóng\n                </button>\n                <button type=\"button\" class=\"btn btn-primary\" id=\"confirmRefund\">\n                    <i class=\"fa-solid fa-check me-1\"></i> Xác nhận hoàn tiền\n                </button>\n            </div>\n        </div>\n    </div>\n</div>\n\n\n<script>\n\$(document).ready(() => {\n    // ======== 1. Khai báo biến dùng chung ========\n    let orderId, orderAmount, orderPay, transId;\n\n    // Cache các selector thường dùng\n    const \$refundModal       = \$('#refundModal');\n    const \$refundModalLabel  = \$('#refundModalLabel');\n    const \$refundFull        = \$('#refundFull');\n    const \$refundPartial     = \$('#refundPartial');\n    const \$partialGroup      = \$('#partialGroup');\n    const \$partialQuantity   = \$('#partialQuantity');\n    const \$refundAmount      = \$('#refundAmount');\n    const \$refundCount       = \$('#refundCount');\n    const \$reason            = \$('#reason');\n    const \$confirmRefundBtn  = \$('#confirmRefund');\n    const \$tokenInput        = \$('#token'); // Token bảo mật\n    const originalBtnContent = \$confirmRefundBtn.html(); // Lưu html gốc của nút\n\n    // ======== 2. Hàm phụ ========\n    // (A) Đặt lý do hoàn tiền tương ứng\n    const setReason = (type) => {\n        if (type === 'partial') {\n            \$reason.val(`Hoàn tiền một phần đơn hàng #\${transId}`);\n        } else {\n            \$reason.val(`Hoàn tiền đơn hàng #\${transId}`);\n        }\n    };\n\n    // (B) Ẩn/Hiện khối nhập số lượng partial\n    const togglePartialGroup = (show) => {\n        if (show) {\n            \$partialGroup.show();\n        } else {\n            \$partialGroup.hide();\n        }\n    };\n\n    // ======== 3. Hàm tính tiền hoàn (AJAX) ========\n    const calculateRefund = (refundType, partialQuantity) => {\n        \$.ajax({\n            url: '";
echo base_url("ajaxs/admin/view.php");
echo "',\n            type: 'POST',\n            dataType: 'json',\n            data: {\n                action: 'tinh_tien_refund',\n                id: orderId,\n                refundType: refundType,\n                partialQuantity: partialQuantity\n            }\n        })\n        .done((response) => {\n            if (response.status === 'error') {\n                showMessage(response.msg, 'error');\n            } else {\n                \$refundAmount.val(response.totalRefund);\n                \$refundCount.val(refundType === 'full' ? orderAmount : partialQuantity);\n            }\n        })\n        .fail((xhr, status, error) => {\n            console.error('Lỗi khi tính toán số tiền hoàn:', error);\n        });\n    };\n\n    // ======== 4. Sự kiện click nút \"Hoàn tiền\" ========\n    \$('.refund-button').on('click', function() {\n        // Lấy dữ liệu từ nút\n        orderId     = \$(this).data('id');\n        orderAmount = parseFloat(\$(this).data('amount'));\n        orderPay    = parseFloat(\$(this).data('pay'));\n        transId     = \$(this).data('transid');\n\n        // Thiết lập Modal\n        \$refundModalLabel.html(\n            `<i class=\"fa-solid fa-rotate-left\"></i> Hoàn tiền đơn hàng #<b>\${transId}</b>`\n        );\n\n        // Reset form về trạng thái \"Hoàn toàn bộ\"\n        \$refundFull.prop('checked', true);\n        \$refundPartial.prop('checked', false);\n        togglePartialGroup(false);\n        \$partialQuantity.val('').attr('max', orderAmount);\n\n        \$refundAmount.val(orderPay.toFixed(2));\n        \$refundCount.val(orderAmount);\n        setReason('full');\n\n        // Tính toán tiền hoàn kiểu full\n        calculateRefund('full', orderAmount);\n\n        // Hiển thị Modal\n        \$refundModal.modal('show');\n    });\n\n    // ======== 5. Chọn \"Hoàn toàn bộ\" / \"Hoàn một phần\" ========\n    \$('input[name=\"refundType\"]').change(() => {\n        const isPartial = \$refundPartial.is(':checked');\n        if (isPartial) {\n            togglePartialGroup(true);\n            // Reset trường liên quan\n            \$refundAmount.val('');\n            \$partialQuantity.val('');\n            \$refundCount.val('');\n            setReason('partial');\n        } else {\n            togglePartialGroup(false);\n            calculateRefund('full', orderAmount);\n            setReason('full');\n        }\n    });\n\n    // ======== 6. Nhập số lượng hoàn một phần ========\n    \$partialQuantity.on('input', function() {\n        let quantity = parseInt(\$(this).val(), 10) || 0;\n        if (quantity > orderAmount) {\n            quantity = orderAmount;\n            \$(this).val(quantity);\n        }\n        // Hiển thị số lượng\n        \$refundCount.val(quantity);\n\n        // Tính tiền hoàn\n        calculateRefund('partial', quantity);\n    });\n\n    // ======== 7. Xác nhận hoàn tiền ========\n    \$confirmRefundBtn.on('click', () => {\n        // 1. Kiểm tra bắt buộc\n        const refundType      = \$('input[name=\"refundType\"]:checked').val();\n        const partialQuantity = parseInt(\$partialQuantity.val()) || 0;\n        const currentReason   = \$reason.val().trim();\n\n        // Nếu chọn \"Hoàn một phần\" mà chưa nhập hoặc nhập 0 => báo lỗi\n        if (refundType === 'partial' && partialQuantity < 1) {\n            showMessage('Vui lòng nhập số lượng tài khoản cần hoàn!', 'error');\n            return; // Dừng, không thực hiện tiếp\n        }\n\n        // Kiểm tra ô \"Lý do hoàn tiền\" - nếu trống => báo lỗi\n        if (!currentReason) {\n            showMessage('Vui lòng nhập lý do hoàn tiền!', 'error');\n            return;\n        }\n\n        // 2. Thông báo confirm\n        if (!confirm('Bạn có chắc chắn muốn hoàn tiền đơn hàng này?')) {\n            return; // Nếu người dùng bấm \"Hủy\", cũng dừng luôn\n        }\n\n        // 3. Bắt đầu xử lý hoàn tiền\n        // Vô hiệu hóa nút & hiển thị loading\n        \$confirmRefundBtn.prop('disabled', true).html('<i class=\"fa-solid fa-spinner fa-spin me-1\"></i> Đang xử lý...');\n\n        // Gửi AJAX hoàn tiền\n        \$.ajax({\n            url: '";
echo BASE_URL("ajaxs/admin/update.php");
echo "',\n            type: 'POST',\n            dataType: 'json',\n            data: {\n                token: \$tokenInput.val(),\n                action: 'refundOrder',\n                id: orderId,\n                refundType: refundType,\n                partialQuantity: partialQuantity,\n                reason: currentReason\n            }\n        })\n        .done((result) => {\n            showMessage(result.msg, result.status);\n            \$refundModal.modal('hide');\n            if (result.status === 'success') {\n                setTimeout(() => {\n                    location.reload();\n                }, 1000);\n            }\n        })\n        .fail(() => {\n            alert('Đã có lỗi xảy ra khi hoàn tiền!');\n        })\n        .always(() => {\n            // Khôi phục trạng thái nút\n            \$confirmRefundBtn.prop('disabled', false).html(originalBtnContent);\n        });\n    });\n\n});\n</script>\n\n\n\n\n\n\n<script>\n\$(function() {\n    \$('#check_all_checkbox_product').on('click', function() {\n        \$('.checkbox_product').prop('checked', this.checked);\n    });\n    \$('.checkbox_product').on('click', function() {\n        \$('#check_all_checkbox_product').prop('checked', \$('.checkbox_product:checked')\n            .length === \$('.checkbox_product').length);\n    });\n});\n\nfunction post_remove(id) {\n    \$.ajax({\n        url: \"";
echo BASE_URL("ajaxs/admin/remove.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            id: id,\n            token: \$(\"#token\").val(),\n            action: 'removeOrder'\n        },\n        success: function(result) {\n            if (result.status == 'success') {\n                showMessage(result.msg, result.status);\n            } else {\n                showMessage(result.msg, result.status);\n            }\n        },\n        error: function() {\n            alert(html(result));\n            location.reload();\n        }\n    });\n}\n\nfunction delete_records() {\n    var checkbox = document.getElementsByName('checkbox_product');\n\n    function postUpdatesSequentially(index) {\n        if (index < checkbox.length) {\n            if (checkbox[index].checked === true) {\n                post_remove(checkbox[index].value);\n            }\n            setTimeout(function() {\n                postUpdatesSequentially(index + 1);\n            }, 100);\n        } else {\n            Swal.fire({\n                title: \"Thành công!\",\n                text: \"Xóa đơn hàng thành công\",\n                icon: \"success\"\n            });\n            setTimeout(function() {\n                location.reload();\n            }, 1000);\n        }\n    }\n    postUpdatesSequentially(0);\n}\n\n\$(\"#btn_delete_product\").click(function() {\n    var checkboxes = document.querySelectorAll('input[name=\"checkbox_product\"]:checked');\n    if (checkboxes.length === 0) {\n        showMessage('Vui lòng chọn ít nhất một bản ghi', 'error');\n        return;\n    }\n    Swal.fire({\n        title: \"Bạn có chắc không?\",\n        text: \"Hệ thống sẽ xóa \" + checkboxes.length +\n            \" đơn hàng bạn chọn khi nhấn Đồng Ý\",\n        icon: \"warning\",\n        showCancelButton: true,\n        confirmButtonColor: \"#3085d6\",\n        cancelButtonColor: \"#d33\",\n        confirmButtonText: \"Đồng ý\",\n        cancelButtonText: \"Đóng\"\n    }).then((result) => {\n        if (result.isConfirmed) {\n            delete_records();\n        }\n    });\n});\n</script>\n\n<script type=\"text/javascript\">\nnew ClipboardJS(\".copy\");\n\nfunction copy() {\n    showMessage(\"";
echo __("Đã sao chép vào bộ nhớ tạm");
echo "\", 'success');\n}\n</script>\n\n<script>\nfunction downloadOrder(trans_id) {\n    Swal.fire({\n        title: \"";
echo __("Xác nhận tải đơn hàng");
echo "\",\n        text: \"";
echo __("Hệ thống sẽ tải về đơn hàng khi bạn nhấn đồng ý");
echo "\",\n        icon: \"warning\",\n        showCancelButton: true,\n        confirmButtonColor: \"#3085d6\",\n        cancelButtonColor: \"#d33\",\n        confirmButtonText: \"";
echo __("Đồng ý");
echo "\",\n        cancelButtonText: \"";
echo __("Đóng");
echo "\",\n    }).then((result) => {\n        if (result.isConfirmed) {\n            \$.ajax({\n                url: \"";
echo BASE_URL("ajaxs/admin/view.php");
echo "\",\n                method: \"POST\",\n                dataType: \"JSON\",\n                data: {\n                    action: 'download_order',\n                    trans_id: trans_id,\n                    token: \$(\"#token\").val(),\n                },\n                success: function(result) {\n                    if (result.status == 'success') {\n                        showMessage(result.msg, result.status);\n                        downloadTXT(result.filename, result.accounts);\n                    } else {\n                        showMessage(result.msg, result.status);\n                    }\n                },\n                error: function() {\n                    alert(html(result));\n                    location.reload();\n                }\n            });\n        }\n    });\n}\n\nfunction downloadTXT(filename, text) {\n    var element = document.createElement('a');\n    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));\n    element.setAttribute('download', filename);\n    element.style.display = 'none';\n    document.body.appendChild(element);\n    element.click();\n    document.body.removeChild(element);\n}\n</script>\n\n\n<script>\nfunction deleteOrder(id) {\n    const originalContent = \$('#btnDeleteOrder' + id)\n        .html(); // Save the original button content\n    \$('#btnDeleteOrder' + id).html(\n            '<span><i class=\"fa fa-spinner fa-spin\"></i></span>')\n        .prop('disabled', true);\n    Swal.fire({\n        title: \"";
echo __("Xác nhận xóa đơn hàng");
echo "\",\n        text: \"";
echo __("Hệ thống sẽ xóa đơn hàng khỏi hệ thống khi bạn nhấn đồng ý");
echo "\",\n        icon: \"warning\",\n        showCancelButton: true,\n        confirmButtonColor: \"#3085d6\",\n        cancelButtonColor: \"#d33\",\n        confirmButtonText: \"";
echo __("Đồng ý");
echo "\",\n        cancelButtonText: \"";
echo __("Đóng");
echo "\",\n    }).then((result) => {\n        if (result.isConfirmed) {\n            post_remove(id);\n            setTimeout(function() {\n                location.reload();\n            }, 500);\n        }\n    }).finally(() => {\n        \$('#btnDeleteOrder' + id).html(originalContent)\n            .prop('disabled', false);\n    });\n}\n</script>\n\n\n<div class=\"modal fade\" id=\"viewOrder\" tabindex=\"-1\" aria-labelledby=\"viewOrder\" data-bs-keyboard=\"false\"\n    aria-hidden=\"true\">\n    <!-- Scrollable modal -->\n    <div class=\"modal-dialog modal-dialog-centered modal-lg\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <h6 class=\"modal-title\" id=\"viewOrder\"><i class=\"fa-solid fa-eye\"></i> CHI TIẾT ĐƠN HÀNG\n                </h6>\n                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n            </div>\n            <div class=\"modal-body\">\n                <textarea class=\"form-control\" id=\"coypyBox\" readonly rows=\"10\"></textarea>\n            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" onclick=\"copy()\" data-clipboard-target=\"#coypyBox\"\n                    class=\"btn btn-danger shadow-danger btn-wave copy\">Sao chép</button>\n                <button type=\"button\" class=\"btn btn-light shadow-light btn-wave\" data-bs-dismiss=\"modal\">Đóng</button>\n            </div>\n        </div>\n    </div>\n</div>\n<script type=\"text/javascript\">\nfunction viewOrder(trans_id) {\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/admin/view.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'view_order',\n            token: '";
echo $getUser["token"];
echo "',\n            trans_id: trans_id\n        },\n        success: function(result) {\n            \$('#viewOrder').modal('show');\n            \$('#coypyBox').val(result.accounts);\n        },\n        error: function() {\n            alert(html(result));\n            location.reload();\n        }\n    });\n}\n</script>\n\n\n\n<div class=\"modal fade\" id=\"top_san_pham_ban_chay\" tabindex=\"-1\" aria-labelledby=\"top_san_pham_ban_chay\"\n    data-bs-keyboard=\"false\" aria-hidden=\"true\">\n    <!-- Scrollable modal -->\n    <div class=\"modal-dialog modal-dialog-centered modal-xl\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <h6 class=\"modal-title\" id=\"top_san_pham_ban_chay\"><i class=\"fa-solid fa-chart-line\"></i> TOP SẢN PHẨM\n                    BÁN CHẠY\n                </h6>\n                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n            </div>\n            <div class=\"modal-body\">\n                <div id=\"hien_thi_top_san_pham_ban_chay\"></div>\n            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-light shadow-light btn-wave\" data-bs-dismiss=\"modal\">Đóng</button>\n            </div>\n        </div>\n    </div>\n</div>\n<script type=\"text/javascript\">\nfunction top_san_pham_ban_chay() {\n    \$('#hien_thi_top_san_pham_ban_chay').html(\n        '<h5 class=\"mb-3 py-4 text-center\"><i class=\"fa fa-spinner fa-spin\"></i> Đang phân tích dữ liệu, vui lòng chờ...</h5>'\n    );\n    \$('#top_san_pham_ban_chay').modal('show');\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/admin/view.php");
echo "\",\n        method: \"POST\",\n        data: {\n            action: 'top_san_pham_ban_chay',\n            token: '";
echo $getUser["token"];
echo "'\n        },\n        success: function(result) {\n            \$('#hien_thi_top_san_pham_ban_chay').html(result);\n        },\n        error: function() {\n            \$('#hien_thi_top_san_pham_ban_chay').html(result);\n        }\n    });\n}\n</script>";

?>