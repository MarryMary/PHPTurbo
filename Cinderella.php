<?php

$dir = "SystemFile/CoreSystems/env/";
foreach($argv as $argument)
{
    if($argument == "table_make"){
        while(true)
        {
            $i = 0;
            if(!file_exists($dir."EngineerTable".$i.".php")) {
                if(file_exists("template.php")) {
                    $template = file_get_contents('template.php');
                    file_put_contents($dir."EngineerTable".$i.".php", $template);
                    echo $dir."EngineerTable".$i.".php　へテンプレートを作成しました。";
                }else{
                    echo "データベース作成コード用テンプレートファイルが見つかりません。";
                }
            }
        }
    }else if($argument == "table_create"){
        $createFiles = glob($dir.'*.php');
        foreach($createFiles as $files)
        {
            if($files != $dir."envcreator.php"){
                require_once($files);
                execution();
            }
        }
    }else if($argument == "reset"){
        $resetFiles = glob($dir.'*.php');
        foreach ($resetFiles as $file)
        {
            if($file == $dir."envcreator.php"){
                require_once($file);
                $envremake = new EnvCreator();
                $envremake->EnvInitializer()->Reset();
            }else {
                require_once($file);
                RollBack();
            }
        }
    }else if($argument == "SysRollback"){
        require_once($dir."envcreator.php");
        $envremake = new EnvCreator();
        $envremake->EnvInitializer()->Reset();
    }else if($argument == "SysClear"){
        try {
            require_once($dir . "envcreator.php");
            $envremake = new EnvCreator();
            $envremake->EnvInitializer()->Reset();
            echo "システムクリアに成功しました。";
        }catch (Exception $e){
            echo "システムクリアに失敗しました。内容は次の通りです：{$e}";
        }
    }else if($argument == "Closed"){
        $closeFiles = glob('./*');
        echo "この操作を実行すると、カレントディレクトリの内容がすべて削除され、システムが設定した環境変数等を全てプロジェクト開始前まで戻します。。Closedコマンドを実行しますか？(y/n)";
        $userSelect = trim(fgets(STDIN));
        if(strtolower($userSelect) == "y" or strtolower($userSelect) == "yes"){
            echo "最終確認です。本当に実行しますか？(y/n)";
            if(strtolower($userSelect) == "y" or strtolower($userSelect) == "yes"){
                foreach($closeFiles as $files){
                    unlink($files);
                }
                echo "プロジェクトをクローズしました。";
            }
        }else{
            exit();
        }
    }else if($argument == "createController"){
        $template = file_get_contents("ControllerTemplate.txt");
        echo "ControllerName?>>";
        $userInput = trim(fgets(STDIN));
        $template = str_replace("{CONTROLLERNAME}", $userInput, $template);
        file_put_contents("SystemFile/Controller/".$userInput.".php", $template);
        echo "Controller Created in SystemFile/Controller/".$userInput.".php";
    }else if($argument == "StartUp"){
        require_once($dir."envcreator.php");
        $envcreator = new EnvCreator();
        $envcreator->EnvInitializer()->Execution();
        echo "テーブルの作成に成功しました。";
    }else if($argument == "help"){
        if(file_exists("CommandHelp.txt")){
            echo file_get_contents("CommandHelp.txt");
        }
    }else if($argument == "createController"){

    }
}