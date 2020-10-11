<?php
$valid_extensions = ['xlsx'];

$path_save_file = 'import/';

$response = [
    'status' => 0,
    'msg' => 'No se cargo ningun archivo'
];

// Save data for generate summary absences
$summary_absences = FALSE;

// Validates dates range
if (key_exists('chSummaryAbsence', $_POST) && $_POST['chSummaryAbsence'] == "on") {

    $ds = DateTime::createFromFormat('d/m/Y', $_POST['dateStart']);
    $de = DateTime::createFromFormat('d/m/Y', $_POST['dateEnd']);

    if ((empty($_POST['dateStart']) && empty($_POST['dateEnd'])) || ($ds && $de)) {

        $summary_absences = [
            'dateStart' => $_POST['dateStart'],
            'dateEnd' => $_POST['dateEnd']
        ];
    } else {

        $response['msg'] = "Ocurrio un problema con el formato de las fechas para el resumen de inasistencias.<br> Por favor verifica que se cumpla el formato dd/mm/yyyy ;)";
        echo json_encode($response);
        exit();
    }

    // var_dump($summary_absences);die();
}

// var_dump($summary_absences);
// var_dump($_POST); die();

if ($_FILES['file']['error'] === 0) {
    // echo json_encode($_FILES['file']);

    $file_name = $_FILES['file']['name'];
    $tmp_path = $_FILES['file']['tmp_name'];

    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $new_path = strtolower($path_save_file . date('Ymdhis') . '_' . $file_name);

    if (in_array($extension, $valid_extensions)) {

        if (move_uploaded_file($tmp_path, $new_path)) {

            $opt_flags = '--apiweb';

            if ($summary_absences) {
                if (!empty($summary_absences['dateStart']) && !empty($summary_absences['dateEnd'])) {
                    $opt_flags .= ' --summary-absences="' . $summary_absences['dateStart'] . ',' . $summary_absences['dateEnd'] . '"';
                } else {
                    $opt_flags .= ' --summary-absences=""';
                }
            }

            var_dump($opt_flags); die();

            if (strtoupper(substr(PHP_OS, null, 3)) === 'WIN') {

                //	var_dump(dirname($_SERVER['DOCUMENT_ROOT']) . '\php\php.exe');	
                $output = shell_exec(dirname($_SERVER['DOCUMENT_ROOT']) . "\php\php.exe sph.php " . $opt_flags);
            } else {

                // Asumimos que es linux
                // Por ahora realizado para poder desarrollar en entorno linux y desplegar en windows.
                $output = shell_exec("php sph.php " . $opt_flags);
            }

            // var_dump($output);

            $decoded_output = (array) json_decode($output);

            // var_dump($decoded_output);die();

            if (key_exists('status', $decoded_output) && $decoded_output['status'] == 1) {

                $response = [
                    'status' => 1,
                    'localpath' => $decoded_output['localpath'],
                    'serverpath' => $_SERVER['HTTP_REFERER'] . $decoded_output['serverpath'],
                    'aditional_msg' => $decoded_output['aditional_msg']
                ];
            } else if (key_exists('status', $decoded_output) && $decoded_output['status'] == -1) {
                $response = [
                    'status' => 4,
                    'msg' => $decoded_output['aditional_msg']
                ];
            } else {

                $response = [
                    'status' => 4,
                    'msg' => 'Error en ejecucion de sph.php. Revisar log'
                ];
            }
        } else {

            $response = [
                'status' => 2,
                'msg' => 'Error: ocurrio un error al mover el archivo (move_uploaded_file...)'
            ];
        }
    } else {

        $response = [
            'status' => 3,
            'msg' => 'Extencion de archivo invalida (solo se permiten archivos .xlsx)'
        ];
    }
}

echo json_encode($response);
exit();
