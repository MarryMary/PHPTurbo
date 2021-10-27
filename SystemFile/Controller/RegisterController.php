<?php
require dirname(__FILE__)."/../../vendor/autoload.php";

class RegisterController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboCore\TurboMixer();
        if($this->para == "preform"){
            return $juicer->SurfaceMix("AuthTemplate/preregister", array())->Go();
        }else if($this->para == "form"){
            if(isset($_GET["token"])){
                //TODO
            }else{
                return $juicer->SpecialMix("FtF")->Go();
            }
        }else if($this->para == "preinsert"){
            if(isset($_POST["email"])) {
                require_once "CoreSystemAccessor.php";
                $registerer = new TurboCore\CoreSystemAccessor();
                $result = $registerer->AuthRegi();
                $return = $result->PreUserRegister(htmlspecialchars($_POST["email"]));
                if($return === True){
                    return $juicer->SurfaceMix("AuthTemplate/finish", array())->Go();
                }
            }else{
                header("Location: /register/preform");
            }
        }else if($this->para == "insert"){
            require_once "CoreSystemAccessor.php";
            $registerer = new TurboCore\CoreSystemAccessor();
            $result = $registerer->AuthRegi();
            #$result = UserRegister($email, $password, $username);
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