<?php
namespace TurboCore;
require dirname(__FILE__)."/../../../vendor/autoload.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
class Authorizer
{
    public function UserAuthorize($usermail, $password)
    {
        $authorizer = new DatabaseConnector();
        $user = $authorizer->Initializer()->Select(["*"], "user")->Where("email", $usermail, "eq")->Run();
        $result = $user->MFetch();
        if($result !== false and password_verify($password, $result["password"])){
            $_SESSION["loggedin"] = true;
            $_SESSION["user"] = $user["id"];
            return true;
        }else{
            return "ユーザー名またはパスワードが違います。";
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
        $authorizer = new DatabaseConnector();
        $user = $authorizer->Initializer()->Select("*", "user")->Where("id", $id, "eq")->Run()->MFetch();
        return $user;
    }
}