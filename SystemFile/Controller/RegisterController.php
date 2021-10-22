<?php

class RegisterController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboMixer;
        if($this->para == "preform"){
            return $juicer->SurfaceMix("AuthTemplate/preregister", array())->Go();
        }else if($this->para == "form"){
            if(isset($_GET["token"])){
                require_once dirname(__FILE__)."/../CoreSystems/Database/DatabaseConnector.php";
                $authorizer = new DatabaseConnector();
                $tokencheck = $authorizer->Initializer()->Select(["*"], "preuser")->Where("token", htmlspecialchars(urldecode($_GET["token"])), "eq")->Run()->CountRow();
                if($tokencheck >= 1){
                    return $juicer->SurfaceMix("AuthTemplate/register", array())->Go();
                }else{
                    //TODO トークン存在チェック
                }
            }else{
                return $juicer->SpecialMix("FtF")->Go();
            }
        }else if($this->para == "preinsert"){
            if(isset($_POST["email"])) {
                require_once "CoreSystemAccessor.php";
                $registerer = new CoreSystemAccessor();
                $result = $registerer->AuthRegi();
                $return = $result->PreUserRegister(htmlspecialchars($_POST["email"]));
                if($return){
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION["finish"] = True;
                    header("Location: /register/finish");
                }
            }
        }else if($this->para == "insert"){
            require_once "CoreSystemAccessor.php";
            $registerer = new CoreSystemAccessor();
            $result = $registerer->AuthRegi();
            if(isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["username"])){
                $result->UserRegister($_POST["email"], $_POST["password"], $_POST["username"]);
            }
        }else if($this->para == "finish"){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if(isset($_SESSION["finish"]) && $_SESSION["finish"]){
                return $juicer->SurfaceMix("AuthTemplate/finish", array())->Go();
                $_SESSION["finish"] = False;
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