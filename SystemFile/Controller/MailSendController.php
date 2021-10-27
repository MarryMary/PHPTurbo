<?php
require dirname(__FILE__)."/../../vendor/autoload.php";

class MailSendController
{
    private $para;
    public function Controller(){
        require dirname(__FILE__)."/../CoreSystems/Mixer/TurboMixer.php";
        $juicer =  new TurboCore\TurboMixer();

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