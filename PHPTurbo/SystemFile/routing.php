<?php
function RouteGetter(){
    $RouteSet = [
        // ここにルーティングを記述してください。
        ['*', '/', function(){ Viewer('congratulation'); }],
        ['*', '/contexam/', function($params){ Viewer('ControllerTransmission>>PHPTurboController', $params); }],
        ['*', '/auth/:action', function($params){ Viewer('ControllerTransmission>>AuthController', $params["action"]); }],
        ['*', '/register/:action', function($params){ Viewer('ControllerTransmission>>RegisterController', $params["action"]); }],
        ['*', '/mailer/:action', function($params){ Viewer('ControllerTransmission>>MailSendController', $params["action"]); }],
        ['*', '/delete/:action', function($params){ Viewer('ControllerTransmission>>AccountDelController', $params["action"]); }],
        //ここから下の変更は推奨しません。
        ['*', '/css/systemstyle/', function(){ ResourceFile('systemstyle', "css"); }],
        ['*', '/phpturbo/administrator/', function(){ Viewer('ControllerTransmission>>TurboGearController'); }],
        ['*', '/phpturbo/lemonadeguardian/', function(){ Viewer('ControllerTransmission>>LemonadeController'); }],
        ['*', '404', function(){ SpecialFile('FtF'); }]
    ];
    return $RouteSet;
}