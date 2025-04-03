<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
function generateSecretKey_Google2FA()
{
    $google2fa = new PragmaRX\Google2FAQRCode\Google2FA();
    return $google2fa->generateSecretKey();
}

?>