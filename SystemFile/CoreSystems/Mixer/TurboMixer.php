<?php
namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";
class TurboMixer
{
    private $template = Null;
    private $prepared = Null;
    public function SurfaceMix($viewFile, $RequestParam)
    {
        $this->template = file_get_contents(dirname(__FILE__) . "/../../../Surface/View_Template/" . $viewFile . ".gregorio.html");
        if (is_array($RequestParam)) {
            $TemplateEngine = new GregorioTemplateEngine();
            $this->template = $TemplateEngine->GregorioCore($this->template, $RequestParam);
        } else {
            $TemplateEngine = new GregorioTemplateEngine();
            $this->template = $TemplateEngine->GregorioCore($this->template);
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

    public function SpecialMix($viewFile, $param = array()){
        $this->template = file_get_contents(dirname(__FILE__)."/../SpecialFile/".$viewFile.".gregorio.html");
        if(is_array($param)){
            $TemplateEngine = new GregorioTemplateEngine();
            $this->template = $TemplateEngine->GregorioCore($this->template, $param);
        }
        return $this;
    }

    public function Go()
    {
        return $this->template;
    }
}