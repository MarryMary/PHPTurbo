<?php
require dirname(__FILE__)."/../../vendor/autoload.php";

class PHPTurboController
{
    private $para;
    public function Controller(){
        $systemfile = new TurboCore\SystemFileReader();
        $sysfile = $systemfile->SettingLoader();
        $userpict = $sysfile["DefaultPict"];
        $pict = str_split($userpict);
        $readCommand = False;
        $readValue = False;
        $escaped = False;
        $commandDump = "";
        $valueDump = "";
        foreach($pict as $p){
            if($escaped === True){
                $escaped = False;
            }else{
                if($readCommand === True && $p == ">"){
                    $readCommand = False;
                    $variable = explode(",", $valueDump);
                    if(is_numeric($variable) && count($variable) == 2){
                        $variable[0] = (int)$variable[0];
                        $variable[1] = (int)$variable[1];
                    }
                    if($commandDump == "rand"){
                        var_dump($userpict.rand($variable[0], $variable[1]));
                        $userpict = $userpict.rand($variable[0], $variable[1]);
                    }
                }else if($readCommand === True && $p == "("){
                    $readValue = True;
                    $readCommand = False;
                }else if($readValue === True && $p == ")"){
                    $readValue = False;
                    $readCommand = True;
                }else if($readValue == True){
                    $valueDump .= $p;
                }else if($readCommand === True){
                    $commandDump .= $p;
                }else if($p == "<"){
                    $readCommand = True;
                }else if($p == "\\"){
                    echo "f";
                    $escaped = True;
                }
            }
        }
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