<?php


class EnvCreator
{
    private $config_json = "../../Settings/SysEnv.json";
    private $config;
    private $initialized = False;
    private $dbc;

    public function EnvInitializer(){
        $this->config = json_decode(mb_convert_encoding(file_get_contents(dirname(__FILE__)."/".$this->config_json), 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);
        $this->initialized = True;
        return $this;
    }

    public function Reset(){
        $this->EnvInitializer()->TableRollback()->DBRollback()->DBCreator()->TableCreator();
        return $this;
    }

    public function Execution(){
        $this->DBCreator()->TableCreator();
        return $this;
    }

    public function Rollback(){
        $this->EnvInitializer()->TableRollback()->DBRollback();
        return $this;
    }

    protected function DBCreator()
    {
        require_once dirname(__FILE__)."/"."../Database/DatabaseConnector.php";
        $dbc = new DatabaseConnector();
        $dbc->Initializer()->Self("CREATE DATABASE IF NOT EXISTS {$this->config["DatabaseName"]}")->Run();
        return $this;
    }

    protected function TableCreator()
    {
        require_once dirname(__FILE__)."/"."../Database/DatabaseConnector.php";
        $this->dbc = new DatabaseConnector();
        if($this->config["DatabaseExistsForceCreate"] == "Yes"){
            $this->dbc->Initializer()->Self("DROP TABLE IF EXISTS user")->Run();
        }
        $this->dbc->Initializer()->Self("CREATE TABLE IF NOT EXISTS preuser(email VARCHAR(256) NOT NULL, token VARCHAR(40) NOT NULL PRIMARY KEY)")->Run();
        $this->dbc->Initializer()->Self("CREATE TABLE IF NOT EXISTS user(id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, email VARCHAR(256) NOT NULL, username VARCHAR(256) NOT NULL, profilepict VARCHAR(500) NOT NULL)")->Run();
        return $this;
    }

    protected function TableRollback()
    {
        $this->dbc->Initializer()->Self("DROP TABLE IF EXISTS user")->Run();
        $this->dbc->Initializer()->Self("DROP TABLE IF EXISTS preuser")->Run();
        return $this;
    }

    protected function DBRollback()
    {
        $this->dbc->Initializer()->Self("DROP DATABASE IF EXISTS {$this->config["DatabaseName"]}")->Run();
        return $this;
    }
}