<?php

// Config path
define('CONFIG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/config/');

$response = [
    'status' => 1,
    'msg' => ''
];

$cfg_array = getConfigFile();

$current_year = date('Y');

// Modificamos datos de configuracion segun obtengamos por post
if (!empty($_POST['taFeriados'])) {

    //TODO: Actualmente solo graba feriados para el año en curso.
    // Modificar para agregar año seleccionable
   

    if (!key_exists('feriados', $cfg_array)) $cfg_array['feriados'] = [];

    if (!key_exists($current_year, $cfg_array['feriados'])) $cfg_array['feriados'][$current_year] = '';

    $dates = explode(',', trim($_POST['taFeriados']));

    $row = createRowDatesFeriados($dates);

    if ($row) {
        $cfg_array['feriados'][$current_year] = $row;
    } else {

        $response['status'] = 0;

        $response['msg'] = '<hr> # Ups! parece que ocurrio un error al procesar las fechas de feriados. Por favor revisa los datos cargados <br> 
        recorda que deben ser fechas validas con el formado dd/mm/yyyy y deben estar separadas por ,(coma) <br>
        <strong>Nota: no debe quedar una coma al final ni al principio.</strong>';

        //echo json_encode($response);
        //exit();
    }
}

if (!empty($_POST['taEmployees'])) {

    if (!key_exists('empleados', $cfg_array)) $cfg_array['empleados'] = [];
    if (!key_exists($current_year, $cfg_array['empleados'])) $cfg_array['empleados']['empleados'] = '';

    $employees = explode(',', trim($_POST['taEmployees']));

    $row = createRowEmployees($employees);
    
    if ($row) {
        $cfg_array['empleados']['empleados'] = $row;
    } else {

        $response['status'] = 0;

        $response['msg'] .= '<hr> # Ups! parece que ocurrio un error al procesar los empleados. Por favor revisa los datos cargados. <br> 
        recorda que deben ser numeros unicamente y deben estar separados por ,(coma) <br>
        <strong>Nota: no debe quedar una coma al final ni al principio.</strong>';

        // echo json_encode($response);
        // exit();
    }
}




if ($response['status'] == 1) {
    saveConfigFile($cfg_array);
    $response['msg'] = 'Configuracion guardada';
}

// $response['status'] = 1;

echo json_encode($response);
exit();


/* ######################################################## */


// Escribe un arhcivo de configuracion a partir de un array
function saveConfigFile($cfg_array)
{
    // Default config ./config/sph.ini
    $new_config_file = CONFIG_PATH . 'sph.ini';

    if (file_exists($new_config_file)) {

        copy($new_config_file, $new_config_file . '.bk');
        unlink($new_config_file);
    }

    // Recorremos el array, las primeras claves son secciones
    foreach ($cfg_array as $sname => $section) {

        file_put_contents($new_config_file, "[$sname]" . PHP_EOL, FILE_APPEND);

        // Recorremos una seccion por cada valor
        foreach ($section as $vname => $value) {

            switch (gettype($value)) {
                case "string":
                    $value = "\"$value\"";
                    break;
                case "integer":
                    break;
                case "NULL":
                    $value = "NULL";
                    break;
                case "boolean":
                    if ($value === TRUE) $value = "TRUE";
                    if ($value === FALSE) $value = "FALSE";
                    break;
            }

            file_put_contents($new_config_file, $vname . ' = ' . $value  . PHP_EOL, FILE_APPEND);
        }
    }
}


function createRowEmployees(array $employees)
{

    foreach ($employees as $key => $value) {
        $value = (integer) $value;

        if (boolval($value) === false) {
            return false;
        }
    }

    return implode(",", $employees);
}


function createRowDatesFeriados(array $dates)
{
    $verify_dates = [];
    $row_cfg = "";

    foreach ($dates as $key => $date) {

        $date = trim($date);

        if (validateDate($date)) {
            $verify_dates[] = $date;
        } else {
            return false;
        }
    }

    $row_cfg .= implode(",", $verify_dates);

    return $row_cfg;
}


function validateDate($date, $format = 'd/m/Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
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
