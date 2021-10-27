<?php
require dirname(__FILE__)."/../../vendor/autoload.php";
class AccountDelController
{
    private $para;
    public function Controller(){
        require dirname(__FILE__)."/../CoreSystems/Mixer/TurboMixer.php";
        $juicer =  new TurboCore\TurboMixer;
        //TODO
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