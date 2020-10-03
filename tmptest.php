<?php
// Archivo ini deconfiguracion

$cfg_file = './config/sph.ini';

// Creamos array a partir del arhcivo ini
$parse_conf = parse_ini_file($cfg_file, TRUE, INI_SCANNER_TYPED);

// var_dump($parse_conf);die();
// Datos de prueba de post, fechas de feriados separadas por coma
$_POST['taFeriados'] = '14/01/2020, 02/01/2020';

$_POST['taUsers'] = '91,4,109';


// Modificamos datos de configuracion segun obtengamos por post
if (!empty($_POST['taFeriados'])) {

    //TODO: Actualmente solo graba feriados para el año en curso.
    // Modificar para agregar año seleccionable
    $current_year = date('Y');
    
    if (!key_exists('feriados', $parse_conf)) $parse_conf['feriados'] = [];

    if (!key_exists($current_year, $parse_conf['feriados'])) $parse_conf['feriados'][$current_year] = '';
    
    $dates = explode(',', trim($_POST['taFeriados']));

    $row = createRowDatesFeriados($dates);

    if($row) $parse_conf['feriados'][$current_year] = $row;
    
}


if (!empty($_POST['taUsers'])) {
    
    if (!key_exists('usuarios', $parse_conf)) $parse_conf['usuarios'] = [];
    if (!key_exists($current_year, $parse_conf['usuarios'])) $parse_conf['usuarios']['usuarios'] = '';
    
    $users = explode(',', trim($_POST['taUsers']));
    var_dump($users);

    $row = createRowUsers($users);

    if($row) $parse_conf['usuarios']['usuarios'] = $row;
    
}


saveConfigFile($parse_conf);


// Escribe un arhcivo de configuracion a partir de un array
function saveConfigFile($cfg_array)
{
    $new_config_file = './sphtest.ini';

    if (file_exists($new_config_file)){

        //copy($new_config_file, $new_config_file . '.bk');
        unlink($new_config_file);
    }

    // Recorremos el array, las primeras claves son secciones
    foreach ($cfg_array as $sname => $section) {

        file_put_contents($new_config_file, "[$sname]" . PHP_EOL, FILE_APPEND);

        // Recorremos una seccion por cada valor
        foreach ($section as $vname => $value) {

            switch(gettype($value)){
                case "string":
                    $value = "\"$value\"";
                break;
                case "integer":
                break;
                case "NULL":
                    $value = "NULL";
                break;
                case "boolean":
                    if($value === TRUE) $value = "TRUE";
                    if($value === FALSE) $value = "FALSE";
                break;
            }

            file_put_contents($new_config_file, $vname . ' = ' . $value  . PHP_EOL, FILE_APPEND);
        }
    }
}


function createRowUsers(array $users){

    foreach ($users as $key => $value) {
        
        if(gettype(intval($value)) != "integer"){
            return false;
        }
    }

    return implode(",", $users);
}


function createRowDatesFeriados(array $dates)
{
    $verify_dates = [];
    $row_cfg = "";

    //$current_year = date('Y');
    //$row_cfg = "$current_year = \"";

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
