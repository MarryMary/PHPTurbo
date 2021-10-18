<?php
session_start();
class Authorizer
{
    public function UserAuthorize($usermail, $password)
    {
        require_once dirname(__FILE__)."/../Database/DatabaseConnector.php";
        $authorizer = new DatabaseConnector();
        $user = $authorizer->Initializer()->Select(["*"], "user")->Where("email", $usermail, "eq")->Run();
        $result = $user->MFetch();
        if($result !== false and password_verify($password, $result["password"])){
            $_SESSION["loggedin"] = true;
            $_SESSION["user"] = $user["id"];
            return true;
        }else{
            return false;
        }
    }

    public function IsAuthorized(){
        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
            return true;
        }else{
            return false;
        }
    }

    public function UserGet($id){
        require_once dirname(__FILE__)."/../Database/DatabaseConnector.php";
        $authorizer = new DatabaseConnector();
        $user = $authorizer->Initializer()->Select("*", "user")->Where("id", $id, "eq")->Run()->fetch();
        return $user;
    }
}