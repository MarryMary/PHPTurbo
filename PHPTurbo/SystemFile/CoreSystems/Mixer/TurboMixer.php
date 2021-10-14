<?php

class TurboMixer
{
    private $template = Null;
    private $isSpecialPage = False;
    public function SurfaceMix($viewFile, $RequestParam)
    {
        $this->template = file_get_contents(dirname(__FILE__)."/../../../Surface/View_Template/".$viewFile.".html");
        return $this;
    }

    public function ResourceMix($viewFile, $type){
        if($type == "css"){
            header('Content-Type: text/css;', 'charset=utf-8');
            $this->template = file_get_contents(dirname(__FILE__)."/../../../Surface/View_Template/css/".$viewFile.".css");
        }else if($type == "js"){
            $this->template = file_get_contents(dirname(__FILE__)."/../../../Surface/View_Template/js/".$viewFile.".js");
        }
        return $this;
    }

    public function SpecialMix($viewFile){
        $this->template = file_get_contents(dirname(__FILE__)."/../SpecialFile/".$viewFile.".html");
        return $this;
    }

    public function BindRequest($Param, $file)
    {
        $prepared = "";
        require_once dirname(__FILE__)."/../SystemFileReader/SysFileLoader.php";
        $loader = new SysFileLoader();
        $SysData = $loader->SettingLoader();
        $langPack = $loader->LangPackLoader();
        $envinfo = array();
        $template = file_get_contents($file);
        $template = explode("\n", $template);
        $mode = "";
        $commands = array();
        foreach($template as $temp){
            if(strpos( $temp, "pt:" ) !== false){
                $command = str_replace('pt:', '', $temp);
                if(strpos($command, "foreach") !== false){
                    $command = str_replace('foreach(', '', $command);
                    $command = str_replace(')', '', $command);
                    $command = explode("as", $command);
                    foreach($command as $com){
                        $commands += str_replace(' ', '', $com);
                    }
                    $commands[0] = str_replace('$', '', $commands[0]);
                }
            }if(strpos($temp, "{") !== false){
                $variable = str_replace("{", "", $temp);
                $variable = str_replace("}", "", $variable);
                if($variable == $command[1]){
                    //TODO
                }
            }
        }
    }

    public function Go()
    {
        return $this->template;
    }
}