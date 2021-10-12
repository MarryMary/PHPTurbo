<?php
function path_info() {
    $DataSource = file_get_contents(dirname(__FILE__)."/../SystemFile/Settings/SysEnv.json");
    $DataSource = mb_convert_encoding($DataSource, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $SysData = json_decode($DataSource,true);
    if (isset($_SERVER['PATH_INFO'])) {
        return $_SERVER['PATH_INFO'];
    }
    else if (isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
        $url = parse_url($SysData["AppURL"] . $_SERVER['REQUEST_URI']);
        if ($url === false) return false;
        return '/' . ltrim(substr($url['path'], strlen(dirname($_SERVER['SCRIPT_NAME']))), '/');
    }
    return false;
}

function path_route(array $map, $method = null, $path = null) {
    $method = strtoupper(is_null($method) ? $_SERVER['REQUEST_METHOD'] : $method);
    if (is_null($path)) $path = path_info();

    $code = '404';
    $codeMap = array();
    foreach ($map as $item) {
        if (count($item) < 3) continue;
        $sameMethod = ($item[0] == '*' || strtoupper($item[0]) == $method);

        if (preg_match('#\A[0-9]{3}\z#', $item[1])) {
            if ($sameMethod) $codeMap[$item[1]] = $item[2];
            continue;
        }
        $pattern = '#\A' . preg_replace('#:([a-zA-Z0-9_]+)#', '(?<$1>[^/]+?)', $item[1]) . '\z#';
        if (!preg_match($pattern, $path, $matches)) continue;
        if (!$sameMethod) {
            $code = '405'; continue;
        }
        call_user_func($item[2], $matches);
        return;
    }

    $statusMap = array('404' => 'Not Found', '405' => 'Method Not Allowed');
    header("HTTP/1.1 {$code} {$statusMap[$code]}");
    if (isset($codeMap[$code])) call_user_func($codeMap[$code], array($path));
    else echo $code;
}
function Escape($value, $encoding = 'UTF-8') { echo htmlspecialchars($value, ENT_QUOTES, $encoding); }

function Viewer($value, $encoding = 'UTF-8') {
    require dirname(__FILE__)."/../SystemFile/CoreSystems/Mixer/TurboMixer.php";
    $juicer = new TurboMixer();
    if(strpos( $value, "ControllerTransmission>>" ) === false) {
        echo $juicer->SurfaceMix($value, $_GET)->Go();
    }else{
        $value = str_replace('ControllerTransmission>>', '', $value);
        require dirname(__FILE__)."/../SystemFIle/Controller/".$value.".php";
        $userController = new $value;
        $template_result = $userController->Controller();
        echo $template_result;
    }
}

require dirname(__FILE__)."/../SystemFile/routing.php";
path_route(RouteGetter());