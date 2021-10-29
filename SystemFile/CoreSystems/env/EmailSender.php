<?php

/*
* MailSendController used "PHPMailer" Library.
* PHPMailer Library version: v6.5.1
* About PHPMailer -> https://github.com/PHPMailer/PHPMailer
*/
namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{
    public function MailSend($template){
        $loader = new SystemFileReader();
        $system = $loader->SettingLoader();
        $lang = $loader->LangPackLoader();
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
            $mail->addAddress($_POST["email"]);

            $mail->isHTML(true);

            $mail->Subject = mb_encode_mimeheader('');
            $mail->Body  = mb_convert_encoding($template,"JIS","UTF-8");

            $mail->send();
            return True;
        } catch (Exception $e) {
            $exceprocessor = new ErrorProcessor();
            $exceprocessor->EchoError($system, $lang, "EmailException", dirname(__FILE__) . "/../../Log/", $e);
            exit;
        }
    }
}