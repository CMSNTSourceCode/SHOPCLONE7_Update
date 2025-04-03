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
function sendCSM($mail_nhan, $ten_nhan, $chu_de, $noi_dung, $bcc = "", $path = "")
{
    $CMSNT = new DB();
    if($noi_dung == "") {
        return false;
    }
    if($CMSNT->site("smtp_status") == 1) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = "html";
        $mail->isSMTP();
        $mail->Host = $CMSNT->site("smtp_host");
        $mail->SMTPAuth = true;
        $mail->Username = $CMSNT->site("smtp_email");
        $mail->Password = $CMSNT->site("smtp_password");
        $mail->SMTPSecure = $CMSNT->site("smtp_encryption");
        $mail->Port = $CMSNT->site("smtp_port");
        $mail->setFrom($CMSNT->site("smtp_email"), $bcc);
        $mail->addAddress($mail_nhan, $ten_nhan);
        $mail->addAttachment($path);
        $mail->addReplyTo($CMSNT->site("smtp_email"), $bcc);
        $mail->isHTML(true);
        $mail->Subject = $chu_de;
        $mail->Body = $noi_dung;
        $mail->CharSet = "UTF-8";
        $send = $mail->send();
        return $send;
    }
    return "Chưa cấu hình SMTP";
}

?>