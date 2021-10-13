<?php

class PHPTurboController
{
    function Controller(){
        $juicer =  new TurboMixer;
        return $juicer->SurfaceMix("ControllerExample", array())->Go();
    }
}