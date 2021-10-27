<?php
require dirname(__FILE__)."/../../vendor/autoload.php";
class AuthController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboCore\TurboMixer;
        require_once "CoreSystemAccessor.php";
        $authorizer = new TurboCore\CoreSystemAccessor();
        if($this->para == "login"){
            if($authorizer->Auth()->IsAuthorized()){
                if(isset($_SESSION["sendTo"])){
                    header("Location: ".$_SESSION["sendTo"]);
                }else{
                    require_once dirname(__FILE__)."/../CoreSystems/SystemFileReader/SysFileLoader.php";
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
        }else if($this->para == "auth"){
            if(isset($_POST["email"]) && isset($_POST["password"])){
                $result = $authorizer->Auth()->UserAuthorize($_POST["email"], $_POST["password"]);
                if($result === True){
                    return $juicer->SurfaceMix("ControllerExample", array())->Go();
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
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] || isset($_SESSION["user"])){
                session_destroy();
                require_once dirname(__FILE__)."/../CoreSystems/SystemFileReader/SysFileLoader.php";
                $loader = new TurboCore\SystemFileReader();
                $settings = $loader->SettingLoader();
                header("Location: ".$settings["sendTo"]);
            }
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