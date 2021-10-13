<?php
function RouteGetter(){
    $RouteSet = array(
        // ここにルーティングを記述してください。
        array('*', '/', function(){ Viewer('congratulation'); }),
        array('*', '/css/systemstyle/', function(){ ResourceFile('systemstyle', "css"); }),
        array('*', '/contexam/', function(){ Viewer('ControllerTransmission>>PHPTurboController'); }),
        array('*', '404', function(){ SpecialFile('FtF'); })
    );
    return $RouteSet;
}