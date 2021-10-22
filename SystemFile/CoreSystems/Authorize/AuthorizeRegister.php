<?php

class AuthorizeRegister
{
    public function UserRegister($email, $password, $username)
    {
        require_once dirname(__FILE__)."/../SystemFileReader/SysFileLoader.php";
        $systemfile = new SystemFileReader();
        $sysfile = $systemfile->SettingLoader();
        require_once dirname(__FILE__)."/../Database/DatabaseConnector.php";
        $authorizer = new DatabaseConnector();
        $userpict = $sysfile["DefaultPict"];
        $insertArray = [
            "email" => htmlspecialchars($email),
            "username" => htmlspecialchars($username),
            "password" => password_hash(htmlspecialchars($password), PASSWORD_DEFAULT),
            "profilepict" => $userpict
        ];
        $precheck = $authorizer->Initializer()->Select(["*"], "preuser")->Where("email", htmlspecialchars($email), "eq")->Run()->CountRow();
        $check = $authorizer->Initializer()->Select(["*"], "user")->Where("email", htmlspecialchars($email), "eq")->Run()->CountRow();
        if(isset($precheck) && isset($check) && $precheck >= 1 && $check == 0){
            $authorizer->Initializer()->Insert("user", "Associative", $insertArray)->Run();
            $authorizer->Initializer()->Delete("preuser")->Where("email", htmlspecialchars($email), "eq")->Run();
        }
    }

    public function PreUserRegister($email){
        require_once dirname(__FILE__)."/../env/UniqueIDProcessor.php";
        $uuidprocessor = new UUIDCreator();
        require_once dirname(__FILE__)."/../Database/DatabaseConnector.php";
        $authorizer = new DatabaseConnector();
        require_once dirname(__FILE__)."/../SystemFileReader/SysFileLoader.php";
        $systemfile = new SystemFileReader();
        $sysfile = $systemfile->SettingLoader();
        $token = str_replace('-', '', $uuidprocessor->generate());
        $insertArray = [
            "email" => $email,
            "token" => $token
        ];
        $precheck = $authorizer->Initializer()->Select(["*"], "preuser")->Where("email", $email, "eq")->Run()->CountRow();
        $check = $authorizer->Initializer()->Select(["*"], "user")->Where("email", $email, "eq")->Run()->CountRow();
        if(isset($precheck) && isset($check) && $precheck == 0 && $check == 0) {
            require_once dirname(__FILE__)."/../env/EmailSender.php";
            $mailTitle = "仮登録受付完了のお知らせ";
            $mailMain = file_get_contents(dirname(__FILE__)."/../SpecialFile/registermailtemplate.gregorio.html");
            $mailer = new EmailSender();
            $token = urlencode($token);
            $params = [
                "url" => $sysfile["AppURL"]."/register/form?token=".$token,
                "time" => "24時間"
            ];
            $result = $mailer->MailSend($email, $mailTitle, $mailMain, $params);
            if($result){
                $authorizer->Initializer()->Insert("preuser", "Associative", $insertArray)->Run();
            }else{
                echo $result;
            }
        }else{
            //TODO 重複処理
        }
        return True;
    }
}