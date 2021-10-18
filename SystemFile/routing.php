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
        ['*', '/accountsetting/', function(){ Viewer('AccountTemplate/TopPage'); }],
        ['*', '/accountnamechanger/', function(){ Viewer('AccountTemplate/NameAvater'); }],
        ['*', '/ahotest/', function(){ Viewer('e79a86e38195e38293e381afe381a9e3828ce3818fe38289e38184e381aee69982e99693e382aae3838ae3838be383bce38197e381a6e381bee38199e3818befbc9f'); }],
        //ここから下の変更は推奨しません。
        ['*', '/css/:filename', function($param){ ResourceFile($param["filename"], "css"); }],
        ['*', '/js/:filename', function($param){ ResourceFile($param["filename"], "js"); }],
        ['*', '/pict/:filename', function($param){ ResourceFile($param["filename"], "pict"); }],
        ['*', '/css/systemstyle/', function(){ ResourceFile('systemstyle', "css"); }],
        ['*', '/phpturbo/administrator/', function(){ Viewer('ControllerTransmission>>TurboGearController'); }],
        ['*', '/phpturbo/lemonadeguardian/', function(){ Viewer('ControllerTransmission>>LemonadeController'); }],
        ['*', '/Exception/', function(){ SpecialFile('CruiserException'); }],
        ['*', '404', function(){ SpecialFile('FtF'); }]
    ];
    return $RouteSet;
}