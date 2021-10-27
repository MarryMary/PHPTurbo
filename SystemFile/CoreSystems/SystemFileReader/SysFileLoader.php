<?php
namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";

class SystemFileReader{
    private $setting;
    private $langpack;
    private $memory;
    public function __construct(){
        $DataSource = file_get_contents(dirname(__FILE__)."/../../Settings/SysEnv.json");
        $DataSource = mb_convert_encoding($DataSource, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $this->setting = json_decode($DataSource,true);
        $lang = file_get_contents(dirname(__FILE__)."/../../Settings/".$this->setting['LangPack']);
        $lang = mb_convert_encoding($lang, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $this->langpack = json_decode($lang, true); 
    }
    
    public function SettingLoader(){
        return $this->setting;
    }

    public function LangPackLoader(){
        return $this->langpack;
    }

    public function MemorialLoader(){
        return $this->memory;
    }

    public function GetUUID(){
        $uuidprocessor = new UUIDCreator();
        return $uuidprocessor -> generate();
    }
}