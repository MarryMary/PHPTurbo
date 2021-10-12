<?php
function RouteGetter(){
    $RouteSet = array(
        // ここにルーティングを記述してください。
        array('*', '/', function(){ Viewer('congratulation'); }),
        array('GET', '/contexam/', function(){ Viewer('ControllerTransmission>>PHPTurboController'); }),
        array('*', '404', function(){ Escape('ページが見つかりません。'); })
    );
    return $RouteSet;
}