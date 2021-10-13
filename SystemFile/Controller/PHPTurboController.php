<?php

class PHPTurboController
{
    private $para;
    public function Controller(){
        $juicer =  new TurboMixer;
        return $juicer->SurfaceMix("ControllerExample", array())->Go();
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