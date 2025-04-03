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
function getOrder_API_26($domain, $api_key, $token, $invoice)
{
    global $CMSNT;
    $allowed_domains = explode(",", $CMSNT->site("domains"));
    $api_key = explode("|", $api_key);
    $token = explode("|", $token);
    list($public_key, $private_key) = $token;
    list($email, $token_pay, $key, $key_createorder) = $api_key;
    $host = $token[2];
    $curl = curl_init($domain . "api/downloadtxt/" . $invoice);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["LEQUE-KEY-API-PUB: " . $public_key, "LEQUE-KEY-API-PRIV: " . $private_key, "HOST: " . $host], CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false]);
    $response = curl_exec($curl);
    curl_close($curl);
    if($response && 0 < strlen($response)) {
        return htmlspecialchars($response);
    }
    return __("Please contact Admin to get order");
}
function buy_API_26($domain, $api_key, $token, $api_id, $amount)
{
    global $CMSNT;
    $allowed_domains = explode(",", $CMSNT->site("domains"));
    $api_key = explode("|", $api_key);
    $token = explode("|", $token);
    list($public_key, $private_key) = $token;
    list($email, $token_pay, $key, $key_createorder) = $api_key;
    $host = $token[2];
    $curl = curl_init();
    $data = ["email" => $email, "key" => $key_createorder, "count" => $amount, "type" => $api_id, "fund" => "13", "success_url" => basename(""), "token_pay" => $token_pay];
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/createorder", CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "webApi", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($data), CURLOPT_HTTPHEADER => ["LEQUE-KEY-API-PUB: " . $public_key, "LEQUE-KEY-API-PRIV: " . $private_key, "HOST: " . $host]]);
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);
    if(isset($response["ok"]) && $response["ok"] == "TRUE") {
        $invoice = $response["invoice"];
        $curl = curl_init();
        $data = ["pay" => "yes", "email_pay" => $email, "token_pay" => $token_pay];
        curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/paybalance/" . $invoice, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "webApi", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($data), CURLOPT_HTTPHEADER => ["LEQUE-KEY-API-PUB: " . $public_key, "LEQUE-KEY-API-PRIV: " . $private_key, "HOST: " . $host]]);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    return json_encode($response);
}
function listProduct_API_26($domain, $api_key, $token)
{
    global $CMSNT;
    $allowed_domains = explode(",", $CMSNT->site("domains"));
    $api_key = explode("|", $api_key);
    $token = explode("|", $token);
    list($public_key, $private_key) = $token;
    list($email, $token_pay, $key) = $api_key;
    $host = $token[2];
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/goods?key=" . $key, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "webApi", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_HTTPHEADER => ["LEQUE-KEY-API-PUB: " . $public_key, "LEQUE-KEY-API-PRIV: " . $private_key, "HOST: " . $host]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function balance_API_26($domain, $api_key, $token)
{
    global $CMSNT;
    $allowed_domains = explode(",", $CMSNT->site("domains"));
    $api_key = explode("|", $api_key);
    $token = explode("|", $token);
    list($public_key, $private_key) = $token;
    list($email, $token_pay) = $api_key;
    $host = $token[2];
    $curl = curl_init();
    $data = ["email" => $email, "token_pay" => $token_pay];
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/balanceuser", CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "webApi", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($data), CURLOPT_HTTPHEADER => ["LEQUE-KEY-API-PUB: " . $public_key, "LEQUE-KEY-API-PRIV: " . $private_key, "HOST: " . $host]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function buy_API_25($domain, $api_key, $id_api, $amount)
{
    return curl_get($domain . "purchase?apikey=" . $api_key . "&accountcode=" . $id_api . "&quantity=" . $amount);
}
function listProduct_API_25($domain)
{
    return curl_get($domain . "instock");
}
function balance_API_25($domain, $apikey)
{
    return curl_get($domain . "balance?apikey=" . $apikey);
}
function balance_API_24($domain, $api_key)
{
    return curl_get2($domain . "api/checkapikey=" . $api_key);
}
function buy_API_24($domain, $api_key, $api_id, $amount)
{
    return curl_get($domain . "api/byproduct/apikey=" . $api_key . "&product_id=" . $api_id . "&quality=" . $amount);
}
function listProduct_API_24($domain, $api_key)
{
    return curl_get($domain . "api/checkprice=" . $api_key);
}
function buy_API_23($domain, $api_key, $api_id, $amount)
{
    return curl_get($domain . "purchase?api_key=" . $api_key . "&accountcode=" . $api_id . "&quantity=" . $amount);
}
function listProduct_API_23($domain)
{
    return curl_get($domain . "instock");
}
function balance_API_23($domain, $api_key)
{
    return curl_get2($domain . "balance?api_key=" . $api_key);
}
function buy_API_22($domain, $token, $product_id, $amount)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/buyHotMailUd", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["quantity" => $amount, "token" => $token, "product_id" => $product_id]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_22($domain, $token)
{
    return curl_get($domain . "api/quantity?token=" . $token);
}
function buy_API_17($domain, $username, $password, $api_id, $amount)
{
    return curl_get2($domain . "/api/BResource.php?username=" . $username . "&password=" . $password . "&id=" . $api_id . "&amount=" . $amount);
}
function listProduct_API_17($domain, $username, $password)
{
    return curl_get2($domain . "/api/CategoryList.php?username=" . $username . "&password=" . $password);
}
function balance_API_17($domain, $username, $password)
{
    return curl_get($domain . "/api/GetBalance.php?username=" . $username . "&password=" . $password);
}
function buy_API_21($domain, $token, $product_id, $amount)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/buy-products", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["quantity" => $amount, "token" => $token, "product_id" => $product_id]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_21($domain, $token)
{
    return curl_get($domain . "api/quantity?token=" . $token);
}
function buy_API_9($domain, $password, $dataPost)
{
    $data = json_encode($dataPost);
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "v1/api/buy?api_key=" . $password, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => $data, CURLOPT_HTTPHEADER => ["Content-Type: application/json"]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_9($domain, $password)
{
    return curl_get($domain . "v1/api/categories?api_key=" . $password);
}
function balance_API_9($domain, $password)
{
    return curl_get($domain . "v1/api/me?api_key=" . $password);
}
function buy_API_4($domain, $token, $id_product, $amount)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "v1/user/partnerbuy", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["amount" => $amount, "categoryId" => $id_product], CURLOPT_HTTPHEADER => ["authorization: " . $token]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function balance_API_4($domain, $username, $password)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "v1/user/login", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["username" => $username, "password" => $password]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_4($domain)
{
    return curl_get2($domain . "v1/public/category/list");
}
function buy_API_19($domain, $api_key, $id_api, $amount)
{
    return curl_get2($domain . "user/buy?apikey=" . $api_key . "&account_type=" . $id_api . "&quality=" . $amount . "&type=null");
}
function listProduct_API_19($domain, $api_key)
{
    return curl_get2($domain . "user/account_type?apikey=" . $api_key);
}
function balance_API_19($domain, $api_key)
{
    return curl_get2($domain . "user/balance?apikey=" . $api_key);
}
function buy_API_18($domain, $api_key, $id_api, $amount)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "mail/buy?mailcode=" . $id_api . "&quantity=" . $amount, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $api_key]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_18($domain)
{
    return curl_get($domain . "mail/currentstock");
}
function balance_API_18($domain, $apikey)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "auth/me", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $apikey]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function buy_API_SHOPCLONE7($domain, $coupon, $api_key, $id_api, $amount, $proxy = "")
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "/ajaxs/client/product.php", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["action" => "buyProduct", "id" => $id_api, "amount" => $amount, "coupon" => $coupon, "api_key" => $api_key], CURLOPT_HTTPHEADER => ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"]]);
    if(!empty($proxy)) {
        $proxy_parts = explode(":", $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2] . ":" . $proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_SHOPCLONE7($domain, $api_key, $proxy = "")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $domain . "/api/products.php?api_key=" . $api_key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"]);
    if(!empty($proxy)) {
        $proxy_parts = explode(":", $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_parts[2] . ":" . $proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
        }
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function balance_API_SHOPCLONE7($domain, $api_key, $proxy = "")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $domain . "/api/profile.php?api_key=" . $api_key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"]);
    if(!empty($proxy)) {
        $proxy_parts = explode(":", $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_parts[2] . ":" . $proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
        }
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function buy_API_SHOPCLONE6($domain, $username, $password, $api_id, $amount, $proxy = "")
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "/api/BResource.php?username=" . $username . "&password=" . $password . "&id=" . $api_id . "&amount=" . $amount, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 60, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_HTTPHEADER => ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"]]);
    if(!empty($proxy)) {
        $proxy_parts = explode(":", $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2] . ":" . $proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_SHOPCLONE6($domain, $username, $password, $proxy = "")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $domain . "/api/ListResource.php?username=" . $username . "&password=" . $password);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"]);
    if(!empty($proxy)) {
        $proxy_parts = explode(":", $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_parts[2] . ":" . $proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_parts[0] . ":" . $proxy_parts[1]);
        }
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function balance_API_SHOPCLONE6($domain, $username, $password, $proxy = "")
{
    $url = $domain . "/api/GetBalance.php?username=" . $username . "&password=" . $password;
    $opts = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false], "http" => ["header" => ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"]]];
    if(!empty($proxy)) {
        $proxy_parts = explode(":", $proxy);
        if(count($proxy_parts) == 4) {
            $proxy = "tcp://" . $proxy_parts[0] . ":" . $proxy_parts[1];
            $opts["http"]["proxy"] = $proxy;
            $opts["http"]["request_fulluri"] = true;
            $opts["http"]["header"][] = "Proxy-Authorization: Basic " . base64_encode($proxy_parts[2] . ":" . $proxy_parts[3]);
        } elseif(count($proxy_parts) == 2) {
            $proxy = "tcp://" . $proxy_parts[0] . ":" . $proxy_parts[1];
            $opts["http"]["proxy"] = $proxy;
            $opts["http"]["request_fulluri"] = true;
        }
    }
    return file_get_contents($url, false, stream_context_create($opts));
}
function getOrder_API_14($domain, $token, $order_id)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_HTTPHEADER => ["Authorization: " . $token], CURLOPT_POSTFIELDS => "{\n            \"act\": \"Get-Order\",\n            \"data\": {\n                \"order_id\": " . $order_id . "\n            }\n        }"]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function buy_API_14($domain, $token, $id_api, $amount)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_HTTPHEADER => ["Authorization: " . $token], CURLOPT_POSTFIELDS => "{\n        \"act\": \"Create-Order\",\n        \"data\": {\n            \"service_id\": " . $id_api . ",\n            \"quantity\": " . $amount . "\n        }\n    }"]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_14($domain, $token)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["act" => "Get-Products"], CURLOPT_HTTPHEADER => ["Authorization: " . $token]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function balance_API_14($domain, $token)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["act" => "Me"], CURLOPT_HTTPHEADER => ["Authorization: " . $token]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function balance_API_6($domain, $api_key)
{
    return curl_get($domain . "/api.php?apikey=" . $api_key . "&action=get-balance");
}
function balance_API_1($domain, $token)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/v1/balance", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 10, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["api_key" => $token]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function listProduct_API_1($domain)
{
    return curl_get2($domain . "api/v1/categories");
}
function buy_API_1($domain, $dataPost)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/v1/buy", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => $dataPost]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function order_API_1($domain, $api_key, $order_id)
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $domain . "api/v1/order", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => ["api_key" => $api_key, "order_id" => $order_id]]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

?>