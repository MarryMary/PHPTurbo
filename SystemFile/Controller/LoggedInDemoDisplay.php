<?php
require dirname(__FILE__)."/../../vendor/autoload.php";

class LoggedInDemoDisplay
{
    private $para;
    public function Controller(){
        $juicer =  new TurboCore\TurboMixer();
        $access = new TurboCore\CoreSystemAccessor();
        $userdata = $access->auth();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(isset($_SESSION["user"])){
            $userdata = $userdata->UserGet($_SESSION["user"]);
            $param = [
                "type" => "LemonadeChecker",
                "username" => $userdata["username"],
                "email" => $userdata["email"],
                "id" => $userdata["id"],
                "picture" => $userdata["profilepict"],
                "mode" => "On"
            ];
        }else{
            $param = [];
        }

        echo $juicer->SurfaceMix("AuthTemplate/loggedin", $param)->Go();
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