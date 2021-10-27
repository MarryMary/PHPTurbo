<?php
namespace TurboCore;
use Exception;
require dirname(__FILE__)."/../../../vendor/autoload.php";

class ErrorProcessor
{
    public function EchoError($data, $langpack, $exceptionMessage, $logdir, $getmess=Null)
    {
        $message = "";
        if($getmess != Null){
            $message = $getmess->getMessage();
        }
        if($data["ShowException"] == "Yes" or $data["Mode"] == "TestOnly"){
            require_once dirname(__FILE__)."/../Mixer/TurboMixer.php";
            $messageArray = [
                "err" => [
                    $langpack[$exceptionMessage].$message
                ]
            ];
            $exceptionView = new TurboMixer();
            $exceptionView->SpecialMix("CruiserException", $messageArray);
        }else{
            try{
                throw new Exception($langpack[$exceptionMessage].$message);
            }catch(Exception $e){
                $log = date("Y-m-d H:i:s")."Threw Exception：".$e->getMessage();
                file_put_contents($logdir."QueryException.txt", $log, FILE_APPEND);
            }
        }
    }
}