<?php
// Config path
define('CONFIG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/config/');

$response = [
    'status' => 1,
    'msg' => '',
    'data' => null
];



if ($_POST['code'] == 'load_config') {
    $cfg_array = getConfigFile();

    if($cfg_array){
        $response['data'] = $cfg_array;
    }else{
        $response['msg'] = 'No se pudo leer la configuracion. revisar el log';
    }

    echo json_encode($response);

    exit();
}



function getConfigFile($cfg_file = NULL)
{

    if ($cfg_file === NULL) {
        $cfg_file = CONFIG_PATH . 'sph.ini';
    }

    if (!file_exists($cfg_file)) {
        echo "WARNING: Confing file not exist" . PHP_EOL;
        return FALSE;
    }

    $cfg_array = parse_ini_file($cfg_file, TRUE, INI_SCANNER_TYPED);

    return $cfg_array;
}
