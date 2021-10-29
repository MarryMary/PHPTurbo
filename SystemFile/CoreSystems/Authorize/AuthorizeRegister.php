<?php
namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";

class AuthorizeRegister
{
    public function UserRegister($email, $password, $username)
    {
        $authorizer = new DatabaseConnector();
        $systemfile = new SystemFileReader();
        $sysfile = $systemfile->SettingLoader();
        $userpict = $sysfile["DefaultPict"];
        $pict = str_split($userpict);
        $readCommand = False;
        $readValue = False;
        $escaped = False;
        $inputDump = "";
        $commandDump = "";
        $valueDump = "";
        foreach($pict as $p){
            if($escaped === True){
                $escaped = False;
            }else{
                if($readCommand === True && $p == ">"){
                    $readCommand = False;
                    $variable = explode(",", $valueDump);
                    if(is_numeric($variable) && count($variable) == 2){
                        $variable[0] = (int)$variable[0];
                        $variable[1] = (int)$variable[1];
                    }
                    if($commandDump == "rand"){
                        $userpict = $inputDump.rand($variable[0], $variable[1]);
                    }
                }else if($readCommand === True && $p == "("){
                    $readValue = True;
                    $readCommand = False;
                }else if($readValue === True && $p == ")"){
                    $readValue = False;
                    $readCommand = True;
                }else if($readValue == True){
                    $valueDump .= $p;
                }else if($readCommand === True){
                    $commandDump .= $p;
                }else if($p == "<"){
                    $readCommand = True;
                }else if($p == "\\"){
                    echo "f";
                    $escaped = True;
                }else{
                    $inputDump .= $p;
                }
            }
        }
        $insertArray = [
            "email" => $email,
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "profilepict" => $userpict
        ];
        $result = $authorizer->Initializer()->Insert("user", "Associative", $insertArray)->Run();
        $authorizer->Initializer()->DELETE("preuser")->Where("email", $email, "eq")->Run();
        if($result == True){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION["alldone"] = True;
            header("Location: /register/alldone");
        }
    }

    public function PreUserRegister($email){
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
            if($precheck == 0 && $check == 0){
                $params = [
                    "time" => 24,
                    "url" => "http://localhost/register/form?token=".$token
                ];
                $mixer = new TurboMixer();
                $template = $mixer->SpecialMix("registermailtemplate", $params)->Go();
                $mailer = new EmailSender();
                $result = $mailer->MailSend($template);
                if($result === True){
                    $authorizer->Initializer()->Insert("preuser", "Associative", $insertArray)->Run();
                }
            }else{
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION["error"] = "このメールアドレスは既に使用されています。";
                header("Location: /register/preform");
            }

        }
        return True;
    }
}