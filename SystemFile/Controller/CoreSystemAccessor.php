<?php
namespace TurboCore;
require dirname(__FILE__)."/../../vendor/autoload.php";

class CoreSystemAccessor{

    public function Auth(){
        require_once dirname(__FILE__)."/../CoreSystems/Authorize/Authorizer.php";
        return new Authorizer();
    }

    public function AuthRegi(){
        require_once dirname(__FILE__)."/../CoreSystems/Authorize/AuthorizeRegister.php";
        return new AuthorizeRegister();
    }

    public function DBConnection(){
        require_once dirname(__FILE__)."/../CoreSystems/Database/DatabaseConnector.php";
        return new DatabaseConnector();
    }

    public function UUIDCreator(){
        require_once dirname(__FILE__)."/../CoreSystems/env/UniqueIDProcessor.php";
        return new UUIDCreator();
    }
}