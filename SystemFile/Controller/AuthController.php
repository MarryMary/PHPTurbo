<?php

class AuthController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboMixer;
        require_once "CoreSystemAccessor.php";
        $authorizer = new CoreSystemAccessor();
        if($this->para == "login"){
            if($authorizer->Auth()->IsAuthorized()){
                if(isset($_SESSION["sendTo"])){
                    header("Location: ".$_SESSION["sendTo"]);
                }else{
                    require_once dirname(__FILE__)."/../CoreSystems/SystemFileReader/SysFileLoader.php";
                    $loader = new SystemFileReader();
                    $settings = $loader->SettingLoader();
                    header("Location: ".$settings["loggedInAccess"]);
                }
            }else{
                return $juicer->SurfaceMix("AuthTemplate/login", array())->Go();
            }
        }else if($this->para == "auth"){
            if(isset($_POST["email"]) && isset($_POST["password"])){
                $result = $authorizer->Auth()->UserAuthorize($_POST["email"], $_POST["password"]);
                if($result){
                    return $juicer->SurfaceMix("ControllerExample", array())->Go();
                }else{
                    return $juicer->SurfaceMix("AuthTemplate/login", array())->Go();
                }
            }else{
                require_once dirname(__FILE__)."/../CoreSystems/SystemFileReader/SysFileLoader.php";
                $loader = new SystemFileReader();
                $settings = $loader->SettingLoader();
                header("Location: /auth/login");
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