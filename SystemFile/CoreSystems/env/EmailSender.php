<?php

/*
* MailSendController used "PHPMailer" Library.
* PHPMailer Library version: v6.5.1
* About PHPMailer -> https://github.com/PHPMailer/PHPMailer
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{
    public function MailSend($address, $title, $template){
        require_once dirname(__FILE__)."/../CoreSystems/SystemFileReader/SysFileLoader.php";
        $loader = new SystemFileReader();
        $system = $loader->SettingLoader();
        require_once 'vendor/autoload.php';
        mb_language("japanese");
        mb_internal_encoding("UTF-8");
        $mail = new PHPMailer(true);

        $mail->CharSet = "iso-2022-jp";
        $mail->Encoding = "7bit";

        $mail->setLanguage('ja', 'vendor/phpmailer/phpmailer/language/');

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = $system["mailHost"];
            $mail->SMTPAuth   = true;
            $mail->Username   = $system["mailUser"];
            $mail->Password   = $system["mailPassword"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $system["mailPort"];


            $mail->setFrom($system["mailFrom"], mb_encode_mimeheader($system["mailName"]));
            $mail->addAddress($address);

            $mail->isHTML(true);

            $mail->Subject = mb_encode_mimeheader($title);
            $mail->Body  = mb_convert_encoding($template,"JIS","UTF-8");

            $mail->send();
            return True;
        } catch (Exception $e) {
            return $e;
        }
    }
}