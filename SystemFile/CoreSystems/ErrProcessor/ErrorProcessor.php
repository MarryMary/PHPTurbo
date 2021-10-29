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
            $messageArray = [
                "err" => [
                    $langpack[$exceptionMessage].$message
                ]
            ];
            $exceptionView = new TurboMixer();
            echo $exceptionView->SpecialMix("PreludeOverReaderReport", $messageArray)->Go();
        }else{
            try{
                throw new Exception($langpack[$exceptionMessage].$message);
            }catch(Exception $e){
                $log = date("Y-m-d H:i:s")."Threw Exception：".$e->getMessage();
                file_put_contents($logdir."Exception.txt", $log, FILE_APPEND);
            }
        }
    }
}