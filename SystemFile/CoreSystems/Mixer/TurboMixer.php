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

    public function BindRequest($Param)
    {
        $filePath = "";
        $DataSource = file_get_contents(dirname(__FILE__)."/../../Settings/SysEnv.json");
        $DataSource = mb_convert_encoding($DataSource, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $SysData = json_decode($DataSource,true);
        $lang = file_get_contents(dirname(__FILE__)."/../../Settings/".$SysData['LangPack']);
        $lang = mb_convert_encoding($lang, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $langPack = json_decode($lang, true);
        $envinfo = array();
        if(!$this->isSpecialPage){
            $filepath = dirname(__FILE__)."/../../../Surface/View_Template/";
            $envInfo = [
                "PHPVERSION" => phpversion('tidy'),
                "PHPTURBOVERSION" => $SysData["SystemVersion"],
                "PHPTURBOCODENAME" => $SysData["CoreName"],
                "PLATFORMINFO" => PHP_OS,
                "ISSPECIALPAGE" => "NO"
            ];
        }else{
            $filepath = dirname(__FILE__)."/../SpecialFile/";
            $envInfo = [
                "PHPVERSION" => phpversion('tidy'),
                "PHPTURBOVERSION" => $SysData["SystemVersion"],
                "PHPTURBOCODENAME" => $SysData["CoreName"],
                "PLATFORMINFO" => PHP_OS,
                "ISSPECIALPAGE" => "YES"
            ];
        }
        if(is_array($Param)){
            foreach($Param as $key => $value){
                $this->template = str_replace("{ ".$key." }", $value, $this->template);
            }
            $tableTemplate = file_get_contents(dirname(__FILE__)."/../SpecialFile/DebugTableTemplate.html");
            foreach($envInfo as $key => $value){
                $tableTemplate = str_replace("{ ".$key." }", $value, $this->template);
            }
            $this->template = str_replace("{DEBUGTABLE}", $tableTemplate, $this->template);
            return $this;
        }else{
            require_once dirname(__FILE__)."/../ErrProcessor/ErrorProcessor.php";
            $CreateException = new ErrorProcessor();
            $CreateException->EchoError($SysData, $langPack, "QueryCantUseBeforeInitialize", dirname(__FILE__)."/../../Logs/");
            return False;
        }
    }

    public function Go()
    {
        return $this->template;
    }
}