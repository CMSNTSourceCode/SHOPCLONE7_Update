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
$body = ["title" => __("Recharge OpenPix") . " | " . $CMSNT->site("title"), "desc" => $CMSNT->site("description"), "keyword" => $CMSNT->site("keywords")];
$body["header"] = "\n<link rel=\"stylesheet\" href=\"" . BASE_URL("public/client/") . "css/wallet.css\">\n";
$body["footer"] = "\n \n";
require_once __DIR__ . "/../../models/is_user.php";
if($CMSNT->site("openpix_status") != 1) {
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
$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_openpix` WHERE " . $where . " ORDER BY `id` DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_openpix` WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(base_url("?action=recharge-openpix&limit=" . $limit . "&shortByDate=" . $shortByDate . "&time=" . $time . "&trans_id=" . $trans_id . "&amount=" . $amount . "&"), $from, $totalDatatable, $limit);
echo "\n\n<section class=\"py-5 inner-section profile-part\">\n    <div class=\"container\">\n        <div class=\"row\">\n            <div class=\"col-md-7\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("OpenPix");
echo "</h4>\n                    <div class=\"text-center mb-4\">\n                        <svg version=\"1.1\" id=\"OpenPixLogo\" xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\" width=\"140\" viewBox=\"0 0 670.49 140.22\"><g fill=\"#96969A\" fill-rule=\"nonzero\" id=\"open\"><polygon points=\"469.7,34.9 469.6,35.2 469.8,35\"></polygon><path d=\"M264.8,59.4c0,8.5-1.2,15.9-3.7,22.4c-2.5,6.5-5.8,11.9-10,16.2c-4.2,4.3-9.1,7.6-14.7,9.8 c-5.6,2.2-11.5,3.3-17.8,3.3c-6.3,0-12.2-1.1-17.8-3.3c-5.6-2.2-10.5-5.4-14.7-9.8c-4.2-4.3-7.6-9.7-10-16.2 c-2.5-6.5-3.7-13.9-3.7-22.4c0-8.5,1.2-15.9,3.7-22.3c2.5-6.4,5.8-11.8,10-16.1c4.2-4.3,9.1-7.6,14.7-9.8 c5.6-2.2,11.5-3.3,17.8-3.3c6.3,0,12.2,1.1,17.8,3.3c5.6,2.2,10.5,5.4,14.7,9.8c4.2,4.3,7.6,9.7,10,16.1 C263.5,43.5,264.8,51,264.8,59.4L264.8,59.4z M254.5,59.4c0-6.6-0.9-12.6-2.6-17.9c-1.7-5.3-4.1-9.8-7.3-13.5 c-3.1-3.7-6.9-6.5-11.3-8.5c-4.4-1.9-9.4-2.9-14.9-2.9c-5.5,0-10.5,1-14.9,2.9c-4.4,1.9-8.2,4.8-11.3,8.5 c-3.1,3.7-5.6,8.2-7.3,13.5c-1.7,5.3-2.6,11.2-2.6,17.9c0,6.7,0.9,12.6,2.6,17.9c1.7,5.3,4.1,9.8,7.3,13.5 c3.1,3.7,6.9,6.6,11.3,8.5c4.4,2,9.4,3,14.9,3c5.5,0,10.5-1,14.9-3c4.4-2,8.2-4.8,11.3-8.5c3.1-3.7,5.6-8.2,7.3-13.5 C253.6,72,254.5,66.1,254.5,59.4L254.5,59.4z\"></path><path d=\"M289.7,135.2h-9.3V37.8c2.8-0.9,6.3-1.9,10.5-2.7c4.3-0.9,9.7-1.3,16.2-1.3c5.4,0,10.3,0.9,14.7,2.6 c4.4,1.8,8.2,4.3,11.4,7.6c3.2,3.3,5.6,7.4,7.4,12.1c1.8,4.7,2.6,10.1,2.6,16.1c0,5.6-0.7,10.8-2.2,15.5 c-1.5,4.7-3.6,8.7-6.5,12.1c-2.8,3.4-6.3,6-10.5,7.9c-4.1,1.9-8.9,2.8-14.2,2.8c-4.8,0-9-0.7-12.6-2.1c-3.6-1.4-6.2-2.7-7.8-3.9 V135.2z M289.7,95.6c0.9,0.7,1.9,1.4,3.2,2.1c1.3,0.8,2.8,1.5,4.5,2.1c1.7,0.7,3.6,1.2,5.6,1.6c2,0.4,4.1,0.6,6.3,0.6 c4.6,0,8.4-0.8,11.5-2.4c3.1-1.6,5.6-3.7,7.5-6.4c1.9-2.7,3.3-5.9,4.1-9.5c0.9-3.7,1.3-7.5,1.3-11.6c0-9.7-2.4-17.1-7.3-22.3 c-4.8-5.2-11.3-7.8-19.2-7.8c-4.6,0-8.2,0.2-11,0.6c-2.8,0.4-4.9,0.9-6.3,1.4V95.6z\"></path><path d=\"M353.7,72c0-6.5,0.9-12.1,2.8-16.9c1.9-4.8,4.3-8.8,7.3-12c3-3.2,6.4-5.6,10.3-7.2c3.8-1.6,7.8-2.4,11.9-2.4 c9,0,16.2,2.9,21.6,8.8c5.4,5.9,8.1,15,8.1,27.2c0,0.8,0,1.5-0.1,2.3c0,0.8-0.1,1.5-0.2,2.1h-51.7c0.3,9,2.5,16,6.7,20.8 c4.2,4.8,10.8,7.3,19.9,7.3c5,0,9-0.5,12-1.4c2.9-0.9,5.1-1.8,6.4-2.4l1.7,8c-1.3,0.8-3.8,1.7-7.5,2.7c-3.7,1-8,1.6-12.9,1.6 c-6.5,0-12-1-16.5-2.9c-4.6-1.9-8.3-4.6-11.3-8c-2.9-3.4-5.1-7.5-6.4-12.2C354.3,82.7,353.7,77.6,353.7,72L353.7,72z M406,66.1 c-0.2-7.7-2-13.7-5.4-18c-3.4-4.3-8.3-6.4-14.5-6.4c-3.3,0-6.3,0.7-8.9,2c-2.6,1.3-4.9,3.1-6.8,5.3c-1.9,2.2-3.5,4.8-4.6,7.8 c-1.1,2.9-1.7,6-1.9,9.3H406z\"></path><path d=\"M430.7,37.8c2.8-0.8,6.5-1.6,11.1-2.6c4.7-0.9,10.4-1.4,17.1-1.4c5.6,0,10.3,0.8,14,2.4 c3.8,1.6,6.7,3.9,9,6.9c2.2,3,3.8,6.6,4.8,10.8c0.9,4.2,1.4,8.8,1.4,13.8v41.2h-9.3V70.7c0-5.2-0.4-9.6-1.1-13.3 c-0.7-3.6-1.9-6.6-3.6-8.8c-1.7-2.3-3.8-3.9-6.6-4.9c-2.7-1-6.1-1.5-10.2-1.5c-4.4,0-8.1,0.2-11.3,0.7c-3.2,0.5-5.3,0.9-6.2,1.3 v64.7h-9.3V37.8z\"></path></g><g fill=\"#4AB7A8\" fill-rule=\"nonzero\" id=\"pix\"><path d=\"M532.1,19.1c12.8,0,22.3,2.4,28.5,7.3c6.3,4.9,9.4,11.8,9.4,20.7c0,5.1-0.9,9.5-2.7,13.1 c-1.8,3.6-4.4,6.5-7.8,8.7c-3.4,2.2-7.5,3.8-12.4,4.8c-4.9,1-10.4,1.5-16.6,1.5h-12.2v34.9h-8.9V21.6c3.1-0.9,6.8-1.5,11-1.9 C524.6,19.3,528.5,19.1,532.1,19.1z M532.5,26.8c-3.3,0-6.1,0.1-8.4,0.3c-2.3,0.2-4.3,0.4-5.9,0.6v39.9h11.2 c4.8,0,9.1-0.3,13-0.8c3.9-0.6,7.2-1.6,9.9-3.1c2.7-1.5,4.8-3.6,6.3-6.3c1.5-2.7,2.2-6.1,2.2-10.3c0-4-0.8-7.3-2.4-9.9 c-1.6-2.6-3.7-4.7-6.4-6.2c-2.6-1.5-5.7-2.6-9.1-3.2C539.6,27.1,536.1,26.8,532.5,26.8z\"></path><path d=\"M599,22.5c0,2-0.6,3.6-1.8,4.8c-1.2,1.2-2.7,1.8-4.4,1.8c-1.7,0-3.2-0.6-4.4-1.8c-1.2-1.2-1.8-2.8-1.8-4.8 c0-2,0.6-3.6,1.8-4.8c1.2-1.2,2.7-1.8,4.4-1.8c1.7,0,3.2,0.6,4.4,1.8C598.3,19,599,20.5,599,22.5z M597,110.2h-8.5V42.9h8.5 V110.2z\"></path><path d=\"M643,81.7c-1.9,2.3-3.8,4.7-5.7,7.2c-1.9,2.5-3.7,5-5.5,7.4c-1.8,2.4-3.4,4.9-4.9,7.4 c-1.5,2.5-2.7,4.7-3.7,6.6h-8.8c3.6-6.6,7.5-12.8,11.6-18.4c4.1-5.6,8.2-11.1,12.5-16.5l-22.8-32.4h9.9l17.6,25.5l17.6-25.5h9.2 l-22.3,32c1.9,2.4,3.9,4.9,6.1,7.7c2.2,2.8,4.3,5.7,6.5,8.7c2.2,3,4.2,6.1,6.3,9.3c2,3.2,3.9,6.4,5.6,9.6h-9.1 c-1-1.9-2.3-4-3.8-6.3c-1.5-2.3-3.1-4.7-4.9-7.2c-1.8-2.5-3.7-5-5.6-7.6C646.8,86.5,644.9,84,643,81.7z\"></path></g><g><path fill=\"#1F6D61\" d=\"M134.8,93.9c-0.8-1.2-1.9-2.2-3.3-3l-11.4-6.2l-7.9-4.3l-7.9,4.3l7.9,4.3l9.9,5.4c2.9,1.6,2.9,5.1,0,6.7 l-43.8,24c-3.5,1.9-8.2,1.9-11.7,0l-43.8-24.1c-2.9-1.6-2.9-5.1,0-6.7l9.8-5.4l7.9-4.3l-7.9-4.3l-7.9,4.3l-11.4,6.2 c-2.8,1.6-4.5,4.1-4.5,6.9c0,1.4,0.4,2.7,1.2,3.8c0.8,1.2,1.9,2.2,3.3,3l53.3,29.2c3.5,1.9,8.2,1.9,11.7,0l53.2-29.2 c2.8-1.6,4.5-4.1,4.5-6.9C136,96.4,135.6,95.1,134.8,93.9z\"></path><path fill=\"#308E83\" d=\"M134.8,67.7c-0.8-1.2-1.9-2.2-3.3-3l-11.4-6.2l-7.9-4.3l-7.9,4.3l7.9,4.3l9.9,5.4c2.9,1.6,2.9,5.1,0,6.7 l-9.8,5.4l-7.9,4.3L78.3,99c-3.5,1.9-8.2,1.9-11.7,0L40.5,84.7l-7.9-4.3l-9.9-5.4c-2.9-1.6-2.9-5.1,0-6.7l9.8-5.4l7.9-4.3 l-7.9-4.3l-7.9,4.3l-11.4,6.2c-2.8,1.6-4.5,4.1-4.5,6.9c0,1.4,0.4,2.7,1.2,3.9c0.8,1.2,1.9,2.2,3.3,3l11.4,6.2l7.9,4.3l34,18.6 c3.5,1.9,8.2,1.9,11.7,0L112.2,89l7.9-4.3l11.4-6.2c2.8-1.6,4.5-4.1,4.5-6.9C136,70.2,135.6,68.9,134.8,67.7z\"></path><path fill=\"#4AB7A8\" d=\"M134.8,41.6c-0.8-1.2-1.9-2.2-3.3-3L78.3,9.5c-3.5-1.9-8.2-1.9-11.7,0L13.3,38.6c-2.8,1.6-4.5,4.1-4.5,6.9 c0,1.4,0.4,2.7,1.2,3.8c0.8,1.2,1.9,2.2,3.3,3l11.4,6.2l7.9,4.3l34,18.6c3.5,1.9,8.2,1.9,11.7,0l33.9-18.6l7.9-4.3l11.4-6.2 c2.8-1.6,4.5-4.1,4.5-6.9C136,44.1,135.6,42.8,134.8,41.6z M122.1,48.8l-9.8,5.4l-7.9,4.3l-26,14.3c-3.5,1.9-8.2,1.9-11.7,0 L40.5,58.5l-7.9-4.3l-9.9-5.4c-2.9-1.6-2.9-5.1,0-6.7l43.8-24c3.5-1.9,8.2-1.9,11.7,0l43.8,24.1C125,43.8,125,47.2,122.1,48.8z\"></path><g fill=\"#1F6D61\"><path d=\"M65.8,44.1c-3.3-2-6.6-3.9-9.9-5.9c-1.2-0.7-2.4-1.1-3.8-1c-0.8-0.1-1.5,0.1-2.2,0.5 c-3.7,2.2-7.5,4.4-11.3,6.7c-1.4,0.8-1.4,2,0,2.8c3.7,2.1,7.4,4.1,11.1,6.3c2.2,1.3,4.4,1.1,6.6,0c3.2-1.8,6.3-3.7,9.5-5.5 C67.6,46.9,67.6,45.2,65.8,44.1z M58.4,46.4l-5.6,3.1c-0.2,0.1-0.5,0.1-0.8,0l-5.6-3.2c-0.5-0.3-0.5-1.1,0-1.4l5.7-3.2 c0.2-0.1,0.6-0.1,0.8,0l5.5,3.2C58.9,45.4,58.9,46.1,58.4,46.4z\"></path><path d=\"M86.3,56.4L74.1,49c-0.8-0.5-1.7-0.5-2.5,0L59,56.2c-0.9,0.5-0.9,1.8,0,2.3l11.3,6.6c1.5,0.8,3.2,0.8,4.7,0 l11.3-6.5C87.2,58.2,87.2,56.9,86.3,56.4z M78.9,58l-6,3.4c-0.2,0.1-0.4,0.1-0.6,0l-6-3.5c-0.4-0.2-0.4-0.8,0-1.1l5.8-3.3 c0.4-0.2,0.8-0.2,1.1,0L79,57C79.4,57.2,79.4,57.8,78.9,58z\"></path><path d=\"M86.6,33.1l-12.3-7.9c-0.8-0.5-1.8-0.5-2.6,0L59,32.3c-1.2,0.7-1.4,2.6-0.2,3.3l11.9,7c1.2,0.7,2.8,0.7,4,0 l11.8-6.4C87.7,35.5,87.7,33.8,86.6,33.1z M78.6,34.9l-5,2.7c-0.5,0.3-1.2,0.3-1.7,0l-5.1-3c-0.5-0.3-0.4-1.1,0.1-1.4l5.4-3 c0.3-0.2,0.8-0.2,1.1,0l5.3,3.4C79.1,33.9,79.1,34.6,78.6,34.9z\"></path><path d=\"M87.1,52.3l3.5,2.2c0.9,0.7,2.1,0.7,3.1,0.1l3.3-2c0.8-0.5,0.8-1.6,0-2.1l-4-2.5c-0.4-0.2-0.8-0.2-1.2,0 l-4.7,2.6C86.6,50.9,86.5,51.8,87.1,52.3z\"></path><path d=\"M96.7,46.7l3.4,2.2c0.9,0.6,2.1,0.6,3,0.1l3.6-2.2c0.5-0.3,0.6-1.1,0-1.5l-3.7-2.3c-0.7-0.5-1.7-0.5-2.4,0 l-3.8,2.2C96.2,45.5,96.1,46.3,96.7,46.7z\"></path><path d=\"M87.6,41l4.2,2.9c0.7,0.5,1.5,0.5,2.2,0l4.3-2.8c0.4-0.3,0.4-0.8,0-1.1l-4.5-2.8c-0.6-0.4-1.3-0.4-1.9,0 l-4.3,2.6C87.2,40.2,87.2,40.7,87.6,41z\"></path></g></g></svg>\n                    </div>\n                    <div class=\"row mb-3\">\n                        <label class=\"col-sm-4 col-form-label\"\n                            for=\"example-hf-email\">";
echo __("Enter the deposit amount: (R\$)");
echo "</label>\n                        <div class=\"col-sm-8\">\n                            <input type=\"hidden\" class=\"form-control\" id=\"token\" value=\"";
echo $getUser["token"];
echo "\">\n                            <input type=\"text\" class=\"form-control\" id=\"amount\"\n                                placeholder=\"";
echo __("Please enter the amount to deposit");
echo "\" required>\n                        </div>\n                    </div>\n                    <center>\n                        <div class=\"wallet-form\">\n                            <button type=\"button\" id=\"btnSubmit\">";
echo __("Submit");
echo "</button>\n                        </div>\n                    </center>\n                </div>\n            </div>\n            <div class=\"col-md-5\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Lưu ý");
echo "</h4>\n                    ";
echo $CMSNT->site("openpix_notice");
echo "                </div>\n            </div>\n            <div class=\"col-md-12\">\n                <div class=\"account-card\">\n                    <h4 class=\"account-title\">";
echo __("Lịch sử nạp OpenPix");
echo "</h4>\n                    <form action=\"";
echo base_url();
echo "\" method=\"GET\">\n                        <input type=\"hidden\" name=\"action\" value=\"recharge-openpix\">\n                        <div class=\"row\">\n                            <div class=\"col-lg col-md-4 col-6\">\n                                <input class=\"form-control col-sm-2 mb-1\" value=\"";
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
echo base_url("?action=recharge-openpix");
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
    echo "</b></td>\n                                    <td class=\"text-center\">R\$ <b>";
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
echo format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `payment_openpix` WHERE " . $where . " AND `status` = 1 ")["SUM(`price`)"]);
echo "</strong>\n\n                                        </div>\n                                    </td>\n                                </tr>\n                            </tfoot>\n                        </table>\n                    </div>\n                    <div class=\"bottom-paginate\">\n                        <p class=\"page-info\">";
echo __("Showing");
echo " ";
echo $limit;
echo " ";
echo __("of");
echo " ";
echo $totalDatatable;
echo " ";
echo __("Results");
echo "</p>\n                        <div class=\"pagination\">\n                            ";
echo $limit < $totalDatatable ? $urlDatatable : "";
echo "                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</section>\n\n\n\n";
require_once __DIR__ . "/footer.php";
echo "\n\n<script type=\"text/javascript\">\n\$(\"#btnSubmit\").on(\"click\", function() {\n    \$('#btnSubmit').html('";
echo __("Please wait...");
echo "').prop('disabled',\n        true);\n    \$.ajax({\n        url: \"";
echo BASE_URL("ajaxs/client/create.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'RechargeOpenPix',\n            token: \$(\"#token\").val(),\n            amount: \$(\"#amount\").val()\n        },\n        success: function(respone) {\n            if (respone.status == 'success') {\n                window.open(respone.invoice_url, \"_self\");\n            } else {\n                Swal.fire(\n                    '";
echo __("Error");
echo "',\n                    respone.msg,\n                    'error'\n                );\n            }\n            \$('#btnSubmit').html('";
echo __("Submit");
echo "')\n                .prop('disabled', false);\n        }\n    })\n});\n</script>\n\n\n<script>\nfunction loadData() {\n    \$.ajax({\n        url: \"";
echo base_url("ajaxs/client/view.php");
echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'notication_topup_openpix',\n            token: '";
echo $getUser["token"];
echo "'\n        },\n        success: function(respone) {\n            if (respone.status == 'success') {\n                Swal.fire({\n                    icon: 'success',\n                    title: '";
echo __("Thành công !");
echo "',\n                    text: respone.msg,\n                    showDenyButton: true,\n                    confirmButtonText: '";
echo __("Nạp Thêm");
echo "',\n                    denyButtonText: `";
echo __("Mua Ngay");
echo "`,\n                }).then((result) => {\n                    if (result.isConfirmed) {\n                        location.reload();\n                    } else if (result.isDenied) {\n                        window.location.href = '";
echo base_url();
echo "';\n                    }\n                });\n            }\n            setTimeout(loadData, 5000);\n        },\n        error: function() {\n            setTimeout(loadData, 5000);\n        }\n    });\n}\nloadData();\n</script>\n\n\n\n<script>\nDashmix.helpersOnLoad(['js-flatpickr', 'jq-datepicker', 'jq-maxlength', 'jq-select2', 'jq-rangeslider',\n    'jq-masked-inputs', 'jq-pw-strength'\n]);\n</script>";

?>