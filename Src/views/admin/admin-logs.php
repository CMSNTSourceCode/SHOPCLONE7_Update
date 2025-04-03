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
$body = ["title" => "Admin Logs", "desc" => "Admin Activity Logs", "keyword" => "admin logs, activity logs"];
$body["header"] = "\n \n";
$body["footer"] = "";
require_once __DIR__ . "/../../models/is_admin.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";
require_once __DIR__ . "/nav.php";
if($getUser["admin"] != 99999) {
    exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back();}</script>");
}
if(isset($_GET["limit"])) {
    $limit = (int) check_string($_GET["limit"]);
} else {
    $limit = 10;
}
if(isset($_GET["page"])) {
    $page = (int) check_string($_GET["page"]);
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `id` > 0 ";
$create_gettime = "";
$urlParam = "";
$methodParam = "";
$ipParam = "";
if(!empty($_GET["url"])) {
    $urlParam = check_string($_GET["url"]);
    $where .= " AND `request_url` LIKE '%" . $urlParam . "%' ";
}
if(!empty($_GET["method"])) {
    $methodParam = check_string($_GET["method"]);
    $where .= " AND `request_method` = '" . $methodParam . "' ";
}
if(!empty($_GET["ip"])) {
    $ipParam = check_string($_GET["ip"]);
    $where .= " AND `ip` LIKE '%" . $ipParam . "%' ";
}
if(!empty($_GET["create_gettime"])) {
    $create_gettime = check_string($_GET["create_gettime"]);
    $create_gettime_1 = str_replace("-", "/", $create_gettime);
    $create_gettime_1 = explode(" to ", $create_gettime_1);
    if($create_gettime_1[0] != $create_gettime_1[1]) {
        $create_gettime_1 = [$create_gettime_1[0] . " 00:00:00", $create_gettime_1[1] . " 23:59:59"];
        $where .= " AND `timestamp` >= '" . $create_gettime_1[0] . "' AND `timestamp` <= '" . $create_gettime_1[1] . "' ";
    }
}
$listDatatable = $CMSNT->get_list(" \n    SELECT * \n    FROM `admin_request_logs` \n    WHERE " . $where . " \n    ORDER BY `timestamp` DESC \n    LIMIT " . $from . ", " . $limit . " \n");
$totalDatatable = $CMSNT->num_rows("\n    SELECT * \n    FROM `admin_request_logs` \n    WHERE " . $where . "\n");
$urlDatatable = pagination(base_url_admin("admin-logs&limit=" . $limit . "&url=" . $urlParam . "&method=" . $methodParam . "&ip=" . $ipParam . "&create_gettime=" . $create_gettime . "&"), $from, $totalDatatable, $limit);
echo "\n<div class=\"main-content app-content\">\n    <div class=\"container-fluid\">\n        <div class=\"d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb\">\n            <h1 class=\"page-title fw-semibold fs-18 mb-0\">\n                <i class=\"fa-solid fa-clock-rotate-left\"></i> Nhật ký hoạt động Admin\n            </h1>\n        </div>\n        <div class=\"row\">\n            <div class=\"col-xl-12\">\n                <div class=\"card custom-card\">\n                    <div class=\"card-header justify-content-between\">\n                        <div class=\"card-title\">\n                            ADMIN REQUEST LOGS\n                        </div>\n                    </div>\n                    <div class=\"card-body\">\n\n                        <!-- FORM TÌM KIẾM -->\n                        <form action=\"\" class=\"align-items-center mb-3\" name=\"formSearch\" method=\"GET\">\n                            <input type=\"hidden\" name=\"module\" value=\"admin\">\n                            <input type=\"hidden\" name=\"action\" value=\"admin-logs\">\n\n                            <div class=\"row row-cols-lg-auto g-3 mb-3\">\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <input class=\"form-control form-control-sm\" value=\"";
echo $urlParam;
echo "\"\n                                        name=\"url\" placeholder=\"Request URL\">\n                                </div>\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <select name=\"method\" class=\"form-select form-select-sm\">\n                                        <option value=\"\">-- Method --</option>\n                                        <option ";
echo $methodParam == "GET" ? "selected" : "";
echo " value=\"GET\">GET</option>\n                                        <option ";
echo $methodParam == "POST" ? "selected" : "";
echo " value=\"POST\">POST</option>\n                                        <option ";
echo $methodParam == "PUT" ? "selected" : "";
echo " value=\"PUT\">PUT</option>\n                                        <option ";
echo $methodParam == "DELETE" ? "selected" : "";
echo " value=\"DELETE\">DELETE</option>\n                                    </select>\n                                </div>\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <input class=\"form-control form-control-sm\" value=\"";
echo $ipParam;
echo "\"\n                                        name=\"ip\" placeholder=\"IP Address\">\n                                </div>\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <input type=\"text\" name=\"create_gettime\" class=\"form-control form-control-sm\"\n                                        id=\"daterange\" value=\"";
echo $create_gettime;
echo "\" placeholder=\"Chọn thời gian\">\n                                </div>\n                                <div class=\"col-12\">\n                                    <button class=\"btn btn-hero btn-sm btn-primary\">\n                                        <i class=\"fa fa-search\"></i> Tìm kiếm\n                                    </button>\n                                    <a class=\"btn btn-hero btn-sm btn-danger\"\n                                        href=\"";
echo base_url_admin("admin-logs");
echo "\">\n                                        <i class=\"fa fa-trash\"></i> Clear filter\n                                    </a>\n                                </div>\n                            </div>\n\n                            <div class=\"top-filter\">\n                                <div class=\"filter-show\">\n                                    <label class=\"filter-label\">Show :</label>\n                                    <select name=\"limit\" onchange=\"this.form.submit()\"\n                                        class=\"form-select filter-select\">\n                                        <option ";
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
echo " value=\"500\">500</option>\n                                    </select>\n                                </div>\n                            </div>\n                        </form>\n                        <!-- END FORM TÌM KIẾM -->\n\n                        <!-- BẢNG KẾT QUẢ -->\n                        <div class=\"table-responsive table-wrapper mb-3\">\n                            <table class=\"table text-nowrap table-striped table-hover table-bordered\">\n                                <thead>\n                                    <tr>\n                                        <th width=\"5%\">#</th>\n                                        <th width=\"10%\">User ID</th>\n                                        <th>Request URL</th>\n                                        <th width=\"10%\">Method</th>\n                                        <th>Params</th>\n                                        <th width=\"15%\">IP</th>\n                                        <th width=\"15%\">Time</th>\n                                    </tr>\n                                </thead>\n                                <tbody>\n                                    ";
if($listDatatable) {
    echo "                                    ";
    foreach ($listDatatable as $row) {
        echo "                                    <tr>\n                                        <td>";
        echo $row["id"];
        echo "</td>\n                                        <td><a class=\"text-primary\" href=\"";
        echo base_url_admin("user-edit&id=" . $row["user_id"]);
        echo "\">";
        echo getRowRealtime("users", $row["user_id"], "username");
        echo "                                                [ID ";
        echo $row["user_id"];
        echo "]</a>\n                                        </td>\n                                        <td>\n                                            <code>";
        echo htmlspecialchars($row["request_url"]);
        echo "</code>\n                                        </td>\n                                        <td>\n                                            <span class=\"badge bg-";
        echo strtoupper($row["request_method"]) == "GET" ? "info" : "warning";
        echo "\">\n                                                ";
        echo $row["request_method"];
        echo "                                            </span>\n                                        </td>\n                                        <td>\n                                            <pre class=\"mb-0\">";
        echo htmlspecialchars(json_encode(json_decode($row["request_params"]), JSON_PRETTY_PRINT));
        echo "</pre>\n                                        </td>\n                                        <td>\n                                            <span class=\"badge bg-light text-dark\">";
        echo $row["ip"];
        echo "</span>\n                                        </td>\n                                        <td>\n                                            <span class=\"badge bg-light text-dark\" data-bs-toggle=\"tooltip\"\n                                                title=\"";
        echo timeAgo(strtotime($row["timestamp"]));
        echo "\">\n                                                ";
        echo $row["timestamp"];
        echo "                                            </span>\n                                        </td>\n                                    </tr>\n                                    ";
    }
    echo "                                    ";
} else {
    echo "                                    <tr>\n                                        <td colspan=\"7\" class=\"text-center\">Không có dữ liệu</td>\n                                    </tr>\n                                    ";
}
echo "                                </tbody>\n                            </table>\n                        </div>\n\n                        <!-- PHÂN TRANG -->\n                        <div class=\"row\">\n                            <div class=\"col-sm-12 col-md-5\">\n                                <p class=\"dataTables_info\">\n                                    Hiển thị ";
echo $limit;
echo " / ";
echo format_cash($totalDatatable);
echo " kết quả\n                                </p>\n                            </div>\n                            <div class=\"col-sm-12 col-md-7 mb-3\">\n                                ";
echo $limit < $totalDatatable ? $urlDatatable : "";
echo "                            </div>\n                        </div>\n                        <!-- END PHÂN TRANG -->\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n";
require_once __DIR__ . "/footer.php";

?>