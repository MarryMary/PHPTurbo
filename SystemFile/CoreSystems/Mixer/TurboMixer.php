<?php

class TurboMixer
{
    private $template = Null;
    private $prepared = Null;
    public function SurfaceMix($viewFile, $RequestParam)
    {
        $MagicKey = "e79a86e38195e38293e381afe381a9e3828ce3818fe38289e38184e381aee69982e99693e382aae3838ae3838be383bce38197e381a6e381bee38199e3818befbc9f";
        if($viewFile == $MagicKey){
            $this->template = file_get_contents(dirname(__FILE__)."/../SpecialFile/GameTurbo.html");
            return $this;
        }else {
            $this->template = file_get_contents(dirname(__FILE__) . "/../../../Surface/View_Template/" . $viewFile . ".html");
            if (is_array($RequestParam)) {
                require_once "PHPVtec.php";
                $TemplateEngine = new PHPVtec();
                $this->template = $TemplateEngine->VTECTemplateEngine($this->template, $RequestParam);
            } else {
                //TODO
            }
        }
        return $this;
    }

    public function ResourceMix($viewFile, $type){
        if($type == "css"){
            header('Content-Type: text/css;', 'charset=utf-8');
            $this->template = file_get_contents(dirname(__FILE__)."/../../../Surface/View_Template/css/".$viewFile.".css");
        }else if($type == "js"){
            header('Content-Type: text/javascript;', 'charset=utf-8');
            $this->template = file_get_contents(dirname(__FILE__)."/../../../Surface/View_Template/js/".$viewFile.".js");
        }else if($type == "pict"){
            header('Content-Type: image/png');
            $ext = [".jpg", ".png", ".gif", ".jpeg"];
            foreach($ext as $extension){
                $filepath = dirname(__FILE__)."/../../../Surface/View_Template/picture/".$viewFile.$extension;
                if(is_readable($filepath)){
                    readfile($filepath);
                    break;
                }
            }
        }
        return $this;
    }

    public function SpecialMix($viewFile){
        $this->template = file_get_contents(dirname(__FILE__)."/../SpecialFile/".$viewFile.".html");
        return $this;
    }

    public function Go()
    {
        return $this->template;
    }
}