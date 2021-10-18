<?php
/*
 * PHPTurbo Template Engine "PHP VTEC ENGINE"
 * Engine Version 0.0.1 beta
 * 2021/10/15 Mary
 */

class PHPVtec
{
    private $codedump = False;
    private $code = Null;
    private $prepared = Null;
    private $codecount = 0;
    private $param;
    public function VTECTemplateEngine($template, $param = array()){
        $template = explode("\n", $template);
        $this->param = $param;
        $this->TurboReader($template);
        return $this->prepared;
    }

    private function TurboReader($templates, $params = array()){
        foreach($this->param as $key => $value) {
            $$key = $value;
        }
        foreach($templates as $template){
            if(substr(ltrim($template), 0, 1) === '@'){
                if(substr_count($template, "@") == 1){
                    if(strpos($template,'authonly') !== false){
                        require_once dirname(__FILE__)."/../SystemFileReader/SysFileLoader.php";
                        $loader = new SystemFileReader();
                        $settings = $loader->SettingLoader();
                        $auth = new Authorizer();
                        if($auth->IsAuthorized()){
                            continue;
                        }else {
                            header("Location: ".$settings["notAuthRedirect"]);
                            break;
                        }
                    }else{
                        $this->code .= str_replace('@', '', ltrim($template));
                        $this->codecount += 1;
                        $this->codedump = True;
                    }
                }else if(substr_count($template, "@") == 2){
                    $this->code .= str_replace('@', '', ltrim($template));
                    $this->codedump = True;
                }else if(substr_count($template, "@") == 3){
                    $this->codedump = True;
                    $this->codecount -= 1;
                    if($this->codecount == 0){
                        $this->code .= str_replace('@', '', ltrim($template));
                        $result = eval($this->code);
                        $this->prepared .= $result;
                        $this->codedump = False;
                    }else{
                        $this->code .= str_replace('@', '', ltrim($template));
                    }
                }
            }else{
                if($this->codedump){
                    if(strpos($template,'"') !== false){
                        $template = str_replace('{{', '', $template);
                        $template = str_replace('}}', '', $template);
                        $this->code .= "return '".$template."';";
                    }else{
                        $template = str_replace('{{', '', $template);
                        $template = str_replace('}}', '', $template);
                        $this->code .= 'return "'.$template.'";';
                    }
                }
                else{
                    if(strpos($template,'{{') !== false || strpos($template,'}}') !== false || strpos($template,'$') !== false){
                        $template = str_replace('{{', '', $template);
                        $template = str_replace('}}', '', $template);
                        $binder = strip_tags($template);
                        $template = str_replace($binder, $$binder, $template);
                        $this->prepared .= $template;
                    }else{
                        $this->prepared .= $template;
                    }
                }
            }
        }
        return True;
    }

}