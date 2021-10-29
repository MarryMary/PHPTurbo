<?php
require dirname(__FILE__)."/../../vendor/autoload.php";
class AuthController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboCore\TurboMixer;
        $authorizer = new TurboCore\CoreSystemAccessor();
        if($this->para == "login"){
            if($authorizer->Auth()->IsAuthorized()){
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                if(isset($_SESSION["sendTo"])){
                    $sendTo = $_SESSION["sendTo"];
                    unset($_SESSION["sendTo"]);
                    header("Location: ".$sendTo);
                }else{
                    $loader = new TurboCore\SystemFileReader();
                    $settings = $loader->SettingLoader();
                    header("Location: ".$settings["loggedInAccess"]);
                }
            }else{
                $err = array();
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                if(isset($_SESSION["err"])){
                    $err = ["err" => [$_SESSION["err"]]];
                    unset($_SESSION["err"]);
                }
                return $juicer->SurfaceMix("AuthTemplate/login", $err)->Go();
            }
        }else if($this->para == "reset"){
            if($authorizer->Auth()->IsAuthorized()){
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                if(isset($_SESSION["sendTo"])){
                    $sendTo = $_SESSION["sendTo"];
                    unset($_SESSION["sendTo"]);
                    header("Location: ".$sendTo);
                }else{
                    $loader = new TurboCore\SystemFileReader();
                    $settings = $loader->SettingLoader();
                    header("Location: ".$settings["loggedInAccess"]);
                }
            }else{
                $err = array();
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                if(isset($_SESSION["err"])){
                    $err = ["err" => [$_SESSION["err"]]];
                    unset($_SESSION["err"]);
                }if(isset($_POST["email"])){
                    $registerer = new TurboCore\CoreSystemAccessor();
                    $result = $registerer->Auth();
                    $return = $result->UserPasswordReset(htmlspecialchars($_POST["email"]));
                    if($return === True){
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION["preregistered"] = True;
                        header("Location: /register/finished");
                    }
                }
                return $juicer->SurfaceMix("AuthTemplate/passwordreset", $err)->Go();
            }
        }else if($this->para == "auth"){
            if(isset($_POST["email"]) && isset($_POST["password"])){
                $result = $authorizer->Auth()->UserAuthorize($_POST["email"], $_POST["password"]);
                if($result === True){
                    $loader = new TurboCore\SystemFileReader();
                    $settings = $loader->SettingLoader();
                    header("Location: ".$settings["loggedInAccess"]);
                }else{
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION["err"] = $result;
                    header("Location: /auth/login");
                }
            }else{
                header("Location: /auth/login");
            }
        }else if($this->para == "logout"){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION = array();
            session_destroy();
            $loader = new TurboCore\SystemFileReader();
            $settings = $loader->SettingLoader();
            header("Location: ".$settings["sendTo"]);
        }
    }

    public function paramSetter($param = False){
        if(!$param){
            //TODO
        }else{
            $this->para = $param;
        }
        return $this;
    }
}