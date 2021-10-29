<?php
require dirname(__FILE__)."/../../vendor/autoload.php";
class AccountDelController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboCore\TurboMixer;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(isset($_POST["password"])){
            $access = new TurboCore\CoreSystemAccessor();
            $userdata = $access->Auth()->UserAuthorize($_SESSION["email"], $_POST["password"], "check");
            if($userdata == True) {
                $dbconnector = new TurboCore\DatabaseConnector();
                $dbconnector->Initializer()->Delete("user")->Where("id", (string)$_SESSION["user"], "eq")->Run();
                $dbconnector->Initializer()->Delete("preuser")->Where("email", $_SESSION["email"], "eq")->Run();
                $_SESSION = array();
                session_destroy();
                header("Location: /");
            }
        }else if(isset($_SESSION["user"])){
            $access = new TurboCore\CoreSystemAccessor();
            $userdata = $access->Auth()->UserGet($_SESSION["user"]);
            $username = $userdata["username"];
            $_SESSION["email"] = $userdata["email"];
            $param = [
                "username" => $username
            ];
            if(isset($_SESSION["error"])){
                $param["err"] = $_SESSION["error"];
                unset($_SESSION["error"]);
            }
            echo $juicer->SurfaceMix("AuthTemplate/deletecheck", $param)->Go();
        }else{
            header("Location: /auth/login");
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