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
$body = ["title" => __("Recharge Tmweasyapi Thailand") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n<link rel=\"stylesheet\" href=\"" . BASE_URL("public/client/") . "css/wallet.css\">\n";
$body["footer"] = "\n \n";
require_once __DIR__ . "/../../models/is_user.php";
if($CMSNT->site("tmweasyapi_status") != 1) {
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
$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_tmweasyapi` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_tmweasyapi` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(base_url("?action=recharge-tmweasyapi&limit=" . $limit . "&shortByDate=" . $shortByDate . "&time=" . $time . "&trans_id=" . $trans_id . "&amount=" . $amount . "&"), $from, $totalDatatable, $limit);
echo "\n\n<section class=\"py-5 inner-section profile-part\">\n    <div class=\"container\">\n        <div class=\"row\">\n            <div class=\"col-md-7\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Tmweasyapi Thailand");
echo "</h4>\n                    <div class=\"text-center mb-4\">\n                        <img width=\"300px\" src=\"";
echo base_url("mod/img/logo-tmweasyapi.webp");
echo "\" />\n                    </div>\n                    <div class=\"row mb-3\">\n                        <label class=\"col-sm-4 col-form-label\"\n                            for=\"example-hf-email\">";
echo __("Enter the deposit amount: (฿)");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <input type=\"hidden\" class=\"form-control\" id=\"token\" value=\"";
echo $getUser["token"];
echo "\">\n                            <input type=\"text\" class=\"form-control\" id=\"amount\"\n                                placeholder=\"";
echo __("Please enter the amount to deposit");
echo "\" required>\n                        </div>\n                    </div>\n                    <center>\n                        <div class=\"wallet-form\">\n                            <button type=\"button\" id=\"btnSubmit\">";
echo __("Submit");
echo "</button>\n                        </div>\n                    </center>\n                </div>\n            </div>\n            <div class=\"col-md-5\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Lưu ý");
echo "</h4>\n                    ";
echo $CMSNT->site("tmweasyapi_notice");
echo "                </div>\n            </div>\n            <div class=\"col-md-12\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Lịch sử nạp Tmweasyapi Thailand");
echo "</h4>\n                    <form action=\"";
echo base_url();
echo "\" method=\"GET\">\n                        <input type=\"hidden\" name=\"action\" value=\"recharge-tmweasyapi\">\n                        <div class=\"row\">\n                            <div class=\"col-lg col-md-4 col-6\">\n                                <input class=\"form-control col-sm-2 mb-1\" value=\"";
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
echo base_url("?action=recharge-tmweasyapi");
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
echo format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `payment_tmweasyapi` WHERE " . $where . " AND `status` = 1 ")["SUM(`price`)"]);
echo "</strong>\n\n                                        </div>\n                                    </td>\n                                </tr>\n                            </tfoot>\n                        </table>\n                    </div>\n                    <div class=\"bottom-paginate\">\n                        <p class=\"page-info\">Showing ";
echo $limit;
echo " of ";
echo $totalDatatable;
echo " Results</p>\n                        <div class=\"pagination\">\n                            ";
echo $limit < $totalDatatable ? $urlDatatable : "";
echo "                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</section>\n\n\n\n";
require_once __DIR__ . "/footer.php";
echo "\n\n<!-- Modal Payment Info -->\n<div class=\"modal fade\" id=\"paymentModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"paymentModalLabel\" aria-hidden=\"true\">\n    <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header bg-primary text-white\" >\n                <h5 class=\"modal-title\" id=\"paymentModalLabel\" style=\"color:white;\">\n                    <i class=\"fas fa-qrcode me-2\"></i>";
echo __("Top up via PromptPay (QR CODE)");
echo "                </h5>\n                <button type=\"button\" class=\"close text-white\" data-dismiss=\"modal\" aria-label=\"Close\">\n                    <span aria-hidden=\"true\">&times;</span>\n                </button>\n            </div>\n            <div class=\"modal-body text-center p-4\">\n                <div class=\"payment-status mb-4\">\n                    <div class=\"spinner-border text-primary mb-3\" role=\"status\">\n                        <span class=\"sr-only\">Loading...</span>\n                    </div>\n                    <h4 class=\"text-primary mb-2\">";
echo __("Waiting for Payment");
echo "</h4>\n                    <p class=\"text-muted\">";
echo __("Please scan PromptPay QR code to complete payment");
echo "</p>\n                </div>\n                \n                <div class=\"qr-container mb-4\">\n                    <div class=\"qr-wrapper p-3 bg-light rounded\">\n                        <img id=\"qrImage\" src=\"\" alt=\"QR Code\" class=\"img-fluid\" style=\"max-width: 200px;\">\n                    </div>\n                    <div class=\"mt-2\">\n                        <button class=\"btn btn-sm btn-outline-primary\" onclick=\"copyQR()\">\n                            <i class=\"fas fa-copy\"></i> ";
echo __("Copy QR Code");
echo "                        </button>\n                    </div>\n                </div>\n\n                <div class=\"payment-details bg-light p-3 rounded mb-4\">\n                    <div class=\"row\">\n                        <div class=\"col-6\">\n                            <div class=\"payment-info-item\">\n                                <small class=\"text-muted d-block\">";
echo __("Amount");
echo "</small>\n                                <h4 class=\"text-danger mb-0\" id=\"paymentAmount\"></h4>\n                            </div>\n                        </div>\n                        <div class=\"col-6\">\n                            <div class=\"payment-info-item\">\n                                <small class=\"text-muted d-block\">";
echo __("Time Remaining");
echo "</small>\n                                <h4 class=\"text-warning mb-0\" id=\"timeRemaining\"></h4>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n\n                <div class=\"payment-actions\">\n                    <button type=\"button\" class=\"btn btn-danger btn-block\" id=\"btnCancelPayment\" data-dismiss=\"modal\">\n                        <i class=\"fas fa-times\"></i> ";
echo __("Cancel Payment");
echo "                    </button>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n<style>\n.modal-content {\n    border: none;\n    border-radius: 15px;\n    box-shadow: 0 0 20px rgba(0,0,0,0.1);\n}\n\n.modal-header {\n    border-radius: 15px 15px 0 0;\n    padding: 1.5rem;\n}\n\n.modal-body {\n    padding: 2rem;\n}\n\n.qr-wrapper {\n    border: 2px dashed #dee2e6;\n    transition: all 0.3s ease;\n}\n\n.qr-wrapper:hover {\n    border-color: #007bff;\n    background-color: #f8f9fa;\n}\n\n.payment-info-item {\n    padding: 0.5rem;\n}\n\n.payment-info-item small {\n    font-size: 0.8rem;\n}\n\n.payment-info-item h4 {\n    font-size: 1.2rem;\n    font-weight: 600;\n}\n\n.btn-lg {\n    padding: 0.8rem 1.5rem;\n    font-size: 1rem;\n    font-weight: 500;\n}\n\n.spinner-border {\n    width: 3rem;\n    height: 3rem;\n}\n</style>\n\n<script type=\"text/javascript\">\n \n// Xử lý nút Cancel Payment\n\$('#btnCancelPayment').on('click', function() {\n    \$('#paymentModal').modal('hide');\n});\n\nfunction copyQR() {\n    const qrImage = document.getElementById('qrImage');\n    const canvas = document.createElement('canvas');\n    const context = canvas.getContext('2d');\n    canvas.width = qrImage.width;\n    canvas.height = qrImage.height;\n    context.drawImage(qrImage, 0, 0);\n    \n    canvas.toBlob(function(blob) {\n        const item = new ClipboardItem({ \"image/png\": blob });\n        navigator.clipboard.write([item]).then(function() {\n            Swal.fire({\n                icon: 'success',\n                title: '";
echo __("Copied!");
echo "',\n                text: '";
echo __("QR code has been copied to clipboard");
echo "',\n                timer: 1500,\n                showConfirmButton: false\n            });\n        });\n    });\n}\n\n\$(\"#btnSubmit\").on(\"click\", function() {\n    \$('#btnSubmit').html('";
echo __("Please wait...");
echo "').prop('disabled', true);\n    \$.ajax({\n        url: \"";
echo BASE_URL("ajaxs/client/create.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'RechargeTmweasyapi',\n            token: \$(\"#token\").val(),\n            amount: \$(\"#amount\").val()\n        },\n        success: function(response) {\n            if (response.status == 'success') {\n                // Hiển thị thông tin trong modal\n                \$('#qrImage').attr('src', 'data:image/png;base64,' + response.qr);\n                \$('#paymentAmount').text(response.amount + ' THB');\n                \$('#paymentUrl').attr('href', response.invoice_url);\n                \n                // Xử lý đếm ngược thời gian\n                let timeLeft = parseInt(response.time_out);\n                const timerDisplay = \$('#timeRemaining');\n                \n                function updateTimer() {\n                    const minutes = Math.floor(timeLeft / 60);\n                    const seconds = timeLeft % 60;\n                    timerDisplay.text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);\n                    \n                    if (timeLeft <= 0) {\n                        clearInterval(timer);\n                        timerDisplay.text('";
echo __("Time expired");
echo "');\n                        timerDisplay.addClass('text-danger');\n                        \$('#paymentModal').modal('hide');\n                        Swal.fire({\n                            icon: 'error',\n                            title: '";
echo __("Time expired");
echo "',\n                            text: '";
echo __("Payment time has expired. Please create a new payment.");
echo "',\n                            confirmButtonText: '";
echo __("OK");
echo "'\n                        });\n                    }\n                    timeLeft--;\n                }\n                \n                // Cập nhật ngay lập tức\n                updateTimer();\n                \n                // Cập nhật mỗi giây\n                const timer = setInterval(updateTimer, 1000);\n                \n                // Hiển thị modal\n                \$('#paymentModal').modal('show');\n                \n                // Xử lý khi modal đóng\n                \$('#paymentModal').on('hidden.bs.modal', function () {\n                    clearInterval(timer);\n                });\n            } else {\n                Swal.fire(\n                    '";
echo __("Error");
echo "',\n                    response.msg,\n                    'error'\n                );\n            }\n            \$('#btnSubmit').html('";
echo __("Submit");
echo "').prop('disabled', false);\n        }\n    })\n});\n</script>\n\n\n<script>\nfunction loadData() {\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/client/view.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'notication_topup_tmweasyapi',\n            token: '";
echo $getUser["token"];
echo "'\n        },\n        success: function(respone) {\n            // Nếu thành công\n            if (respone.status == 'success') {\n                // Tắt modal thanh toán\n                \$('#paymentModal').modal('hide');\n                \n                Swal.fire({\n                    icon: 'success',\n                    title: '";
echo __("Thành công !");
echo "',\n                    text: respone.msg,\n                    showDenyButton: true,\n                    confirmButtonText: '";
echo __("Nạp Thêm");
echo "',\n                    denyButtonText: `";
echo __("Mua Ngay");
echo "`,\n                }).then((result) => {\n                    if (result.isConfirmed) {\n                        // Người dùng bấm \"Nạp Thêm\" => reload trang\n                        location.reload();\n                    } else if (result.isDenied) {\n                        // Người dùng bấm \"Mua Ngay\" => chuyển hướng\n                        window.location.href = '";
echo base_url();
echo "';\n                    } else {\n                        // Nếu họ đóng Swal mà không chọn gì (hoặc 'dismiss'),\n                        // thì 5 giây sau gọi lại loadData.\n                        setTimeout(loadData, 5000);\n                    }\n                });\n            } else {\n                // Nếu status != 'success' => không hiển thị Swal\n                // hoặc bạn có thể hiện thông báo lỗi\n                // Sau đó 5 giây mới load lại\n                setTimeout(loadData, 5000);\n            }\n        },\n        error: function() {\n            // Nếu Ajax lỗi => 5 giây sau gọi lại loadData\n            setTimeout(loadData, 5000);\n        }\n    });\n}\n\n// Lần đầu gọi hàm\nloadData();\n</script>\n\n\n\n<script>\nDashmix.helpersOnLoad(['js-flatpickr', 'jq-datepicker', 'jq-maxlength', 'jq-select2', 'jq-rangeslider',\n    'jq-masked-inputs', 'jq-pw-strength'\n]);\n</script>";

?>