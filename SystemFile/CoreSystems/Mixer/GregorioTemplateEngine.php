<?php
/*
 * PHPTurbo Template Engine "Gregorio Template ENGINE"
 * Engine Version 0.0.1 beta
 * 2021/10/15 Mary
 */

namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";

class GregorioTemplateEngine
{
    private $codedump = False;
    private $code = Null;
    private $prepared = Null;
    private $codecount = 0;
    private $param;
    public function GregorioCore($template, $param = array()){
        $template = explode("\n", $template);
        $this->param = $param;
        $this->TurboReader($template);
        return $this->prepared;
    }

    private function TurboReader($templates){
        foreach($this->param as $key => $value) {
            $$key = $value;
        }
        foreach($templates as $template){
            if(substr(ltrim($template), 0, 1) === '@'){
                if(substr_count($template, "@") == 1){
                    if(strpos($template,'authonly') !== false){
                        $loader = new SystemFileReader();
                        $settings = $loader->SettingLoader();
                        $auth = new Authorizer();
                        if($auth->IsAuthorized()){
                            continue;
                        }else {
                            header("Location: ".$settings["notAuthRedirect"]);
                            break;
                        }
                    }else if(strpos($template,'csrfGuard') !== false){
                        $tokenCreator = new UUIDCreator();
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['csrfToken'] = str_replace('-', '', $tokenCreator->generate());
                        $template = '<input type="hidden" name="csrfPosting" value="'.$_SESSION['csrfToken'].'">';
                        if($this->codedump){
                            $this->code .= "$this->prepared .= '".$template.'";';
                        }else{
                            $this->prepared .= $template;
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
                        eval($this->code);
                        $this->codedump = False;
                    }else{
                        $this->code .= str_replace('@', '', ltrim($template));
                    }
                }
            }else{
                if($this->codedump){
                    if(strpos($template,'"') !== false){
                        $pattern = '/\{{.+?\}}/';
                        preg_match_all($pattern, $template, $variable);
                        foreach($variable[0] as $val){
                            $binder = str_replace('{{', '', $val);
                            $binder = str_replace('}}', '', $binder);
                            $template = str_replace($val, "'.".$binder.".'", $template);
                        }
                        $this->code .= '$this->prepared .= \''.$template.'\';';
                    }else{
                        $pattern = '/\{{.+?\}}/';
                        preg_match_all($pattern, $template, $variable);
                        foreach($variable[0] as $val){
                            $binder = str_replace('{{', '', $val);
                            $binder = str_replace('}}', '', $binder);
                            $template = str_replace($val, '".'.$binder.'."', $template);
                        }
                        $this->code .= '$this->prepared .= "'.$template.'";';
                    }
                }else{
                    if(strpos($template,'{{') !== false || strpos($template,'}}') !== false || strpos($template,'$') !== false){
                        $pattern = '/\{{.+?\}}/';
                        preg_match_all($pattern, $template, $variable);
                        foreach($variable[0] as $val){
                            $binder = str_replace('{{', '', $val);
                            $binder = str_replace('}}', '', $binder);
                            $binder = str_replace('$', '', $binder);
                            $template = str_replace($val, $$binder, $template);
                        }
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