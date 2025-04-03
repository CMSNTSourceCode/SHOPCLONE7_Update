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
$body = ["title" => __("Recharge Korapay") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n<link rel=\"stylesheet\" href=\"" . BASE_URL("public/client/") . "css/wallet.css\">\n";
$body["footer"] = "\n \n";
require_once __DIR__ . "/../../models/is_user.php";
if($CMSNT->site("korapay_status") != 1) {
    redirect(base_url());
}
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/nav.php";
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
$where = " `user_id` = '" . $getUser["id"] . "' AND `status` = 1 ";
$shortByDate = "";
$trans_id = "";
$time = "";
$amount = "";
if(!empty($_GET["trans_id"])) {
    $trans_id = check_string($_GET["trans_id"]);
    $where .= " AND `trans_id` = \"" . $trans_id . "\" ";
}
if(!empty($_GET["amount"])) {
    $amount = check_string($_GET["amount"]);
    $where .= " AND `amount` = " . $amount . " ";
}
if(!empty($_GET["time"])) {
    $time = check_string($_GET["time"]);
    $create_gettime_1 = str_replace("-", "/", $time);
    $create_gettime_1 = explode(" to ", $create_gettime_1);
    if($create_gettime_1[0] != $create_gettime_1[1]) {
        $create_gettime_1 = [$create_gettime_1[0] . " 00:00:00", $create_gettime_1[1] . " 23:59:59"];
        $where .= " AND `created_at` >= '" . $create_gettime_1[0] . "' AND `created_at` <= '" . $create_gettime_1[1] . "' ";
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
        $where .= " AND `created_at` LIKE '%" . $currentDate . "%' ";
    }
    if($shortByDate == 2) {
        $where .= " AND YEAR(created_at) = " . $currentYear . " AND WEEK(created_at, 1) = " . $currentWeek . " ";
    }
    if($shortByDate == 3) {
        $where .= " AND MONTH(created_at) = '" . $currentMonth . "' AND YEAR(created_at) = '" . $currentYear . "' ";
    }
}
$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_korapay` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_korapay` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(base_url("?action=recharge-korapay&limit=" . $limit . "&shortByDate=" . $shortByDate . "&time=" . $time . "&trans_id=" . $trans_id . "&amount=" . $amount . "&"), $from, $totalDatatable, $limit);
echo "\n\n<section class=\"py-5 inner-section profile-part\">\n    <div class=\"container\">\n        <div class=\"row\">\n            <div class=\"col-md-7\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Recharge Korapay");
echo "</h4>\n                    <div class=\"text-center mb-4\">\n                        <img width=\"300px\" src=\"";
echo base_url("mod/img/icon-korapay.webp");
echo "\" />\n                    </div>\n                    <div class=\"row mb-3\">\n                        <label class=\"col-sm-4 col-form-label\"\n                            for=\"example-hf-email\">";
echo __("Enter the deposit amount: (" . $CMSNT->site("korapay_currency_code") . ")");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <input type=\"hidden\" class=\"form-control\" id=\"token\" value=\"";
echo $getUser["token"];
echo "\">\n                            <input type=\"text\" class=\"form-control\" id=\"amount\"\n                                placeholder=\"";
echo __("Please enter the amount to deposit");
echo "\" required>\n                        </div>\n                    </div>\n                    <center>\n                        <div class=\"wallet-form\">\n                            <button type=\"button\" id=\"btnSubmit\">";
echo __("Submit");
echo "</button>\n                        </div>\n                    </center>\n                </div>\n            </div>\n            <div class=\"col-md-5\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Lưu ý");
echo "</h4>\n                    ";
echo $CMSNT->site("korapay_notice");
echo "                </div>\n            </div>\n            <div class=\"col-md-12\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Lịch sử nạp Korapay");
echo "</h4>\n                    <form action=\"";
echo base_url();
echo "\" method=\"GET\">\n                        <input type=\"hidden\" name=\"action\" value=\"recharge-korapay\">\n                        <div class=\"row\">\n                            <div class=\"col-lg col-md-4 col-6\">\n                                <input class=\"form-control col-sm-2 mb-1\" value=\"";
echo $trans_id;
echo "\" name=\"trans_id\"\n                                    placeholder=\"";
echo __("Search transaction id");
echo "\">\n                            </div>\n                            <div class=\"col-lg col-md-4 col-6\">\n                                <input class=\"form-control col-sm-2 mb-1\" value=\"";
echo $amount;
echo "\" name=\"amount\"\n                                    placeholder=\"";
echo __("Search amount");
echo "\">\n                            </div>\n                            <div class=\"col-lg col-md-6 col-6\">\n                                <input type=\"text\" class=\"js-flatpickr form-control mb-1\" id=\"example-flatpickr-range\"\n                                    name=\"time\" placeholder=\"";
echo __("Chọn thời gian cần tìm");
echo "\" value=\"";
echo $time;
echo "\"\n                                    data-mode=\"range\">\n                            </div>\n                            <div class=\"col-lg col-md-4 col-6\">\n                                <button class=\"shop-widget-btn mb-2\"><i\n                                        class=\"fas fa-search\"></i><span>";
echo __("Tìm kiếm");
echo "</span></button>\n                            </div>\n                            <div class=\"col-lg col-md-4 col-6\">\n                                <a href=\"";
echo base_url("?action=recharge-korapay");
echo "\" class=\"shop-widget-btn mb-2\"><i\n                                        class=\"far fa-trash-alt\"></i><span>";
echo __("Bỏ lọc");
echo "</span></a>\n                            </div>\n                        </div>\n                        <div class=\"top-filter\">\n                            <div class=\"filter-show\"><label class=\"filter-label\">Show :</label>\n                                <select name=\"limit\" onchange=\"this.form.submit()\" class=\"form-select filter-select\">\n                                    <option ";
echo $limit == 5 ? "selected" : "";
echo " value=\"5\">5</option>\n                                    <option ";
echo $limit == 10 ? "selected" : "";
echo " value=\"10\">10</option>\n                                    <option ";
echo $limit == 20 ? "selected" : "";
echo " value=\"20\">20</option>\n                                    <option ";
echo $limit == 50 ? "selected" : "";
echo " value=\"50\">50</option>\n                                    <option ";
echo $limit == 100 ? "selected" : "";
echo " value=\"100\">100</option>\n                                    <option ";
echo $limit == 500 ? "selected" : "";
echo " value=\"500\">500</option>\n                                    <option ";
echo $limit == 1000 ? "selected" : "";
echo " value=\"1000\">1000</option>\n                                </select>\n                            </div>\n                            <div class=\"filter-short\">\n                                <label class=\"filter-label\">";
echo __("Short by Date:");
echo "</label>\n                                <select name=\"shortByDate\" onchange=\"this.form.submit()\"\n                                    class=\"form-select filter-select\">\n                                    <option value=\"\">";
echo __("Tất cả");
echo "</option>\n                                    <option ";
echo $shortByDate == 1 ? "selected" : "";
echo " value=\"1\">";
echo __("Hôm nay");
echo "                                    </option>\n                                    <option ";
echo $shortByDate == 2 ? "selected" : "";
echo " value=\"2\">";
echo __("Tuần này");
echo "                                    </option>\n                                    <option ";
echo $shortByDate == 3 ? "selected" : "";
echo " value=\"3\">";
echo __("Tháng này");
echo "                                    </option>\n                                </select>\n                            </div>\n                        </div>\n                    </form>\n                    <div class=\"table-scroll\">\n                        <table class=\"table fs-sm mb-0\">\n                            <thead>\n                                <tr>\n                                    <th class=\"text-center\">";
echo __("TransID");
echo "</th>\n                                    <th class=\"text-center\">";
echo __("Amount");
echo "</th>\n                                    <th class=\"text-center\">";
echo __("Price");
echo "</th>\n                                    <th class=\"text-center\">";
echo __("Status");
echo "</th>\n                                    <th class=\"text-center\">";
echo __("Create date");
echo "</th>\n                                    <th class=\"text-center\">";
echo __("Action");
echo "</th>\n                                </tr>\n                            </thead>\n                            <tbody>\n                                ";
foreach ($listDatatable as $row) {
    echo "                                <tr>\n                                    <td class=\"text-center\"><b>";
    echo $row["trans_id"];
    echo "</b></td>\n                                    <td class=\"text-center\"><b>";
    echo $row["amount"];
    echo "</b></td>\n                                    <td class=\"text-center\"><b\n                                            style=\"color: red;\">";
    echo format_currency($row["price"]);
    echo "</b></td>\n                                    <td class=\"text-center\">";
    echo display_invoice($row["status"]);
    echo "</td>\n                                    <td class=\"text-center\"><i class=\"far fa-calendar-alt mr-2 text-secondary\"></i>\n                                        ";
    echo $row["created_at"];
    echo "</td>\n                                    <td class=\"text-center\">\n                                        <a class=\"btn btn-primary btn-sm\" target=\"_blank\"\n                                            href=\"";
    echo $row["checkout_url"];
    echo "\">\n                                            <i class=\"fas fa-credit-card mr-2\"></i>\n                                            ";
    echo __("Pay Now");
    echo "                                        </a>\n                                    </td>\n                                </tr>\n                                ";
}
echo "                            </tbody>\n                            <tfoot>\n                                <tr>\n                                    <td colspan=\"7\">\n                                        <div class=\"float-right\">\n                                            ";
echo __("Paid:");
echo "                                            <strong\n                                                style=\"color:red;\">";
echo format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `payment_korapay` WHERE " . $where . " AND `status` = 1 ")["SUM(`price`)"]);
echo "</strong>\n\n                                        </div>\n                                    </td>\n                                </tr>\n                            </tfoot>\n                        </table>\n                    </div>\n                    <div class=\"bottom-paginate\">\n                        <p class=\"page-info\">Showing ";
echo $limit;
echo " of ";
echo $totalDatatable;
echo " Results</p>\n                        <div class=\"pagination\">\n                            ";
echo $limit < $totalDatatable ? $urlDatatable : "";
echo "                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</section>\n\n\n\n";
require_once __DIR__ . "/footer.php";
echo "\n\n<script type=\"text/javascript\">\n\$(\"#btnSubmit\").on(\"click\", function() {\n    \$('#btnSubmit').html('";
echo __("Please wait...");
echo "').prop('disabled',\n        true);\n    \$.ajax({\n        url: \"";
echo BASE_URL("ajaxs/client/create.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'RechargeKorapay',\n            token: \$(\"#token\").val(),\n            amount: \$(\"#amount\").val()\n        },\n        success: function(respone) {\n            if (respone.status == 'success') {\n                window.open(respone.invoice_url, \"_self\");\n            } else {\n                Swal.fire(\n                    '";
echo __("Error");
echo "',\n                    respone.msg,\n                    'error'\n                );\n            }\n            \$('#btnSubmit').html('";
echo __("Submit");
echo "')\n                .prop('disabled', false);\n        }\n    })\n});\n</script>\n\n\n\n<script>\nfunction loadData() {\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/client/view.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'notication_topup_korapay',\n            token: '";
echo $getUser["token"];
echo "'\n        },\n        success: function(respone) {\n            // Nếu thành công\n            if (respone.status == 'success') {\n                Swal.fire({\n                    icon: 'success',\n                    title: '";
echo __("Thành công !");
echo "',\n                    text: respone.msg,\n                    showDenyButton: true,\n                    confirmButtonText: '";
echo __("Nạp Thêm");
echo "',\n                    denyButtonText: `";
echo __("Mua Ngay");
echo "`,\n                }).then((result) => {\n                    if (result.isConfirmed) {\n                        // Người dùng bấm \"Nạp Thêm\" => reload trang\n                        location.reload();\n                    } else if (result.isDenied) {\n                        // Người dùng bấm \"Mua Ngay\" => chuyển hướng\n                        window.location.href = '";
echo base_url();
echo "';\n                    } else {\n                        setTimeout(loadData, 5000);\n                    }\n                });\n            } else {\n                setTimeout(loadData, 5000);\n            }\n        },\n        error: function() {\n            // Nếu Ajax lỗi => 5 giây sau gọi lại loadData\n            setTimeout(loadData, 5000);\n        }\n    });\n}\n\n// Lần đầu gọi hàm\nloadData();\n</script>\n\n\n\n<script>\nDashmix.helpersOnLoad(['js-flatpickr', 'jq-datepicker', 'jq-maxlength', 'jq-select2', 'jq-rangeslider',\n    'jq-masked-inputs', 'jq-pw-strength'\n]);\n</script>";

?>