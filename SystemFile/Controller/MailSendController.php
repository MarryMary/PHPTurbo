<?php

class MailSendController
{
    private $para;
    public function Controller(){
        require dirname(__FILE__)."/../CoreSystems/Mixer/TurboMixer.php";
        $juicer =  new TurboMixer();

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