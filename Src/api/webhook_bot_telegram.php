<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/../libs/db.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../libs/lang.php";
require_once __DIR__ . "/../libs/helper.php";
require_once __DIR__ . "/../libs/database/users.php";
$CMSNT = new DB();
if($CMSNT->site("status") != 1 && !isSecureCookie("admin_login")) {
    exit("status_website_off");
}
if($CMSNT->site("telegram_assistant_status") != 1) {
    exit("Trạng thái đang OFF");
}
$content = file_get_contents("php://input");
$update = json_decode($content, true);
ignore_user_abort(true);
header("HTTP/1.1 200 OK");
flush();
if(!$update) {
    exit;
}
$message = $update["message"] ?? NULL;
$callback_query = $update["callback_query"] ?? NULL;
$secret_token = $CMSNT->site("telegram_assistant_secret_token") ?? "";
$headers = getallheaders();
if(!isset($headers["X-Telegram-Bot-Api-Secret-Token"]) || $headers["X-Telegram-Bot-Api-Secret-Token"] !== $secret_token) {
    exit("Xác thực X-Telegram-Bot-Api-Secret-Token không hợp lệ");
}
if($message) {
    $chat_id = $message["chat"]["id"];
    $sender_username = strtolower($message["from"]["username"] ?? "");
} elseif($callback_query) {
    $chat_id = $callback_query["message"]["chat"]["id"];
    $sender_username = strtolower($callback_query["from"]["username"] ?? "");
}
$allowed_usernames = array_map("trim", explode(",", $CMSNT->site("telegram_assistant_list_username")));
if(!in_array($sender_username, array_map("strtolower", $allowed_usernames))) {
    exit("Bạn không có quyền ra lệnh cho bot này!");
}
$commands = ["addfund" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "removefund" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "balance" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "userinfo" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "listusers" => function ($params) use($CMSNT) {
    static $chat_id = NULL;
    $perPage = 10;
    $page = isset($params[0]) && is_numeric($params[0]) ? (int) $params[0] : 1;
    if($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $totalUsers = $CMSNT->num_rows("SELECT id FROM `users`");
    $totalPages = ceil($totalUsers / $perPage);
    $users = $CMSNT->get_list("SELECT * FROM `users` ORDER BY `id` DESC LIMIT " . $perPage . " OFFSET " . $offset);
    $message = "📋 *Danh sách người dùng (Trang " . $page . "/" . $totalPages . ")*\n";
    $message .= "------------------------------------\n";
    foreach ($users as $index => $user) {
        $message .= "👤 *Username:* `" . $user["username"] . "`\n";
        $message .= "🆔 *ID:* `" . $user["id"] . "`\n";
        $message .= "📧 *Email:* `" . $user["email"] . "`\n";
        $message .= "💰 *Số dư:* " . format_currency($user["money"]) . "\n";
        $message .= "------------------------------------\n";
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "listusers " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "listusers " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}, "blockuser" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "unblockuser" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "topusers" => function ($params) use($CMSNT) {
    static $chat_id = NULL;
    $perPage = 10;
    $page = isset($params[0]) && is_numeric($params[0]) ? (int) $params[0] : 1;
    if($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $totalUsers = $CMSNT->num_rows("SELECT id FROM `users`");
    $totalPages = ceil($totalUsers / $perPage);
    $users = $CMSNT->get_list("\n            SELECT * \n            FROM `users` \n            ORDER BY `total_money` DESC \n            LIMIT " . $perPage . " OFFSET " . $offset . "\n        ");
    $message = "💹 *TOP Người dùng nạp tiền (Trang " . $page . "/" . $totalPages . ")*\n";
    $message .= "------------------------------------\n";
    $medals = ["🥇", "🥈", "🥉"];
    foreach ($users as $index => $user) {
        $globalRank = $offset + $index + 1;
        $medal = $globalRank <= 3 ? $medals[$globalRank - 1] : "";
        $message .= "*Hạng:* `" . $globalRank . "` " . $medal . "\n";
        $message .= "*Username:* `" . $user["username"] . "`\n";
        $message .= "💰 *Tổng nạp:* *" . format_currency($user["total_money"]) . "*\n";
        $message .= "------------------------------------\n";
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "topusers " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "topusers " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}, "siteinfo" => function () use($CMSNT) {
    static $chat_id = NULL;
    $site_status = $CMSNT->site("status") == 1 ? "Hoạt động" : "Bảo trì";
    sendmessage($chat_id, "🌐 *Thông tin website:*\nTên site: *" . $CMSNT->site("title") . "*\n" . "Trạng thái: *" . $site_status . "*\n" . "Người dùng: *" . $CMSNT->num_rows("SELECT * FROM users") . "*");
}, "changepassword" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "setstatus" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "revenuetoday" => function () use($CMSNT) {
    static $chat_id = NULL;
    $currentDate = date("Y-m-d");
    $queryToday = "\n            SELECT \n                COUNT(id) AS total_orders_today, \n                SUM(pay)  AS total_pay_today, \n                SUM(cost) AS total_cost_today\n            FROM `product_order`\n            WHERE `refund` = 0\n            AND `create_gettime` LIKE '" . $currentDate . "%' \n        ";
    $resultToday = $CMSNT->get_row($queryToday);
    $total_orders_today = $resultToday["total_orders_today"] ?? 0;
    $total_pay_today = $resultToday["total_pay_today"] ?? 0;
    $total_cost_today = $resultToday["total_cost_today"] ?? 0;
    $profit_today = $total_pay_today - $total_cost_today;
    $new_users_today = $CMSNT->get_row("\n            SELECT COUNT(id) AS total_users_today \n            FROM `users`\n            WHERE `create_date` LIKE '" . $currentDate . "%'\n        ")["total_users_today"] ?? 0;
    $yesterdayDate = date("Y-m-d", strtotime("-1 day"));
    $queryYesterday = "\n            SELECT \n                SUM(pay)  AS total_pay_yesterday,\n                SUM(cost) AS total_cost_yesterday\n            FROM `product_order`\n            WHERE `refund` = 0\n            AND `create_gettime` LIKE '" . $yesterdayDate . "%'\n        ";
    $resultPrev = $CMSNT->get_row($queryYesterday);
    $total_pay_yesterday = $resultPrev["total_pay_yesterday"] ?? 0;
    $total_cost_yesterday = $resultPrev["total_cost_yesterday"] ?? 0;
    $profit_yesterday = $total_pay_yesterday - $total_cost_yesterday;
    if($profit_yesterday == 0) {
        $difference = $profit_today;
        $percentDiff = 0;
    } else {
        $difference = $profit_today - $profit_yesterday;
        $percentDiff = $difference / $profit_yesterday * 100;
    }
    $percentDiff = round($percentDiff, 2);
    if(0 < $difference) {
        $trendEmoji = "🟢";
        $trendString = "*Tăng:* +" . $percentDiff . "%";
    } elseif($difference < 0) {
        $trendEmoji = "🔴";
        $trendString = "*Giảm:* " . $percentDiff . "%";
    } else {
        $trendEmoji = "⚪️";
        $trendString = "*Không thay đổi* (0%)";
    }
    $message = "📊 *BÁO CÁO DOANH THU HÔM NAY*\n";
    $message .= "------------------------------------\n";
    $message .= "📅 *Ngày:* `" . $currentDate . "`\n";
    $message .= "------------------------------------\n";
    $message .= "🛒 *Tổng đơn hàng:* `" . format_cash($total_orders_today) . "`\n";
    $message .= "💰 *Tổng doanh thu:* *" . format_currency($total_pay_today) . "*\n";
    $message .= "💸 *Tổng chi phí:* *" . format_currency($total_cost_today) . "*\n";
    $message .= "📈 *Lợi nhuận:* *" . format_currency($profit_today) . "*\n";
    $message .= "👥 *Người dùng mới:* `" . format_cash($new_users_today) . "`\n";
    $message .= "------------------------------------\n";
    $message .= $trendEmoji . " So với hôm qua: " . $trendString . "\n";
    sendmessage($chat_id, $message);
}, "revenueweek" => function () use($CMSNT) {
    static $chat_id = NULL;
    $startOfWeek = date("Y-m-d", strtotime("monday this week"));
    $endOfWeek = date("Y-m-d", strtotime("sunday this week"));
    $queryThisWeek = "\n            SELECT \n                COUNT(id) AS total_orders_week, \n                SUM(pay) AS total_pay_week, \n                SUM(cost) AS total_cost_week\n            FROM `product_order`\n            WHERE `refund` = 0\n            AND `create_gettime` BETWEEN '" . $startOfWeek . " 00:00:00' AND '" . $endOfWeek . " 23:59:59'\n        ";
    $resultThis = $CMSNT->get_row($queryThisWeek);
    $total_orders_week = $resultThis["total_orders_week"] ?? 0;
    $total_pay_week = $resultThis["total_pay_week"] ?? 0;
    $total_cost_week = $resultThis["total_cost_week"] ?? 0;
    $profit_week = $total_pay_week - $total_cost_week;
    $new_users_week = $CMSNT->get_row("\n            SELECT COUNT(id) AS total_users_week \n            FROM `users`\n            WHERE `create_date` BETWEEN '" . $startOfWeek . "' AND '" . $endOfWeek . "'\n        ")["total_users_week"] ?? 0;
    $previousWeekStart = date("Y-m-d", strtotime("monday last week"));
    $previousWeekEnd = date("Y-m-d", strtotime("sunday last week"));
    $queryPrevWeek = "\n            SELECT \n                SUM(pay) AS total_pay_prev,\n                SUM(cost) AS total_cost_prev\n            FROM `product_order`\n            WHERE `refund` = 0\n            AND `create_gettime` BETWEEN '" . $previousWeekStart . " 00:00:00' AND '" . $previousWeekEnd . " 23:59:59'\n        ";
    $resultPrev = $CMSNT->get_row($queryPrevWeek);
    $total_pay_prev = $resultPrev["total_pay_prev"] ?? 0;
    $total_cost_prev = $resultPrev["total_cost_prev"] ?? 0;
    $profit_previous = $total_pay_prev - $total_cost_prev;
    if($profit_previous == 0) {
        $difference = $profit_week;
        $percentDiff = 0;
    } else {
        $difference = $profit_week - $profit_previous;
        $percentDiff = $difference / $profit_previous * 100;
    }
    $percentDiff = round($percentDiff, 2);
    if(0 < $difference) {
        $trendEmoji = "🟢";
        $trendString = "*Tăng:* +" . $percentDiff . "%";
    } elseif($difference < 0) {
        $trendEmoji = "🔴";
        $trendString = "*Giảm:* " . $percentDiff . "%";
    } else {
        $trendEmoji = "⚪️";
        $trendString = "*Không thay đổi* (0%)";
    }
    $message = "📊 *BÁO CÁO DOANH THU TUẦN NÀY*\n";
    $message .= "------------------------------------\n";
    $message .= "📅 *Khoảng thời gian:* `" . $startOfWeek . "` → `" . $endOfWeek . "`\n";
    $message .= "------------------------------------\n";
    $message .= "🛒 *Tổng đơn hàng:* `" . format_cash($total_orders_week) . "`\n";
    $message .= "💰 *Tổng doanh thu:* *" . format_currency($total_pay_week) . "*\n";
    $message .= "💸 *Tổng chi phí:* *" . format_currency($total_cost_week) . "*\n";
    $message .= "📈 *Lợi nhuận:* *" . format_currency($profit_week) . "*\n";
    $message .= "👥 *Người dùng mới:* `" . format_cash($new_users_week) . "`\n";
    $message .= "------------------------------------\n";
    $message .= $trendEmoji . " So với tuần trước: " . $trendString . "\n";
    sendmessage($chat_id, $message);
}, "revenuemonth" => function () use($CMSNT) {
    static $chat_id = NULL;
    $startOfMonth = date("Y-m-01");
    $endOfMonth = date("Y-m-t");
    $queryThisMonth = "\n            SELECT \n                COUNT(id) AS total_orders_month, \n                SUM(pay) AS total_pay_month, \n                SUM(cost) AS total_cost_month\n            FROM `product_order`\n            WHERE `refund` = 0\n            AND `create_gettime` BETWEEN '" . $startOfMonth . " 00:00:00' AND '" . $endOfMonth . " 23:59:59'\n        ";
    $resultThis = $CMSNT->get_row($queryThisMonth);
    $total_orders_month = $resultThis["total_orders_month"] ?? 0;
    $total_pay_month = $resultThis["total_pay_month"] ?? 0;
    $total_cost_month = $resultThis["total_cost_month"] ?? 0;
    $profit_month = $total_pay_month - $total_cost_month;
    $new_users_month = $CMSNT->get_row("\n            SELECT COUNT(id) AS total_users_month \n            FROM `users`\n            WHERE `create_date` BETWEEN '" . $startOfMonth . "' AND '" . $endOfMonth . "'\n        ")["total_users_month"] ?? 0;
    $previousMonthStart = date("Y-m-01", strtotime("-1 month"));
    $previousMonthEnd = date("Y-m-t", strtotime("-1 month"));
    $queryPrevMonth = "\n            SELECT \n                SUM(pay) AS total_pay_prev,\n                SUM(cost) AS total_cost_prev\n            FROM `product_order`\n            WHERE `refund` = 0\n            AND `create_gettime` BETWEEN '" . $previousMonthStart . " 00:00:00' AND '" . $previousMonthEnd . " 23:59:59'\n        ";
    $resultPrev = $CMSNT->get_row($queryPrevMonth);
    $total_pay_prev = $resultPrev["total_pay_prev"] ?? 0;
    $total_cost_prev = $resultPrev["total_cost_prev"] ?? 0;
    $profit_previous = $total_pay_prev - $total_cost_prev;
    if($profit_previous == 0) {
        $difference = $profit_month - $profit_previous;
        $percentDiff = 0;
    } else {
        $difference = $profit_month - $profit_previous;
        $percentDiff = $difference / $profit_previous * 100;
    }
    $percentDiff = round($percentDiff, 2);
    if(0 < $difference) {
        $trendEmoji = "🟢";
        $trendString = "*Tăng:* +" . $percentDiff . "%";
    } elseif($difference < 0) {
        $trendEmoji = "🔴";
        $trendString = "*Giảm:* " . $percentDiff . "%";
    } else {
        $trendEmoji = "⚪️";
        $trendString = "*Không thay đổi* (0%)";
    }
    $message = "📊 *BÁO CÁO DOANH THU THÁNG NÀY*\n";
    $message .= "------------------------------------\n";
    $message .= "📅 *Khoảng thời gian:* `" . $startOfMonth . "` → `" . $endOfMonth . "`\n";
    $message .= "------------------------------------\n";
    $message .= "🛒 *Tổng đơn hàng:* `" . format_cash($total_orders_month) . "`\n";
    $message .= "💰 *Tổng doanh thu:* *" . format_currency($total_pay_month) . "*\n";
    $message .= "💸 *Tổng chi phí:* *" . format_currency($total_cost_month) . "*\n";
    $message .= "📈 *Lợi nhuận:* *" . format_currency($profit_month) . "*\n";
    $message .= "👥 *Người dùng mới:* `" . format_cash($new_users_month) . "`\n";
    $message .= "------------------------------------\n";
    $message .= $trendEmoji . " So với tháng trước: " . $trendString . "\n";
    sendmessage($chat_id, $message);
}, "orders" => function ($params) use($chat_id) {
    static $CMSNT = NULL;
    $perPage = 5;
    $page = isset($params[0]) && is_numeric($params[0]) ? (int) $params[0] : 1;
    if($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $totalOrders = $CMSNT->num_rows("SELECT id FROM product_order");
    $totalPages = ceil($totalOrders / $perPage);
    $orders = $CMSNT->get_list("SELECT * FROM product_order \n                                     ORDER BY id DESC \n                                     LIMIT " . $perPage . " OFFSET " . $offset);
    $message = "📋 *Danh sách đơn hàng (Trang " . $page . "/" . $totalPages . "):*\n\n";
    foreach ($orders as $order) {
        $username = getRowRealtime("users", $order["buyer"], "username");
        $message .= "🆔 *Mã giao dịch:* `" . $order["trans_id"] . "`\n";
        $message .= "📦 *Sản phẩm:* `" . $order["product_name"] . "`\n";
        $message .= "👤 *Người mua:* `" . $username . "`\n";
        $message .= "🔢 *Số lượng:* " . format_cash($order["amount"]) . " - 💰 *Thanh toán:* " . format_currency($order["pay"]) . "\n";
        $message .= "--------------------------\n";
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "orders " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "orders " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}, "checkorder" => function ($params) {
    static $chat_id = NULL;
    static $CMSNT = NULL;
}, "bestsellers" => function ($params) use($CMSNT) {
    static $chat_id = NULL;
    $perPage = 10;
    $page = isset($params[0]) && is_numeric($params[0]) ? (int) $params[0] : 1;
    if($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $rowCount = $CMSNT->get_row("\n            SELECT COUNT(DISTINCT product_id) AS total_products\n            FROM product_order\n            WHERE refund = 0\n        ");
    $totalProducts = $rowCount ? $rowCount["total_products"] : 0;
    $totalPages = 0 < $totalProducts ? ceil($totalProducts / $perPage) : 1;
    $products = $CMSNT->get_list("\n            SELECT \n                product_id,\n                product_name,\n                SUM(amount) AS total_sold,\n                SUM(pay) AS total_sales\n            FROM product_order\n            WHERE refund = 0\n            GROUP BY product_id\n            ORDER BY total_sold DESC\n            LIMIT " . $perPage . " OFFSET " . $offset . "\n        ");
    $message = "🔥 *TOP Sản phẩm bán chạy (Trang " . $page . "/" . $totalPages . ")*\n";
    $message .= "------------------------------------\n";
    $medals = ["🥇", "🥈", "🥉"];
    foreach ($products as $index => $item) {
        $globalRank = $offset + $index + 1;
        $medal = $globalRank <= 3 ? $medals[$globalRank - 1] : "";
        $message .= "*Hạng:* `" . $globalRank . "` " . $medal . "\n";
        $message .= "*Tên sản phẩm:* `" . $item["product_name"] . "`\n";
        $message .= "🔢 *Tổng bán:* *" . format_cash($item["total_sold"]) . "*\n";
        $message .= "💰 *Tổng tiền:* *" . format_currency($item["total_sales"]) . "*\n";
        $message .= "------------------------------------\n";
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "bestsellers " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "bestsellers " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    if(!$products) {
        $message = "Không có dữ liệu sản phẩm bán chạy!";
    }
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}, "logs" => function ($params) use($CMSNT) {
    static $chat_id = NULL;
    $perPage = 10;
    $page = isset($params[0]) && is_numeric($params[0]) ? (int) $params[0] : 1;
    if($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $totalLogs = $CMSNT->num_rows("SELECT id FROM `logs`");
    $totalPages = 0 < $totalLogs ? ceil($totalLogs / $perPage) : 1;
    $listLogs = $CMSNT->get_list("\n            SELECT *\n            FROM `logs`\n            ORDER BY `id` DESC\n            LIMIT " . $perPage . " OFFSET " . $offset . "\n        ");
    $message = "📜 *Nhật ký hoạt động (Trang " . $page . "/" . $totalPages . ")*\n";
    $message .= "------------------------------------\n";
    if(!$listLogs) {
        $message .= "Không có dữ liệu logs.";
    } else {
        foreach ($listLogs as $row) {
            $username = getRowRealtime("users", $row["user_id"], "username") ?: "N/A";
            $timeRelative = timeAgo(strtotime($row["createdate"]));
            $message .= "👤 *Username:* `" . $username . "` [ID " . $row["user_id"] . "]\n";
            $message .= "🔧 *Hành động:* `" . $row["action"] . "`\n";
            $message .= "⏰ *Thời gian:* `" . $row["createdate"] . "` (" . $timeRelative . ")\n";
            $message .= "🌐 *IP:* " . $row["ip"] . "\n";
            $message .= "💻 *Thiết bị:* " . $row["device"] . "\n";
            $message .= "------------------------------------\n";
        }
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "logs " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "logs " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}, "log" => function ($params) use($CMSNT) {
    static $chat_id = NULL;
    $username = $params[0] ?? NULL;
    $pageParam = $params[1] ?? NULL;
    if(empty($username)) {
        return sendmessage($chat_id, "⚠️ *Vui lòng nhập:* `/log username trang`\nVD: `/log ntthanhz 2`");
    }
    $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `username` = '" . $username . "' ");
    if(!$user) {
        return sendmessage($chat_id, "⚠️ *Không tìm thấy username:* `" . $username . "`");
    }
    $page = is_numeric($pageParam) && 0 < $pageParam ? (int) $pageParam : 1;
    if($page < 1) {
        $page = 1;
    }
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    $user_id = $user["id"];
    $totalLogs = $CMSNT->num_rows("SELECT `id` FROM `logs` WHERE `user_id` = '" . $user_id . "' ");
    $totalPages = 0 < $totalLogs ? ceil($totalLogs / $perPage) : 1;
    $listLogs = $CMSNT->get_list("\n            SELECT *\n            FROM `logs`\n            WHERE `user_id` = '" . $user_id . "'\n            ORDER BY `id` DESC\n            LIMIT " . $perPage . " OFFSET " . $offset . "\n        ");
    $message = "📜 *Nhật ký hoạt động của* `" . $username . "` *(Trang " . $page . "/" . $totalPages . ")*\n";
    $message .= "------------------------------------\n";
    if(!$listLogs) {
        $message .= "Không có dữ liệu logs.";
    } else {
        foreach ($listLogs as $row) {
            $timeRelative = timeAgo(strtotime($row["createdate"]));
            $message .= "🔧 *Hành động:* `" . $row["action"] . "`\n";
            $message .= "⏰ *Thời gian:* `" . $row["createdate"] . "` (" . $timeRelative . ")\n";
            $message .= "🌐 *IP:* " . $row["ip"] . "\n";
            $message .= "💻 *Thiết bị:* " . $row["device"] . "\n";
            $message .= "------------------------------------\n";
        }
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "log " . $username . " " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "log " . $username . " " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}, "topmoney" => function ($params) use($CMSNT) {
    static $chat_id = NULL;
    $perPage = 10;
    $page = isset($params[0]) && is_numeric($params[0]) ? (int) $params[0] : 1;
    if($page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $totalUsers = $CMSNT->num_rows("SELECT id FROM `users`");
    $totalPages = ceil($totalUsers / $perPage);
    $users = $CMSNT->get_list("\n            SELECT * \n            FROM `users` \n            ORDER BY `money` DESC \n            LIMIT " . $perPage . " OFFSET " . $offset . "\n        ");
    $message = "💹 *TOP Người dùng có số dư cao nhất (Trang " . $page . "/" . $totalPages . ")*\n";
    $message .= "------------------------------------\n";
    $medals = ["🥇", "🥈", "🥉"];
    foreach ($users as $index => $user) {
        $globalRank = $offset + $index + 1;
        $medal = $globalRank <= 3 ? $medals[$globalRank - 1] : "";
        $message .= "*Hạng:* `" . $globalRank . "` " . $medal . "\n";
        $message .= "*Username:* `" . $user["username"] . "`\n";
        $message .= "💰 *Số dư khả dựng:* *" . format_currency($user["money"]) . "*\n";
        $message .= "------------------------------------\n";
    }
    $inline_keyboard = [];
    if(1 < $page) {
        $inline_keyboard[] = [["text" => "⬅️ Trang trước", "callback_data" => "topmoney " . ($page - 1)]];
    }
    if($page < $totalPages) {
        $inline_keyboard[] = [["text" => "➡️ Trang sau", "callback_data" => "topmoney " . ($page + 1)]];
    }
    $replyMarkup = json_encode(["inline_keyboard" => $inline_keyboard]);
    $message_id = $GLOBALS["message_id"] ?? NULL;
    sendmessagewithbuttons($chat_id, $message, $replyMarkup, $message_id);
}];
if($callback_query) {
    $callback_data = explode(" ", $callback_query["data"]);
    $command = $callback_data[0];
    $params = array_slice($callback_data, 1);
    $message_id = $callback_query["message"]["message_id"];
    $GLOBALS["message_id"] = $message_id;
    if(isset($commands[$command])) {
        savetelegramlog($sender_username, $command, $params, "callback");
        $commands[$command]($params);
        file_get_contents("https://api.telegram.org/bot" . $CMSNT->site("telegram_assistant_token") . "/answerCallbackQuery?" . http_build_query(["callback_query_id" => $callback_query["id"], "text" => "Tải dữ liệu thành công!", "show_alert" => false]));
        exit;
    }
    sendmessage($chat_id, "❌ Lệnh không hợp lệ: `" . $command . "`");
    exit;
}
if($message) {
    $text = strtolower(trim($message["text"]));
    if(!str_starts_with($text, "/")) {
        return NULL;
    }
    preg_match("/^\\/(\\w+)\\s*(.*)?\$/", $text, $matches);
    $command = $matches[1] ?? "";
    $params = array_filter(explode(" ", trim($matches[2] ?? "")));
    if(isset($commands[$command])) {
        savetelegramlog($sender_username, $command, $params, "message");
        $commands[$command]($params);
    } elseif($command[["help" => true, "cmd" => true]]) {
        sendHelpMenu($chat_id);
    } else {
        sendmessage($chat_id, "❌ *Lệnh không hợp lệ.*\nVui lòng gõ `/help` hoặc `/cmd` để xem danh sách lệnh.");
    }
}
$botToken = $CMSNT->site("telegram_assistant_token");
setbotcommands($botToken);
function sendMessage($chat_id, $text)
{
    global $CMSNT;
    if(!$CMSNT->site("telegram_assistant_LicenseKey")) {
        $text = "⚠️ Vui lòng kích hoạt giấy phép trước khi sử dụng chức năng này";
    } else {
        $checkKey = checkAddonLicense($CMSNT->site("telegram_assistant_LicenseKey"), "SHOPCLONE7_BOTQUANLY");
        if(!$checkKey["status"]) {
            $text = $checkKey["msg"];
        }
    }
    $url = "https://api.telegram.org/bot" . $CMSNT->site("telegram_assistant_token") . "/sendMessage";
    file_get_contents($url . "?" . http_build_query(["chat_id" => $chat_id, "text" => $text, "parse_mode" => "Markdown"]));
}
function sendMessageWithButtons($chat_id, $text, $replyMarkup, $message_id = NULL)
{
    global $CMSNT;
    if(!$CMSNT->site("telegram_assistant_LicenseKey")) {
        $text = "⚠️ Vui lòng kích hoạt giấy phép trước khi sử dụng chức năng này";
    } else {
        $checkKey = checkAddonLicense($CMSNT->site("telegram_assistant_LicenseKey"), "SHOPCLONE7_BOTQUANLY");
        if(!$checkKey["status"]) {
            $text = $checkKey["msg"];
        }
    }
    $url = "https://api.telegram.org/bot" . $CMSNT->site("telegram_assistant_token") . "/";
    $data = ["chat_id" => $chat_id, "text" => $text, "parse_mode" => "Markdown", "reply_markup" => $replyMarkup];
    if($message_id) {
        $url .= "editMessageText";
        $data["message_id"] = $message_id;
    } else {
        $url .= "sendMessage";
    }
    $response = file_get_contents($url . "?" . http_build_query($data));
}
function saveTelegramLog($username, $command, $params, $type = "message")
{
    global $CMSNT;
    $CMSNT->insert("telegram_logs", ["username" => $username, "command" => $command, "params" => json_encode($params), "type" => $type, "time" => date("Y-m-d H:i:s")]);
}
function sendHelpMenu($chat_id)
{
    $helpMessage = "⚙️ **DANH SÁCH LỆNH QUẢN LÝ** ⚙️\n\n------------------------------------\n**[1.] LỆNH QUẢN LÝ TÀI KHOẢN**\n------------------------------------\n➕ *Cộng tiền:* `/addfund username số_tiền lý_do`  \n➖ *Trừ tiền:* `/removefund username số_tiền lý_do`  \n🚫 *Khóa tài khoản:* `/blockuser username lý_do`  \n🔑 *Mở khóa tài khoản:* `/unblockuser username`  \n🔍 *Kiểm tra số dư:* `/balance username`  \n👤 *Thông tin tài khoản:* `/userinfo username`  \n🔄 *Đổi mật khẩu:* `/changepassword username mật_khẩu_mới`  \n📜 *Xem nhật ký user:* `/log username`\n\n------------------------------------\n**[2.] LỆNH QUẢN LÝ HỆ THỐNG**\n------------------------------------\nℹ️ *Thông tin website:* `/siteinfo`  \n🔄 *Bật/tắt trạng thái website:* `/setstatus 0 hoặc 1`  \n📜 *Xem nhật ký hoạt động:* `/logs`\n\n------------------------------------\n**[3.] LỆNH QUẢN LÝ CHUNG**\n------------------------------------\n🛒 *Đơn hàng gần đây:* `/orders`  \n📄 *Chi tiết đơn hàng:* `/checkorder mã_đơn_hàng`\n\n------------------------------------\n**[4.] LỆNH THỐNG KÊ - BÁO CÁO**\n------------------------------------\n📅 *Doanh thu hôm nay:* `/revenuetoday`  \n📅 *Doanh thu tuần này:* `/revenueweek`  \n📅 *Doanh thu tháng này:* `/revenuemonth`  \n📊 *Danh sách thành viên:* `/listusers`  \n🏆 *TOP Nạp tiền:* `/topusers`  \n🏆 *TOP Số dư:* `/topmoney`  \n🏆 *TOP Sản phẩm bán chạy:* `/bestsellers`\n\n------------------------------------\n**VÍ DỤ SỬ DỤNG**\n------------------------------------\n`/addfund johndoe 50000 hoàn tiền đơn hàng`\n";
    sendmessage($chat_id, $helpMessage);
}
function setBotCommands($botToken)
{
    $commands = [["command" => "help", "description" => "Xem danh sách lệnh: /help"], ["command" => "cmd", "description" => "Xem danh sách lệnh: /cmd"], ["command" => "addfund", "description" => "Cộng tiền: /addfund username số_tiền lý_do"], ["command" => "removefund", "description" => "Trừ tiền: /removefund username số_tiền lý_do"], ["command" => "blockuser", "description" => "Khóa tài khoản: /blockuser username lý_do"], ["command" => "unblockuser", "description" => "Mở khóa tài khoản: /unblockuser username"], ["command" => "balance", "description" => "Kiểm tra số dư: /balance username"], ["command" => "userinfo", "description" => "Thông tin tài khoản: /userinfo username"], ["command" => "changepassword", "description" => "Thay đổi mật khẩu: /changepassword username mật_khẩu_mới"], ["command" => "log", "description" => "Xem nhật ký hoạt động từng user: /log username"], ["command" => "siteinfo", "description" => "Thông tin website: /siteinfo"], ["command" => "setstatus", "description" => "Bật/tắt trạng thái website: /setstatus 0 hoặc 1"], ["command" => "logs", "description" => "Xem nhật ký hoạt động: /logs"], ["command" => "orders", "description" => "Đơn hàng gần đây: /orders"], ["command" => "checkorder", "description" => "Chi tiết đơn hàng: /checkorder mã_đơn_hàng"], ["command" => "revenuetoday", "description" => "Doanh thu hôm nay: /revenuetoday"], ["command" => "revenueweek", "description" => "Doanh thu tuần này: /revenueweek"], ["command" => "revenuemonth", "description" => "Doanh thu tháng này: /revenuemonth"], ["command" => "listusers", "description" => "Danh sách thành viên: /listusers"], ["command" => "topusers", "description" => "TOP Nạp tiền: /topusers"], ["command" => "topmoney", "description" => "TOP Số dư: /topmoney"], ["command" => "bestsellers", "description" => "TOP Sản phẩm bán chạy: /bestsellers"]];
    $url = "https://api.telegram.org/bot" . $botToken . "/setMyCommands";
    $data = json_encode(["commands" => $commands]);
    $options = ["http" => ["header" => "Content-Type: application/json", "method" => "POST", "content" => $data]];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result ? "Đã thiết lập lệnh gợi ý thành công!" : "Lỗi thiết lập lệnh gợi ý.";
}

?>