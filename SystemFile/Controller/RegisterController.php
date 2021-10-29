<?php
require dirname(__FILE__)."/../../vendor/autoload.php";

class RegisterController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboCore\TurboMixer();
        if($this->para == "preform"){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $param = array();
            $_SESSION = array();
            session_destroy();
            if(isset($_SESSION["error"])){
                $param["err"] = [$_SESSION["error"]];
                unset($_SESSION["error"]);
            }
            return $juicer->SurfaceMix("AuthTemplate/preregister", $param)->Go();
        }else if($this->para == "form"){
            if(isset($_GET["token"])){
                $dbconnection = new TurboCore\DatabaseConnector();
                $dbconnection->Initializer()->Self("DELETE FROM preuser WHERE regdate <= SYSDATE() - interval 1 day")->Run();
                $precheck = $dbconnection->Initializer()->Select(["*"], "preuser")->Where("uuid", $_GET["token"], "eq")->Run()->CountRow();
                if($precheck != 0){
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $email = $dbconnection->Initializer()->Select(["*"], "preuser")->Where("uuid", $_GET["token"], "eq")->Run()->MFetch();
                    $_SESSION["email"] = $email["email"];
                    $_SESSION["token"] = $_GET["token"];
                    $param = [
                        "email" => $email["email"]
                    ];
                    if(isset($_SESSION["error"])){
                        $param["err"] = [$_SESSION["error"]];
                        unset($_SESSION["error"]);
                    }
                    return $juicer->SurfaceMix("AuthTemplate/register", $param)->Go();
                }else{
                    return $juicer->SpecialMix("FtF")->Go();
                }
            }else{
                return $juicer->SpecialMix("FtF")->Go();
            }
        }else if($this->para == "preinsert"){
            if(isset($_POST["email"])) {
                $registerer = new TurboCore\CoreSystemAccessor();
                $result = $registerer->AuthRegi();
                $return = $result->PreUserRegister(htmlspecialchars($_POST["email"]));
                if($return === True){
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION["preregistered"] = True;
                    header("Location: /register/finished");
                }
            }else{
                header("Location: /register/preform");
            }
        }else if($this->para == "finalcheck"){
            $registerer = new TurboCore\CoreSystemAccessor();
            $sysdata = new TurboCore\SystemFileReader();
            $system = $sysdata->SettingLoader();
            $result = $registerer->AuthRegi();
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if(isset($_SESSION["email"]) && isset($_POST["password1"]) && isset($_POST["password2"]) && isset($_POST["username"])){
                $password = "";
                if($_POST["password1"] == $_POST["password2"]){
                    if(strlen($_POST["password1"]) >= (int)$system["passwordMinLength"]){
                        /* 
                            "passwordIsSymbol": "True",
                            "passwordSymbolMust": "True",
                            "passwordIsUpper": "True",
                            "passwordUpperMust": "True",
                            "passwordIsNumeric": "True",
                            "passwordNumericMust": "True",
                        */
                        $password = "";
                        for($i=0; $i < strlen($_POST["password1"]);$i++){
                            $password .= "●";
                        }
                        $param = [
                            "email" => $_SESSION["email"],
                            "password" => $password,
                            "username" => $_POST["username"],
                            "token" => $_SESSION["token"]
                        ];
                        $_SESSION["password"] = $_POST["password1"];
                        $_SESSION["username"] = $_POST["username"];
                        return $juicer->SurfaceMix("AuthTemplate/finalcheck", $param)->Go();
                    }else{
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION["error"] = "パスワード長は最大{$system["passwordMinLength"]}文字である必要があります。";
                        header("Location: /register/form?token=".$_SESSION["token"]);
                    }
                }else{
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION["error"] = "パスワードとパスワード（確認用）の入力が異なります。どちらも同じパスワードを入力して下さい。";
                    header("Location: /register/form?token=".$_SESSION["token"]);
                }
            }else{
                header("Location: /register/preform");
            }
        }else if($this->para == "insert"){
            $registerer = new TurboCore\CoreSystemAccessor();
            $result = $registerer->AuthRegi();
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if(isset($_SESSION["email"]) && isset($_SESSION["password"]) && isset($_SESSION["username"])){
                $result->UserRegister($_SESSION["email"], $_SESSION["password"], $_SESSION["username"]);
            }else{
                header("Location: /register/preform");
            }
        }else if($this->para == "finished"){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if($_SESSION["preregistered"] === True){
                return $juicer->SurfaceMix("AuthTemplate/finish", array())->Go();
            }else{
                header("Location: /register/preform");
            }
        }else if($this->para == "alldone"){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if($_SESSION["alldone"] === True){
                $_SESSION = array();
                session_destroy();
                return $juicer->SurfaceMix("AuthTemplate/alldone", array())->Go();
            }else{
                header("Location: /register/preform");
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