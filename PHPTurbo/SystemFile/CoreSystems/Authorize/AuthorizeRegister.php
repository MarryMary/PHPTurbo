<?php

class AuthorizeRegister
{
    public function UserRegister($email, $password, $username)
    {
        require_once dirname(__FILE__)."/../SystemFileReader/SysFileLoader.php";
        $systemfile = new SystemFileReader();
        $sysfile = $systemfile->SettingLoader();
        require_once dirname(__FILE__)."/../Database/DatabaseConnector.php";
        $authorizer = new DatabaseConnector();
        $userpict = $sysfile["DefaultPict"];
        $insertArray = [
            "email" => $email,
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "profilepict" => $userpict
        ];
        $authorizer->Initializer()->Insert("user", "Associative", $insertArray)->Run();
    }

    public function PreUserRegister($email){
        require_once dirname(__FILE__)."/../env/UniqueIDProcessor.php";
        $uuidprocessor = new UuidV4Factory();
        require_once dirname(__FILE__)."/../Database/DatabaseConnector.php";
        $authorizer = new DatabaseConnector();
        $insertArray = [
            "email" => $email,
            "uuid" => str_replace('-', '', $uuidprocessor->generate())
        ];
        $user = $authorizer->Initializer()->Insert("user", "Associative", $insertArray)->Run();
    }
}