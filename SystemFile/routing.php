<?php
function RouteGetter(){
    $RouteSet = [
        // ここにルーティングを記述してください。
        ['*', '/', function(){ Viewer('congratulation'); }],
        ['*', '/contexam/', function($params){ Viewer('ControllerTransmission>>PHPTurboController', $params); }],
        //ここから下の変更は推奨しません。
        ['*', '/css/systemstyle/', function(){ ResourceFile('systemstyle', "css"); }],
        ['*', '404', function(){ SpecialFile('FtF'); }]
    ];
    return $RouteSet;
}