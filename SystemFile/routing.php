<?php
function RouteGetter(){
    $RouteSet = [
        // ここにルーティングを記述してください。
        ['*', '/', function(){ Viewer('congratulation'); }],
        ['*', '/contexam/', function($params){ Viewer('ControllerTransmission>>PHPTurboController', $params); }],
        ['*', '/auth/:action', function($params){ Viewer('ControllerTransmission>>AuthController', $params["action"]); }],
        ['*', '/register/:action', function($params){ Viewer('ControllerTransmission>>RegisterController', $params["action"]); }],
        ['*', '/delete/:action', function($params){ Viewer('ControllerTransmission>>AccountDelController', $params["action"]); }],
        //ここから下の変更は推奨しません。
        ['*', '/css/:filename', function($param){ ResourceFile($param["filename"], "css"); }],
        ['*', '/js/:filename', function($param){ ResourceFile($param["filename"], "js"); }],
        ['*', '/pict/:filename', function($param){ ResourceFile($param["filename"], "pict"); }],
        ['*', '/css/systemstyle/', function(){ ResourceFile('systemstyle', "css"); }],
        ['*', '/phpturbo/administrator/', function(){ Viewer('ControllerTransmission>>TurboGearController'); }],
        ['*', '/phpturbo/lemonadeguardian/', function(){ Viewer('ControllerTransmission>>LemonadeController'); }],
        ['*', '/Exception/', function(){ SpecialFile('CruiserException'); }],
        ['*', '/mailer/:action', function($params){ Viewer('ControllerTransmission>>MailSendController', $params["action"]); }],
        ['*', '404', function(){ SpecialFile('FtF'); }]
    ];
    return $RouteSet;
}