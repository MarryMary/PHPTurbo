<?php

class RegisterController
{
    private $para;
    public function Controller(){
        require dirname(__FILE__)."/../CoreSystems/Mixer/TurboMixer.php";
        $juicer =  new TurboMixer;
        require_once "CoreSystemAccessor.php";
        $registerer = new CoreSystemAccessor();
        $result = $register->AuthRegi();
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