<?php
require 'vendor/autoload.php';

foreach($argv as $argument)
{
    if($argument == "Migration"){
        try{
            $envcreator = new TurboCore\EnvCreator();
            $envcreator->EnvInitializer()->Execution();
            $createFiles = glob($dir.'*.php');
            foreach($createFiles as $files)
            {
                require_once($files);
                Execution();
            }
            echo "Migration was successful.";
        }catch(Exception $e){
            echo "Migration was failed.error is this->".$e->getMessage();
        }
    }else if($argument == "CreateMigration"){
        $template = file_get_contents("SystemFile/CoreSystems/templates/dbtemplate.txt");
        $loader = new TurboCore\SystemFileReader();
        $uuid = $loader->GetUUID();
        file_put_contents("SystemFile/Model/".$uuid.".php", $template);
        echo "Controller Created in SystemFile/Model/".$uuid.".php";
    }else if($argument == "createController"){
        $template = file_get_contents("SystemFile/CoreSystems/templates/ControllerTemplate.txt");
        echo "ControllerName?>>";
        $userInput = trim(fgets(STDIN));
        $template = str_replace("{CONTROLLERNAME}", $userInput, $template);
        file_put_contents("SystemFile/Controller/".$userInput.".php", $template);
        echo "Controller Created in SystemFile/Controller/".$userInput.".php";
    }else if($argument == "MagicServer"){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            echo "\n StartUp Magic Server Listen->Port:80 DocumentRoot:reader/ \n";
            exec("php -S localhost:80 -t reader/");
        } else if(PHP_OS == "Linux" or PHP_OS == "NetBSD" or PHP_OS == "OpenBSD" or PHP_OS == "FreeBSD" or PHP_OS == "Darwin") {
            echo "\n StartUp Magic Server Listen->Port:80 DocumentRoot:reader/ \n";
            exec("sudo php -S localhost:80 -t reader/");
        }
    }else if($argument == "help"){
        if(file_exists("CommandHelp.txt")){
            echo file_get_contents("CommandHelp.txt");
        }
    }
}