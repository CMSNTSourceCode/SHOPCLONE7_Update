<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
define("IN_SITE", true);
require_once __DIR__ . "/libs/db.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/libs/helper.php";
$CMSNT = new DB();
$whitelist = ["127.0.0.1", "::1"];
$arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
if(in_array($_SERVER["REMOTE_ADDR"], $whitelist)) {
    exit("Localhost không thể sử dụng chức năng này");
}
if($CMSNT->site("status_update") == 1) {
    if($config["version"] != curl_get_contents("https://raw.githubusercontent.com/CMSNTSourceCode/SHOPCLONE7_Update/refs/heads/main/version.txt", 3)) {
        define("filename", "update_" . random("ABC123456789", 6) . ".zip");
        define("serverfile", "http://github.com/CMSNTSourceCode/SHOPCLONE7_Update/raw/refs/heads/main/update.zip");
        file_put_contents(filename, curl_get_contents(serverfile, 30));
        $file = filename;
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        $zip = new ZipArchive();
        $res = $zip->open($file);
        if($res === true) {
            $zip->extractTo($path);
            $zip->close();
            unlink(filename);
            $query = file_get_contents(BASE_URL("install.php"), false, stream_context_create($arrContextOptions));
            if($query) {
                unlink("install.php");
            }
            $file = @fopen("Update.txt", "a");
            if($file) {
                $data = "[UPDATE] Phiên cập nhật phiên bản gần nhất vào lúc " . gettime() . PHP_EOL;
                fwrite($file, $data);
                fclose($file);
            }
            exit("Cập nhật thành công!");
        }
        exit("Cập nhật thất bại!");
    }
    exit("Không có phiên bản mới nhất");
}
exit("Chức năng cập nhật tự động đang được tắt");

?>