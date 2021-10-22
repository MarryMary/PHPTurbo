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
                //TODO
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
                    return $juicer->SurfaceMix("AuthTemplate/finish", array())->Go();
                }
            }
        }else if($this->para == "insert"){
            require_once "CoreSystemAccessor.php";
            $registerer = new CoreSystemAccessor();
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