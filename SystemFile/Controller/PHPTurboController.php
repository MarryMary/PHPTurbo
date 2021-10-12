<?php

class PHPTurboController
{
    function Controller(){
        require dirname(__FILE__)."/../CoreSystems/Mixer/TurboMixer.php";
        $juicer =  new TurboMixer;
        //TODO
    }
}