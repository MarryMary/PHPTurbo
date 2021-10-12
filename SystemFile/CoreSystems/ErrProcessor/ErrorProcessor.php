<?php

class ErrorProcessor
{
    public function EchoError($data, $langpack, $exceptionMessage, $logdir, $getmess="")
    {
        $message = "";
        if($getmess != ""){
            $message = $getmess->getMessage();
        }
        if($data["ShowException"] == "Yes" or $data["Mode"] == "TestOnly"){
            throw new Exception($langpack[$exceptionMessage].$message);
        }else{
            try{
                throw new Exception($langpack[$exceptionMessage].$message);
            }catch(Exception $e){
                $log = date("Y-m-d H:i:s")."Threw Exception".$e->getMessage();
                file_put_contents($logdir."QueryException.txt", $log, FILE_APPEND);
            }
        }
    }
}