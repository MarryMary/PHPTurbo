<?php

class TurboMixer
{
    private $template = Null;
    private $prepared = Null;
    public function SurfaceMix($viewFile, $RequestParam)
    {
        $this->template = file_get_contents(dirname(__FILE__) . "/../../../Surface/View_Template/" . $viewFile . ".gregorio.html");
        if (is_array($RequestParam)) {
            require_once "GregorioTemplateEngine.php";
            $TemplateEngine = new GregorioTemplateEngine();
            $this->template = $TemplateEngine->GregorioCore($this->template, $RequestParam);
        } else {
            //TODO
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
        $this->template = file_get_contents(dirname(__FILE__)."/../SpecialFile/".$viewFile.".gregorio.html");
        return $this;
    }

    public function Go()
    {
        return $this->template;
    }
}