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
$body = ["title" => "Telegram Logs", "desc" => "CMSNT Panel", "keyword" => "cmsnt, CMSNT, cmsnt.co"];
$body["header"] = "\n \n";
$body["footer"] = "";
require_once __DIR__ . "/../../models/is_admin.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";
require_once __DIR__ . "/nav.php";
if(!checkPermission($getUser["admin"], "view_telegram_logs")) {
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
$usernameParam = "";
$commandParam = "";
$typeParam = "";
if(!empty($_GET["username"])) {
    $usernameParam = check_string($_GET["username"]);
    $where .= " AND `username` LIKE '%" . $usernameParam . "%' ";
}
if(!empty($_GET["command"])) {
    $commandParam = check_string($_GET["command"]);
    $where .= " AND `command` LIKE '%" . $commandParam . "%' ";
}
if(!empty($_GET["type"])) {
    $typeParam = check_string($_GET["type"]);
    $where .= " AND `type` = '" . $typeParam . "' ";
}
$listDatatable = $CMSNT->get_list(" \n    SELECT * \n    FROM `telegram_logs` \n    WHERE " . $where . " \n    ORDER BY `id` DESC \n    LIMIT " . $from . ", " . $limit . " \n");
$totalDatatable = $CMSNT->num_rows("\n    SELECT * \n    FROM `telegram_logs` \n    WHERE " . $where . "\n");
$urlDatatable = pagination(base_url_admin("telegram-logs&limit=" . $limit . "&username=" . $usernameParam . "&command=" . $commandParam . "&type=" . $typeParam . "&"), $from, $totalDatatable, $limit);
echo "\n<div class=\"main-content app-content\">\n    <div class=\"container-fluid\">\n        <div class=\"d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb\">\n            <h1 class=\"page-title fw-semibold fs-18 mb-0\">\n                <i class=\"fa-solid fa-clock-rotate-left\"></i> Nhật ký sử dụng Bot quản lý hệ thống\n            </h1>\n        </div>\n        <div class=\"row\">\n            <div class=\"col-xl-12\">\n                <div class=\"card custom-card\">\n                    <div class=\"card-header justify-content-between\">\n                        <div class=\"card-title\">\n                            TELEGRAM LOGS\n                        </div>\n                    </div>\n                    <div class=\"card-body\">\n\n                        <!-- FORM TÌM KIẾM -->\n                        <form action=\"\" class=\"align-items-center mb-3\" name=\"formSearch\" method=\"GET\">\n                            <input type=\"hidden\" name=\"module\" value=\"admin\">\n                            <input type=\"hidden\" name=\"action\" value=\"telegram-logs\">\n\n                            <div class=\"row row-cols-lg-auto g-3 mb-3\">\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <input class=\"form-control form-control-sm\" value=\"";
echo $usernameParam;
echo "\"\n                                        name=\"username\" placeholder=\"Username\">\n                                </div>\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <input class=\"form-control form-control-sm\" value=\"";
echo $commandParam;
echo "\"\n                                        name=\"command\" placeholder=\"Command\">\n                                </div>\n                                <div class=\"col-lg col-md-4 col-6\">\n                                    <select name=\"type\" class=\"form-select form-select-sm\">\n                                        <option value=\"\">-- Loại --</option>\n                                        <option ";
echo $typeParam == "message" ? "selected" : "";
echo " value=\"message\">Message\n                                        </option>\n                                        <option ";
echo $typeParam == "callback" ? "selected" : "";
echo " value=\"callback\">Callback\n                                        </option>\n                                    </select>\n                                </div>\n                                <div class=\"col-12\">\n                                    <button class=\"btn btn-hero btn-sm btn-primary\">\n                                        <i class=\"fa fa-search\"></i> Tìm kiếm\n                                    </button>\n                                    <a class=\"btn btn-hero btn-sm btn-danger\"\n                                        href=\"";
echo base_url_admin("telegram-logs");
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
echo " value=\"500\">500</option>\n                                        <option ";
echo $limit == 1000 ? "selected" : "";
echo " value=\"1000\">1.000</option>\n                                        <option ";
echo $limit == 5000 ? "selected" : "";
echo " value=\"5000\">5.000</option>\n                                        <option ";
echo $limit == 10000 ? "selected" : "";
echo " value=\"10000\">10.000</option>\n                                    </select>\n                                </div>\n                            </div>\n                        </form>\n                        <!-- END FORM TÌM KIẾM -->\n\n                        <!-- BẢNG KẾT QUẢ -->\n                        <div class=\"table-responsive table-wrapper mb-3\">\n                            <table class=\"table text-nowrap table-striped table-hover table-bordered\">\n                                <thead>\n                                    <tr>\n                                        <th width=\"5%\">#</th>\n                                        <th width=\"15%\">Username Telegram</th>\n                                        <th width=\"15%\">Command</th>\n                                        <th>Params</th>\n                                        <th width=\"10%\">Type</th>\n                                        <th width=\"15%\">Time</th>\n                                    </tr>\n                                </thead>\n                                <tbody>\n                                    ";
if($listDatatable) {
    echo "                                    ";
    foreach ($listDatatable as $row) {
        echo "                                    <tr>\n                                        <td>";
        echo $row["id"];
        echo "</td>\n                                        <td>\n                                            <b>\n                                                <a href=\"https://t.me/";
        echo htmlspecialchars($row["username"]);
        echo "\"\n                                                    target=\"_blank\" class=\"text-primary\">\n                                                    @";
        echo htmlspecialchars($row["username"]);
        echo "                                                </a>\n                                            </b>\n                                        </td>\n\n                                        <td>/";
        echo htmlspecialchars($row["command"]);
        echo "</td>\n                                        <td>\n                                            <code>";
        echo htmlspecialchars($row["params"]);
        echo "</code>\n                                        </td>\n                                        <td>\n                                            ";
        if($row["type"] == "message") {
            echo "                                            <span class=\"badge bg-primary\">";
            echo htmlspecialchars($row["type"]);
            echo "</span>\n                                            ";
        } else {
            echo "                                            <span class=\"badge bg-info\">";
            echo htmlspecialchars($row["type"]);
            echo "</span>\n                                            ";
        }
        echo "                                        </td>\n                                        <td>\n                                            <span class=\"badge bg-light text-dark\" data-bs-toggle=\"tooltip\"\n                                                title=\"";
        echo timeAgo(strtotime($row["time"]));
        echo "\">\n                                                ";
        echo $row["time"];
        echo "                                            </span>\n                                        </td>\n                                    </tr>\n                                    ";
    }
    echo "                                    ";
} else {
    echo "                                    <tr>\n                                        <td colspan=\"6\" class=\"text-center\">Không có dữ liệu</td>\n                                    </tr>\n                                    ";
}
echo "                                </tbody>\n                            </table>\n                        </div>\n\n                        <!-- PHÂN TRANG -->\n                        <div class=\"row\">\n                            <div class=\"col-sm-12 col-md-5\">\n                                <p class=\"dataTables_info\">\n                                    Hiển thị ";
echo $limit;
echo " / ";
echo format_cash($totalDatatable);
echo " kết quả\n                                </p>\n                            </div>\n                            <div class=\"col-sm-12 col-md-7 mb-3\">\n                                ";
echo $limit < $totalDatatable ? $urlDatatable : "";
echo "                            </div>\n                        </div>\n                        <!-- END PHÂN TRANG -->\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n\n";
require_once __DIR__ . "/footer.php";

?>