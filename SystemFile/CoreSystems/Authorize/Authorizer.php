<?php
namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";

class Authorizer
{
    public function UserAuthorize($usermail, $password, $mode="auth")
    {
        $authorizer = new DatabaseConnector();
        $result = $authorizer->Initializer()->Select(["*"], "user")->Where("email", $usermail, "eq")->Run();
        $user = $result->MFetch();
        if($result !== false and password_verify($password, $user["password"])){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if($mode == "auth"){
                $_SESSION["loggedin"] = true;
                $_SESSION["user"] = $user["id"];
            }
            return true;
        }else{
            return "ユーザー名またはパスワードが違います。";
        }
    }

    public function IsAuthorized(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
            return true;
        }else{
            return false;
        }
    }

    public function UserGet($id){
        $authorizer = new DatabaseConnector();
        $user = $authorizer->Initializer()->Select(["*"], "user")->Where("id", (string)$id, "eq")->Run()->MFetch();
        return $user;
    }

    public function UserPasswordReset($email){
        $uuidprocessor = new UUIDCreator();
        $authorizer = new DatabaseConnector();
        $token = str_replace('-', '', $uuidprocessor->generate());
        $insertArray = [
            "email" => $email,
            "uuid" => $token
        ];
        $precheck = $authorizer->Initializer()->Select(["*"], "preuser")->Where("email", $email, "eq")->Run()->CountRow();
        $check = $authorizer->Initializer()->Select(["*"], "user")->Where("email", $email, "eq")->Run()->CountRow();
        if(isset($precheck) && isset($check)) {
            if($precheck == 0 && $check >= 1){
                $params = [
                    "time" => 24,
                    "url" => "http://localhost/auth/reset?token=".$token
                ];
                $mixer = new TurboMixer();
                $template = $mixer->SpecialMix("resetmailtemplate", $params)->Go();
                $mailer = new EmailSender();
                $result = $mailer->MailSend($template);
                if($result === True){
                    $authorizer->Initializer()->Insert("preuser", "Associative", $insertArray)->Run();
                }
            }else{
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION["error"] = "既にリセット用リンクが送信されているか、本登録されていません。";
                header("Location: /auth/reset");
            }

        }
        return True;
    }
}