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
                //TODO
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
                return $juicer->SurfaceMix("AuthTemplate/login", array())->Go();
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