<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../libs/db.php";
require_once __DIR__ . "/../../libs/lang.php";
require_once __DIR__ . "/../../libs/helper.php";
require_once __DIR__ . "/../../libs/database/users.php";
require_once __DIR__ . "/../../models/is_admin.php";
if(!isset($_POST["action"])) {
    $data = json_encode(["status" => "error", "msg" => "The Request Not Found"]);
    exit($data);
}
if($CMSNT->site("status_demo") != 0) {
    exit(json_encode(["status" => "error", "msg" => __("Chức năng này không thể sử dụng trên website demo")]));
}
if($_POST["action"] == "cap_nhat_san_pham_nhanh") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $id = (int) check_string($_POST["id"]);
    if(!($product = $CMSNT->get_row(" SELECT * FROM `products` WHERE `id` = " . $id . " "))) {
        exit(json_encode(["status" => "error", "msg" => "Sản phẩm không tồn tại trong hệ thống"]));
    }
    if(!empty($_POST["category_id"])) {
        $isUpdate = $CMSNT->update("products", ["category_id" => check_string($_POST["category_id"])], " `id` = " . $id . " ");
    }
    if(!empty($_POST["status"])) {
        $status = check_string($_POST["status"]);
        if($status == "ON") {
            $status = 1;
        } else {
            $status = 0;
        }
        $isUpdate = $CMSNT->update("products", ["status" => $status], " `id` = " . $id . " ");
    }
    if(!empty($_POST["discount"])) {
        $discount = check_string($_POST["discount"]);
        $isUpdate = $CMSNT->update("products", ["discount" => $discount], " `id` = " . $id . " ");
    }
    if(isset($isUpdate)) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Cập nhật nhanh sản phẩm (ID " . $id . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Không có sản phẩm nào được thay đổi")]));
}
if($_POST["action"] == "reset_total_money_users") {
    if(!checkPermission($getUser["admin"], "edit_user")) {
        exit(json_encode(["status" => "error", "msg" => __("Bạn không có quyền sử dụng tính năng này")]));
    }
    $isUpdate = $CMSNT->update("users", ["total_money" => 0], " `total_money` > 0 ");
    if(isset($isUpdate)) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Reset tổng nạp toàn bộ thành viên"]);
        exit(json_encode(["status" => "success", "msg" => "Reset tổng nạp toàn bộ user thành công!"]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Reset thất bại")]));
}
if($_POST["action"] == "update_status_user") {
    if(!checkPermission($getUser["admin"], "edit_user")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    if(!($user = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '" . check_string($_POST["id"]) . "' "))) {
        exit(json_encode(["status" => "error", "msg" => "Thành viên không tồn tại trong hệ thống"]));
    }
    $isUpdate = $CMSNT->update("users", ["banned" => !empty($_POST["status"]) ? check_string($_POST["status"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Update Status User (" . $user["username"] . " - " . $user["id"] . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "update_product_code_product_stock") {
    if(!checkPermission($getUser["admin"], "edit_stock_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    if(!($row = $CMSNT->get_row(" SELECT * FROM `product_stock` WHERE `id` = '" . check_string($_POST["id"]) . "' "))) {
        exit(json_encode(["status" => "error", "msg" => "Tài khoản không tồn tại trong hệ thống"]));
    }
    $isUpdate = $CMSNT->update("product_stock", ["product_code" => !empty($_POST["product_code"]) ? check_string($_POST["product_code"]) : $row["product_code"]], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Cập nhật mã kho hàng cho tài khoản (" . $row["uid"] . ")"]);
        exit(json_encode(["status" => "success", "msg" => "Cập nhật kho hàng tài khoản " . $row["uid"] . " thành công!"]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật kho hàng tài khoản " . $row["uid"] . " thất bại")]));
}
if($_POST["action"] == "refundOrder") {
    if(!checkPermission($getUser["admin"], "refund_orders_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    if(empty($_POST["id"])) {
        exit(json_encode(["status" => "error", "msg" => "ID đơn hàng không tồn tại"]));
    }
    $id = check_string($_POST["id"]);
    if(!($product_order = $CMSNT->get_row(" SELECT * FROM `product_order` WHERE `id` = '" . $id . "' "))) {
        exit(json_encode(["status" => "error", "msg" => "Đơn hàng không tồn tại trong hệ thống"]));
    }
    if($product_order["refund"] == 1) {
        exit(json_encode(["status" => "error", "msg" => __("Đơn hàng này đã được hoàn tiền rồi")]));
    }
    if($product_order["amount"] == 0 || $product_order["pay"] == 0) {
        exit(json_encode(["status" => "error", "msg" => __("Đơn hàng này đã được hoàn tiền rồi")]));
    }
    if(empty($_POST["reason"])) {
        exit(json_encode(["status" => "error", "msg" => "Vui lòng nhập lý do hoàn tiền"]));
    }
    $reason = check_string($_POST["reason"]);
    $refundType = check_string($_POST["refundType"]);
    if($refundType == "partial") {
        $partialQuantity = isset($_POST["partialQuantity"]) ? (int) $_POST["partialQuantity"] : 0;
        if($product_order["amount"] < $partialQuantity) {
            exit(json_encode(["status" => "error", "msg" => __("Số lượng tài khoản cần hoàn vượt quá số lượng tài khoản của đơn hàng này.")]));
        }
        if($partialQuantity <= 0) {
            exit(json_encode(["status" => "error", "msg" => __("Vui lòng nhập số lượng tài khoản cần hoàn.")]));
        }
        $rate = $product_order["pay"] / $product_order["amount"];
        $amountRefund = $partialQuantity * $rate;
        $User = new users();
        $isRefund = $User->RefundCredits($product_order["buyer"], $amountRefund, "[Admin] " . $reason, "REFUND_partial_ORDER_" . $product_order["trans_id"] . "_" . time());
        $leftover = $product_order["amount"] - $partialQuantity;
        $commission_fee = 0;
        if($isRefund) {
            $user = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '" . $product_order["buyer"] . "' ");
            if($user["ref_id"] != 0) {
                $ck = $CMSNT->site("affiliate_ck");
                if(getRowRealtime("users", $user["ref_id"], "ref_ck") != 0) {
                    $ck = getRowRealtime("users", $user["ref_id"], "ref_ck");
                }
                $commission_fee = $amountRefund * $ck / 100;
                $User->RemoveCommission($user["ref_id"], $commission_fee, __("Thu hồi đơn hoàn tiền " . $user["username"]));
            }
            $CMSNT->update("product_order", ["pay" => $product_order["pay"] - $amountRefund, "cost" => $product_order["cost"] / $product_order["amount"] * $leftover, "money" => $product_order["money"] / $product_order["amount"] * $leftover, "amount" => $leftover], " `id` = '" . $id . "' ");
            if($CMSNT->site("cong_tien_nguoi_ban") == 1) {
                $User->RemoveCredits($product_order["seller"], $amountRefund, __("Thu hồi hoàn tiền một phần đơn hàng") . " #" . $product_order["trans_id"], "TAKE_REFUND_partial_ORDER_" . $product_order["trans_id"] . "_" . time());
            }
            $Mobile_Detect = new Mobile_Detect();
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Hoàn tiền một phần đơn hàng") . " (" . $product_order["trans_id"] . ")"]);
            exit(json_encode(["status" => "success", "msg" => __("Hoàn tiền đơn hàng thành công!")]));
        }
        exit(json_encode(["status" => "error", "msg" => __("Đơn hàng này đã được hoàn tiền rồi")]));
    }
    $User = new users();
    $isRefund = $User->RefundCredits($product_order["buyer"], $product_order["pay"], "[Admin] " . $reason, "REFUND_ORDER_" . $product_order["trans_id"]);
    $commission_fee = 0;
    if($isRefund) {
        $user = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '" . $product_order["buyer"] . "' ");
        if($user["ref_id"] != 0) {
            $ck = $CMSNT->site("affiliate_ck");
            if(getRowRealtime("users", $user["ref_id"], "ref_ck") != 0) {
                $ck = getRowRealtime("users", $user["ref_id"], "ref_ck");
            }
            $commission_fee = $product_order["pay"] * $ck / 100;
            $User->RemoveCommission($user["ref_id"], $commission_fee, __("Thu hồi đơn hoàn tiền " . $user["username"]));
        }
        $CMSNT->update("product_order", ["refund" => 1, "trash" => 1, "pay" => 0, "cost" => 0, "amount" => 0, "money" => 0], " `id` = '" . $id . "' ");
        if($CMSNT->site("cong_tien_nguoi_ban") == 1) {
            $User->RemoveCredits($product_order["seller"], $product_order["pay"], __("Thu hồi hoàn tiền đơn hàng") . " #" . $product_order["trans_id"], "TAKE_REFUND_ORDER_" . $product_order["trans_id"]);
        }
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Hoàn tiền đơn hàng") . " (" . $product_order["trans_id"] . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Hoàn tiền đơn hàng thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Đơn hàng này đã được hoàn tiền rồi")]));
}
if($_POST["action"] == "update_stt_table_product") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("products", ["stt" => !empty($_POST["stt"]) ? check_string($_POST["stt"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Cập nhật ưu tiên sản phẩm (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => "Cập nhật ưu tiên sản phẩm ID " . check_string($_POST["id"]) . " thành công!"]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "update_category_category") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("categories", ["parent_id" => !empty($_POST["category_id"]) ? check_string($_POST["category_id"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Cập nhật chuyên mục cha cho chuyên mục (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật chuyên mục cha thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "update_category_product") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("products", ["category_id" => !empty($_POST["category_id"]) ? check_string($_POST["category_id"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Cập nhật chuyên mục cho sản phẩm (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật chuyên mục thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "updateTableProductAPI") {
    if(!checkPermission($getUser["admin"], "manager_suppliers")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("suppliers", ["status" => !empty($_POST["status"]) ? check_string($_POST["status"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Update Supplier (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "updateTableCategory") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("categories", ["stt" => !empty($_POST["stt"]) ? check_string($_POST["stt"]) : 0, "status" => !empty($_POST["status"]) ? check_string($_POST["status"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Update Table Category (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "update_status_category") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("categories", ["status" => !empty($_POST["status"]) ? check_string($_POST["status"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Update Status Category (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "update_status_product") {
    if(!checkPermission($getUser["admin"], "edit_product")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("products", ["status" => !empty($_POST["status"]) ? check_string($_POST["status"]) : 0], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Update Status Product (ID " . check_string($_POST["id"]) . ")"]);
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "cancel_email_campaigns") {
    if(!checkPermission($getUser["admin"], "edit_email_campaigns")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("email_campaigns", ["status" => 2], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        exit(json_encode(["status" => "success", "msg" => __("Cập nhật thành công!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Cập nhật thất bại")]));
}
if($_POST["action"] == "setDefaultLanguage") {
    if(!checkPermission($getUser["admin"], "edit_lang")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    if(empty($_POST["id"])) {
        exit(json_encode(["status" => "error", "msg" => __("Data does not exist")]));
    }
    $id = check_string($_POST["id"]);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '" . $id . "' ");
    if(!$row) {
        $data = json_encode(["status" => "error", "msg" => __("Data does not exist")]);
        exit($data);
    }
    $CMSNT->update("languages", ["lang_default" => 0], " `id` > 0 ");
    $isUpdate = $CMSNT->update("languages", ["lang_default" => 1], " `id` = '" . $id . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Set default language (" . $row["lang"] . " ID " . $row["id"] . ")"]);
        $data = json_encode(["status" => "success", "msg" => __("Language status change successful")]);
        exit($data);
    }
}
if($_POST["action"] == "changeTranslate") {
    if(!checkPermission($getUser["admin"], "edit_lang")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $isUpdate = $CMSNT->update("translate", ["value" => check_string($_POST["value"])], " `id` = '" . check_string($_POST["id"]) . "' ");
    if($isUpdate) {
        exit(json_encode(["status" => "success", "msg" => __("Update successful!")]));
    }
    exit(json_encode(["status" => "error", "msg" => __("Update failed!")]));
}
if($_POST["action"] == "setDefaultCurrency") {
    if(!checkPermission($getUser["admin"], "edit_currency")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    $id = check_string($_POST["id"]);
    $row = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '" . $id . "' ");
    if(!$row) {
        $data = json_encode(["status" => "error", "msg" => "ID tiền tệ không tồn tại trong hệ thống"]);
        exit($data);
    }
    $CMSNT->update("currencies", ["default_currency" => 0], " `id` > 0 ");
    $isUpdate = $CMSNT->update("currencies", ["default_currency" => 1], " `id` = '" . $id . "' ");
    if($isUpdate) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Set mặc định tiền tệ (" . $row["name"] . " ID " . $row["id"] . ")"]);
        $data = json_encode(["status" => "success", "msg" => "Thay đổi trạng thái tiền tệ thành công"]);
        exit($data);
    }
    exit(json_encode(["status" => "error", "msg" => "Cập nhật thất bại"]));
}
if($_POST["action"] == "logoutALL") {
    if(!checkPermission($getUser["admin"], "edit_user")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    foreach ($CMSNT->get_list(" SELECT * FROM `users` WHERE `id` > 0 ") as $row) {
        $CMSNT->update("users", ["token" => generateUltraSecureToken(32)], " `id` = '" . $row["id"] . "' ");
    }
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Log out all members on the system")]);
    $data = json_encode(["status" => "success", "msg" => __("Đăng xuất tất cả tài khoản thành công!")]);
    exit($data);
} elseif($_POST["action"] == "changeAPIKey") {
    if(!checkPermission($getUser["admin"], "edit_user")) {
        exit(json_encode(["status" => "error", "msg" => "Bạn không có quyền sử dụng tính năng này"]));
    }
    foreach ($CMSNT->get_list(" SELECT * FROM `users`  ") as $row) {
        $CMSNT->update("users", ["api_key" => generateUltraSecureToken(16)], " `id` = '" . $row["id"] . "' ");
    }
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Change API Key for all members")]);
    $data = json_encode(["status" => "success", "msg" => __("Thay đổi API KEY thành công!")]);
    exit($data);
} elseif($_POST["action"] == "updateCategorySTT" && isset($_POST["order"]) && is_array($_POST["order"])) {
    $order = $_POST["order"];
    foreach ($order as $item) {
        $id = isset($item["id"]) ? (int) $item["id"] : 0;
        $position = isset($item["position"]) ? (int) $item["position"] : 0;
        if(0 < $id) {
            $CMSNT->update("categories", ["stt" => $position], " `id` = " . $id . " ");
        }
    }
    exit(json_encode(["status" => "success", "msg" => "Cập nhật thứ tự thành công!"]));
} elseif($_POST["action"] == "updateChildCategorySTT" && isset($_POST["order"]) && is_array($_POST["order"])) {
    $order = $_POST["order"];
    foreach ($order as $item) {
        $id = isset($item["id"]) ? (int) $item["id"] : 0;
        $position = isset($item["position"]) ? (int) $item["position"] : 0;
        $parent_id = isset($item["parent_id"]) ? (int) $item["parent_id"] : 0;
        if(0 < $id && 0 < $parent_id) {
            $check = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = " . $id . " AND `parent_id` = " . $parent_id);
            if($check) {
                $CMSNT->update("categories", ["stt" => $position], " `id` = " . $id . " ");
            }
        }
    }
    exit(json_encode(["status" => "success", "msg" => "Cập nhật thứ tự chuyên mục con thành công!"]));
} else {
    exit(json_encode(["status" => "error", "msg" => __("Invalid data")]));
}

?>