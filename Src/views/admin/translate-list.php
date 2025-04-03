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
$body = ["title" => "Translate", "desc" => "CMSNT Panel", "keyword" => "cmsnt, CMSNT, cmsnt.co,"];
$body["header"] = "\n<link rel=\"stylesheet\" href=\"https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css\">\n<link rel=\"stylesheet\" href=\"https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css\">\n<link rel=\"stylesheet\" href=\"https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css\">\n";
$body["footer"] = "\n<script src=\"https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js\"></script>\n<script src=\"https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js\"></script>\n<script src=\"https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js\"></script>\n<script src=\"https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js\"></script>\n<script src=\"https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js\"></script>\n";
require_once __DIR__ . "/../../models/is_admin.php";
if(isset($_GET["id"])) {
    $id = check_string($_GET["id"]);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '" . $id . "' ");
    if(!$row) {
        redirect(base_url("admin/language-list"));
    }
} else {
    redirect(base_url("admin/language-list"));
}
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";
require_once __DIR__ . "/nav.php";
if(!checkPermission($getUser["admin"], "edit_lang")) {
    exit("<script type=\"text/javascript\">if(!alert(\"Bạn không có quyền sử dụng tính năng này\")){window.history.back();}</script>");
}
if(isset($_POST["addTranslate"])) {
    if($CMSNT->site("status_demo") != 0) {
        exit("<script type=\"text/javascript\">if(!alert(\"Không được dùng chức năng này vì đây là trang web demo.\")){window.history.back().location.reload();}</script>");
    }
    foreach ($CMSNT->get_list("SELECT * FROM `languages` WHERE `id` != '" . $row["id"] . "' ") as $lang) {
        if($CMSNT->num_rows("SELECT * FROM `translate` WHERE `name` = '" . check_string($_POST["name"]) . "' AND `lang_id` = '" . $lang["id"] . "'  ") < 1) {
            $CMSNT->insert("translate", ["value" => check_string($_POST["name"]), "name" => check_string($_POST["name"]), "lang_id" => $lang["id"]]);
        }
    }
    if($CMSNT->num_rows("SELECT * FROM `translate` WHERE `name` = '" . check_string($_POST["name"]) . "' AND `lang_id` = '" . $row["id"] . "' ") < 1) {
        $isInsert = $CMSNT->insert("translate", ["value" => check_string($_POST["value"]), "name" => check_string($_POST["name"]), "lang_id" => $row["id"]]);
    }
    if(isset($isInsert)) {
        $Mobile_Detect = new Mobile_Detect();
        $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => "Add Translate (" . check_string($_POST["value"]) . ")."]);
        exit("<script type=\"text/javascript\">window.location=\"" . base_url_admin("translate-list&id=" . $id) . "\";</script>");
    }
    $CMSNT->update("translate", ["value" => check_string($_POST["value"]), "name" => check_string($_POST["name"]), "lang_id" => $row["id"]], " `name` = '" . check_string($_POST["name"]) . "' AND `lang_id` = '" . $row["id"] . "'  ");
    exit("<script type=\"text/javascript\">window.location=\"" . base_url_admin("translate-list&id=" . $id) . "\";</script>");
} else {
    if(isset($_POST["updateTranslate"])) {
        if($row["lang_default"] == 1) {
            exit("<script type=\"text/javascript\">if(!alert(\"" . __("You cannot format because this is the system default language") . "\")){window.history.back().location.reload();}</script>");
        }
        $isDelete = $CMSNT->remove("translate", " `lang_id` = '" . $row["id"] . "' ");
        if($isDelete) {
            foreach ($CMSNT->get_list("SELECT * FROM `translate` WHERE `lang_id` = '" . $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1 ")["id"] . "' ") as $tran) {
                $CMSNT->insert("translate", ["lang_id" => $row["id"], "value" => $tran["value"], "name" => $tran["name"]]);
            }
            $Mobile_Detect = new Mobile_Detect();
            $CMSNT->insert("logs", ["user_id" => $getUser["id"], "ip" => myip(), "device" => $Mobile_Detect->getUserAgent(), "createdate" => gettime(), "action" => __("Change language content.")]);
            exit("<script type=\"text/javascript\">if(!alert(\"Cập nhật thành công !\")){window.history.back().location.reload();}</script>");
        }
    }
    echo "\n \n\n<div class=\"main-content app-content\">\n    <div class=\"container-fluid\">\n        <div class=\"d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb\">\n            <h1 class=\"page-title fw-semibold fs-18 mb-0\">Translates ";
    echo $row["lang"];
    echo "</h1>\n            <div class=\"ms-md-1 ms-0\">\n                <nav>\n                    <ol class=\"breadcrumb mb-0\">\n                        <li class=\"breadcrumb-item\"><a href=\"";
    echo base_url_admin("language-list");
    echo "\">Languages</a></li>\n                        <li class=\"breadcrumb-item\"><a href=\"#\">";
    echo $row["lang"];
    echo "</a></li>\n                        <li class=\"breadcrumb-item active\" aria-current=\"page\">Translates</li>\n                    </ol>\n                </nav>\n            </div>\n        </div>\n        <div class=\"row\">\n            <div class=\"col-xl-7\">\n                <div class=\"card custom-card\">\n                    <div class=\"card-header justify-content-between\">\n                        <div class=\"card-title\">\n                            THÊM NỘI DUNG\n                        </div>\n                    </div>\n                    <div class=\"card-body\">\n                        <form action=\"\" method=\"POST\">\n                            <div class=\"form-group mb-3\">\n                                <label>Default:</label>\n                                <textarea class=\"form-control\" name=\"name\"\n                                    placeholder=\"Nhập nội dung tiếng việt mặc định\" required></textarea>\n                            </div>\n                            <div class=\"form-group mb-3\">\n                                <label>";
    echo $row["lang"];
    echo ":</label>\n                                <textarea class=\"form-control\" name=\"value\" placeholder=\"Nhập nội dung cần dịch\"\n                                    required></textarea>\n                            </div>\n                            <div class=\"d-grid gap-2 mb-4\">\n                                <button type=\"submit\" name=\"addTranslate\" class=\"btn btn-primary btn-wave\">THÊM\n                                    NGAY</button>\n                            </div>\n                        </form>\n                    </div>\n                </div>\n            </div>\n            <div class=\"col-xl-5\">\n                <div class=\"card custom-card\">\n                    <div class=\"card-header justify-content-between\">\n                        <div class=\"card-title\">\n                            LƯU Ý\n                        </div>\n                    </div>\n                    <div class=\"card-body\">\n                        <p>Hệ thống tự động cập nhật nội dung mới khi nội dung bạn thêm vào bị trùng lặp.</p>\n                        <p>Quý khách có thể sử dụng tính năng này để thay đổi nội dung trên website.</p>\n                    </div>\n                </div>\n            </div>\n            <div class=\"col-xl-12\">\n                <div class=\"card custom-card\">\n                    <div class=\"card-header justify-content-between\">\n                        <div class=\"card-title\">\n                            TRANSLATES\n                        </div>\n                        <button type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#exampleModalScrollable2\"\n                            class=\"btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light\"><i\n                                class='bx bx-reset'></i> Tạo lại bản dịch</button>\n                    </div>\n                    <div class=\"card-body\">\n                        <table id=\"datatable-basic\" class=\"table table-striped table-hover table-bordered\"\n                            style=\"width:100%\">\n                            <thead>\n                                <tr>\n                                    <th width=\"3%\">#</th>\n                                    <th>";
    echo __("Default");
    echo "</th>\n                                    <th>";
    echo $row["lang"];
    echo "</th>\n                                    <th width=\"20%\">";
    echo __("Action");
    echo "</th>\n                                </tr>\n                            </thead>\n                            <tbody>\n                                ";
    $i = 0;
    foreach ($CMSNT->get_list("SELECT * FROM `translate` WHERE `lang_id` = '" . $row["id"] . "' ORDER BY id DESC ") as $trans) {
        echo "                                <tr onchange=\"updateForm(`";
        echo $trans["id"];
        echo "`)\">\n                                    <td>";
        echo $i++;
        echo "</td>\n                                    <td>\n                                        <textarea class=\"form-control\" disabled>";
        echo $trans["name"];
        echo "</textarea>\n                                    </td>\n                                    <td>\n                                        <textarea class=\"form-control\"\n                                            id=\"value";
        echo $trans["id"];
        echo "\">";
        echo $trans["value"];
        echo "</textarea>\n                                    </td>\n                                    <td class=\"text-center\">\n                                        <div class=\"btn-list\">\n                                            <button type=\"button\" class=\"btn btn-primary-gradient btn-wave\"\n                                                onclick=\"autoTranslate('";
        echo $trans["id"];
        echo "', '";
        echo addslashes($trans["name"]);
        echo "', '";
        echo $row["code"];
        echo "', this)\">\n                                                <i class=\"ri-translate\"></i>\n                                                ";
        echo __("Dịch tự động");
        echo "                                            </button>\n                                            <button type=\"button\" class=\"btn btn-danger-gradient btn-wave\"\n                                                onclick=\"RemoveRow('";
        echo $trans["id"];
        echo "', '";
        echo addslashes($trans["name"]);
        echo "')\">\n                                                <i class=\"ri-delete-bin-line\"></i>\n                                                ";
        echo __("Delete");
        echo "                                            </button>\n                                        </div>\n\n                                    </td>\n                                </tr>\n                                ";
    }
    echo "                            </tbody>\n                        </table>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n\n";
    require_once __DIR__ . "/footer.php";
    echo "\n\n<div class=\"modal fade\" id=\"exampleModalScrollable2\" tabindex=\"-1\" aria-labelledby=\"exampleModalScrollable2\"\n    data-bs-keyboard=\"false\" aria-hidden=\"true\">\n    <!-- Scrollable modal -->\n    <div class=\"modal-dialog modal-dialog-centered modal-lg dialog-scrollable\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <h6 class=\"modal-title\" id=\"staticBackdropLabel2\">Cài lại nội dung mặc định</h6>\n                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n            </div>\n            <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">\n                <div class=\"modal-body\">\n                    <p>Hệ thống sẽ cập nhật lại nội dung giống ngôn ngữ\n                        <b>";
    echo $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1")["lang"];
    echo "</b>, bạn\n                        có chắc chắn muốn thực hiện thay đổi này không?\n                    </p>\n                </div>\n                <div class=\"modal-footer\">\n                    <button type=\"button\" class=\"btn btn-light \" data-bs-dismiss=\"modal\">Close</button>\n                    <button type=\"submit\" name=\"updateTranslate\" class=\"btn btn-primary shadow-primary btn-wave\"><i\n                            class=\"fa fa-fw fa-plus me-1\"></i>Xác nhận</button>\n                </div>\n            </form>\n        </div>\n    </div>\n</div>\n\n\n<script>\n/**\n * Hàm gọi API dịch tự động và hiển thị hiệu ứng spinner trên nút khi đang thực hiện.\n * @param {string} id - ID của bản ghi để cập nhật nội dung dịch vào textarea tương ứng.\n * @param {string} defaultText - Văn bản cần dịch (nội dung mặc định).\n * @param {string} targetLang - Mã ngôn ngữ đích (ví dụ: 'en' hoặc 'vi').\n * @param {HTMLElement} btn - Nút được click để hiển thị hiệu ứng spinner.\n */\nfunction autoTranslate(id, defaultText, targetLang, btn) {\n    // Kiểm tra nếu targetLang trống\n    if (!targetLang || targetLang.trim() === \"\") {\n        alert(\"Vui lòng cập nhật ISO CODE ngôn ngữ trước khi thực hiện dịch tự động!\");\n        // Chuyển hướng trang hiện tại\n        window.location.href = \"";
    echo base_url_admin("language-edit&id=" . $id);
    echo "\";\n        return;\n    }\n\n    // Lưu lại nội dung ban đầu của nút\n    const originalHTML = btn.innerHTML;\n    // Vô hiệu hóa nút và thêm spinner (sử dụng lớp của Bootstrap)\n    btn.disabled = true;\n    btn.innerHTML =\n        `<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> Translating...`;\n\n    // URL của API\n    const apiUrl = 'https://api.cmsnt.co/translation-api.php';\n    const url = `\${apiUrl}?license_key=";
    echo $CMSNT->site("license_key");
    echo "&q=\${encodeURIComponent(defaultText)}&target=\${encodeURIComponent(targetLang)}`;\n\n    // Gọi API bằng fetch\n    fetch(url)\n        .then(response => response.json())\n        .then(data => {\n            // Nếu có lỗi từ API\n            if (data.error) {\n                alert(\"Lỗi: \" + data.error);\n                btn.disabled = false;\n                btn.innerHTML = originalHTML;\n                return;\n            }\n            // Xử lý kết quả trả về\n            let translatedText = \"\";\n            if (data.data && data.data.translations && data.data.translations.length > 0) {\n                translatedText = data.data.translations[0].translatedText;\n                // Cập nhật giá trị cho textarea\n                document.getElementById(\"value\" + id).value = translatedText;\n                // Kích hoạt sự kiện change thủ công (dùng jQuery)\n                \$(\"#value\" + id).trigger(\"change\");\n\n            } else {\n                alert(\"Không nhận được kết quả dịch.\");\n                btn.disabled = false;\n                btn.innerHTML = originalHTML;\n                return;\n            }\n            // Cập nhật kết quả dịch vào textarea tương ứng\n            document.getElementById(\"value\" + id).value = translatedText;\n            // Khôi phục trạng thái nút\n            btn.disabled = false;\n            btn.innerHTML = originalHTML;\n        })\n        .catch(error => {\n            console.error(\"Có lỗi xảy ra: \", error);\n            alert(\"Có lỗi xảy ra khi dịch.\");\n            btn.disabled = false;\n            btn.innerHTML = originalHTML;\n        });\n}\n</script>\n\n\n\n\n\n<script type=\"text/javascript\">\nfunction updateForm(id) {\n    \$.ajax({\n        url: \"";
    echo BASE_URL("ajaxs/admin/update.php");
    echo "\",\n        method: \"POST\",\n        dataType: \"JSON\",\n        data: {\n            action: 'changeTranslate',\n            id: id,\n            value: \$(\"#value\" + id).val()\n        },\n        success: function(result) {\n            if (result.status == 'success') {\n                //showMessage(result.msg, result.status);\n            } else {\n                showMessage(result.msg, result.status);\n            }\n        },\n        error: function() {\n            alert(html(result));\n            location.reload();\n        }\n    });\n}\n\nfunction RemoveRow(id, name) {\n    cuteAlert({\n        type: \"question\",\n        title: \"Xác Nhận Xóa Ngôn Ngữ\",\n        message: \"Bạn có chắc chắn muốn xóa ngôn ngữ (\" + name + \") không ?\",\n        confirmText: \"Đồng Ý\",\n        cancelText: \"Hủy\"\n    }).then((e) => {\n        if (e) {\n            \$.ajax({\n                url: \"";
    echo BASE_URL("ajaxs/admin/remove.php");
    echo "\",\n                method: \"POST\",\n                dataType: \"JSON\",\n                data: {\n                    action: 'removeTranslate',\n                    id: id\n                },\n                success: function(result) {\n                    if (result.status == 'success') {\n                        showMessage(result.msg, result.status);\n                        location.reload();\n                    } else {\n                        showMessage(result.msg, result.status);\n                    }\n                },\n                error: function() {\n                    alert(html(result));\n                    location.reload();\n                }\n            });\n        }\n    })\n}\n</script>\n\n<script>\n\$('#datatable-basic').DataTable({\n    language: {\n        searchPlaceholder: 'Search...',\n        sSearch: '',\n    },\n    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, \"Tất cả\"]],\n    \"pageLength\": 50,\n    scrollX: true\n});\n</script>";
}

?>